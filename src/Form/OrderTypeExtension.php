<?php

declare(strict_types=1);

namespace DH\ArtisSimpleProductVariantOverwriteHotfixPlugin\Form;

use App\Entity\Product\Product;
use App\Factory\Product\DHVariantSpecificationItemValueViewFactoryInterface;
use DH\Artis\Product\Specification\SpecificationItem\SpecificationItemValueResolverInterface;
use DH\ArtisDiscountAsPromoHotfixPlugin\Subscriber\OrderDiscountHotfixEventSubscriber;
use DH\ArtisSimpleProductVariantOverwriteHotfixPlugin\Subscriber\SimpleProductVariantHotfixEventSubscriber;
use Doctrine\ORM\EntityManagerInterface;
use Sylius\Bundle\ProductBundle\Form\Type\ProductType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductVariantType;
use Symfony\Component\Form\AbstractTypeExtension;
use Symfony\Component\Form\FormBuilderInterface;

final class OrderTypeExtension extends AbstractTypeExtension
{
    /** @var EntityManagerInterface */
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->addEventSubscriber(new OrderDiscountHotfixEventSubscriber($this->entityManager));
    }

    public static function getExtendedTypes(): iterable
    {
        return [OrderType::class];
    }
}
