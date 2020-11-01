Artis Discount As Promo Hotfix Extension
========================================

Plugin which converts order's adjustments into order items' adjustments based on their part in the overall amount.

# installation

First, install using composer:

```bash
composer require digital-holding/artis-discount-as-promo-hotfix-plugin
```

Then, add entry to your `config/bundles.php`:
```php
DH\ArtisDiscountAsPromoHotfixPlugin\DHArtisDiscountAsPromoHotfixPlugin::class => ['all' => true]
```

(this part may be done for you by `Symfony/Flex` already)
