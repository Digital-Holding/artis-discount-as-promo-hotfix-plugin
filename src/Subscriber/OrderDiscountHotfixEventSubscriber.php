<?php

namespace DH\ArtisDiscountAsPromoHotfixPlugin\Subscriber;

use App\Entity\Product\ProductVariantInterface;
use App\Entity\Product\ProductVariantSpecificationItemInterface;
use App\Entity\Product\ProductVariantSpecificationItemValue;
use App\Entity\Product\ProductVariantSpecificationItemValueInterface;
use App\Entity\Product\ProductVariantSpecificationItemValues;
use App\Entity\Product\ProductVariantSpecificationItemValuesInterface;
use App\Factory\Product\DHVariantSpecificationItemValueViewFactoryInterface;
use DH\Artis\Product\Specification\SpecificationItem\SpecificationItemValueResolverInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use DH\Artis\Product\Specification\SpecificationItem\SpecificationItemValueType;
use Doctrine\ORM\EntityManagerInterface;
use PhpSpec\Specification;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\Promotion;
use Sylius\Component\Promotion\Model\PromotionAction;

class OrderDiscountHotfixEventSubscriber implements EventSubscriberInterface
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public static function getSubscribedEvents(): array
    {
        return [
            FormEvents::PRE_SET_DATA => 'preSetData',
        ];
    }

    public function preSetData(FormEvent $event)
    {
        $data = $event->getData();
        /** @var OrderInterface */
        $order = $event->getForm()->getNormData();

        $promosSum = 0;
        if (isset($data['adjustments']) && is_array($data['adjustments'])) {
            foreach($data['adjustmetns'] as $adjustmentRaw) {
                $promosSum += intval($adjustmentRaw['amount']);
            }
        }

        if ($promosSum)
        {
            $promotion = new Promotion();
            $action = new PromotionAction();
            $action->setType('order_fixed_discount');
            $action->setConfiguration([
                $order->getChannel()->getCode() => [
                    'amount' => $promosSum
                ]
            ]);
            $order->addPromotion($promotion);
        }

        $event->setData($data);
    }
}
