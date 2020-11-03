<?php

namespace DH\ArtisDiscountAsPromoHotfixPlugin\Subscriber;

use App\Entity\Channel\ChannelPricing;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvents;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Sylius\Component\Core\Model\ProductVariantInterface as ModelProductVariantInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

class OrderDiscountHotfixEventSubscriber implements EventSubscriberInterface
{
    /** @var RepositoryInterface */
    private $pricesRepo;

    /** @var RepositoryInterface */
    private $variantsRepo;

    /** @var RequestStack */
    private $requestStack;

    public function __construct(EntityManagerInterface $entityManager, RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
        $this->pricesRepo = $entityManager->getRepository(ChannelPricing::class);
        $this->variantsRepo = $entityManager->getRepository(ModelProductVariantInterface::class);
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SUBMIT => 'preSubmit',
        ];
    }

    public function preSubmit($event)
    {
        $orderData = $event->getData();

        $orderData = $this->redistributeOrderDiscount($orderData);
        $event->setData($orderData);
    }

    protected function getPriceByCodeAndChannel($code, $channel)
    {
        if (!$variant = $this->variantsRepo->findOneBy(['code' => $code])) {
            throw new Exception('Product variant not found. code: ' . $code);
        } //split to avoid joins

        if (!$price = $this->pricesRepo->findOneBy(['productVariant' => $variant->getId(), 'channelCode' => $channel])) {
            throw new Exception(sprintf('Price not found for channel %s and variant %s.', $channel, $code));
        }

        return $price->getPrice();
    }

    protected function getCurrentChannel()
    {
        /** @var Request */
        $request = $this->requestStack->getCurrentRequest();
        $route = $request->attributes->get('_route_params');
        return $route['channelCode'];
    }

    protected function getAdjustmentsSum($data)
    {
        $sum = 0;
        if (isset($data['adjustments']) && is_array($data['adjustments'])) {
            foreach ($data['adjustments'] as $adjustmentRaw) {
                $sum += intval($adjustmentRaw['amount']);
            }
        }

        return $sum;
    }

    protected function redistributeOrderDiscount($data)
    {
        $promosSum = $this->getAdjustmentsSum($data);

        if ($promosSum <= 0) {
            return $data;
        }

        $channel = $this->getCurrentChannel();

        $total = 0;
        $priceByCodeAndPosition = [];
        foreach ($data['items'] as $i => $item) {
            $price = $this->getPriceByCodeAndChannel($item['variant'], $channel);
            $priceAfterQuantity = $price * $item['quantity'];
            $localAdjustments = $this->getAdjustmentsSum($item);

            //price after existing adjustments
            $priceAfterLocalDiscounts = $priceAfterQuantity - $localAdjustments;
            $priceByCodeAndPosition[$item['variant'] . '_' . $i] = $priceAfterLocalDiscounts;
            $total += $priceAfterLocalDiscounts;
        }

        foreach ($data['items'] as $i => &$item) {
            $code = $item['variant'];
            if (!isset($item['adjustments'])) {
                $item['adjustments'] = [];
            }

            $item['adjustments'][] = [
                'amount' => intval(($priceByCodeAndPosition[$code . '_' . $i] / $total) * $promosSum)
            ];
        }

        //remove order adjustments
        unset($data['adjustments']);
        return $data;
    }
}
