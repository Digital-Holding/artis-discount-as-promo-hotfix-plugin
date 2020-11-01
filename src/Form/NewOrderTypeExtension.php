<?php

declare(strict_types=1);

namespace DH\ArtisDiscountAsPromoHotfixPlugin\Form;

use DH\ArtisDiscountAsPromoHotfixPlugin\Subscriber\OrderDiscountHotfixEventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\AdminOrderCreationPlugin\Form\Type\NewOrderType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\HttpFoundation\RequestStack;

final class NewOrderTypeExtension extends AbstractTypeExtension
{
    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var RequestStack */
    private $requestStack;

    public function __construct(EntityManagerInterface $entityManager, RequestStack $requestStack)
    {
        $this->entityManager = $entityManager;
        $this->requestStack = $requestStack;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->addEventSubscriber(new OrderDiscountHotfixEventSubscriber($this->entityManager, $this->requestStack));
    }

    public static function getExtendedTypes(): iterable
    {
        return [NewOrderType::class];
    }
}
