Artis Simple Product Variant Overwrite Hotfix Extension
=======================================================

Plugin which solves the problem of saving simple products which causes clearing of fields not present in the view.

By default Sylius adds repeated variant's fields into the form of the parent product which looks like bad coding in case
we actually do not need to set particular variant's parameters while editing the product.

This plugin solves this problem by adding a listener which removes fields which were not set from the form and therefore
stops any processing of the values.

# installation

First, install using composer:

```bash
composer require digital-holding/artis-simple-product-variant-overwrite-hotfix-plugin
```

Then, add entry to your `config/bundles.php`:
```php
DH\ArtisSimpleProductVariantOverwriteHotfixPlugin\DHArtisSimpleProductVariantOverwriteHotfixPlugin::class => ['all' => true]
```

(this part may be done for you by `Symfony/Flex` already)
