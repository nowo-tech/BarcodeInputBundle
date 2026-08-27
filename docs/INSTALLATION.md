# Installation

```bash
composer require nowo-tech/barcode-input-bundle
```

The Flex recipe lives under `.symfony/recipe/` (copy those files if Flex does not apply them).

Enable bundle if Flex does not do it automatically:

```php
return [
    Nowo\BarcodeInputBundle\NowoBarcodeInputBundle::class => ['all' => true],
];
```

The bundle ships a built script at `src/Resources/public/barcode-input.js`. After `composer require`, install assets in your app:

```bash
php bin/console assets:install
```

This publishes files to `public/bundles/nowobarcodeinput/`. The bundle also registers a named Symfony asset package (`nowo_barcode_input`), so templates should load the script via that package instead of hard-coding the public path:

```twig
<script src="{{ asset('barcode-input.js', 'nowo_barcode_input') }}"></script>
```

To rebuild TypeScript from this repository (contributors / custom builds):

```bash
pnpm install
pnpm run build
php bin/console assets:install
```

## Twig Extra Bundle (REQ-TWIG-004)

This package ships Twig templates. Host applications **must** install and enable Twig Extra:

```bash
composer require twig/extra-bundle twig/string-extra
```

Register `Twig\Extra\TwigExtraBundle\TwigExtraBundle` in `config/bundles.php` (Flex usually does this). Demos already include the same stack. The package `release-check` runs `make check-twig-extra` to guard this contract.
