<?php

declare(strict_types=1);

namespace DH\ArtisDiscountAsPromoHotfixPlugin\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('artis_discount_as_promo_hotfix_plugin');
        $rootNode = $treeBuilder->getRootNode();

        return $treeBuilder;
    }
}
