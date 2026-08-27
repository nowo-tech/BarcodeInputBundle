<?php

declare(strict_types=1);

namespace Nowo\BarcodeInputBundle\Tests\Unit\DependencyInjection;

use Nowo\BarcodeInputBundle\DependencyInjection\NowoBarcodeInputExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;

/**
 * @covers \Nowo\BarcodeInputBundle\DependencyInjection\NowoBarcodeInputExtension
 */
final class NowoBarcodeInputExtensionTest extends TestCase
{
    public function testGetAlias(): void
    {
        $extension = new NowoBarcodeInputExtension();
        self::assertSame('nowo_barcode_input', $extension->getAlias());
    }

    public function testLoadRegistersParameters(): void
    {
        $extension = new NowoBarcodeInputExtension();
        $container = new ContainerBuilder();
        $container->setParameter('kernel.bundles_metadata', []);

        $extension->load([[
            'enable_scanner'  => false,
            'max_length'      => 32,
            'trim_whitespace' => false,
            'formats'         => ['code_39'],
            'facing_mode'     => 'user',
            'form_theme'      => 'bootstrap_5_layout.html.twig',
        ]], $container);

        self::assertFalse($container->getParameter('nowo_barcode_input.enable_scanner'));
        self::assertSame(32, $container->getParameter('nowo_barcode_input.max_length'));
        self::assertFalse($container->getParameter('nowo_barcode_input.trim_whitespace'));
        self::assertSame(['code_39'], $container->getParameter('nowo_barcode_input.formats'));
        self::assertSame('user', $container->getParameter('nowo_barcode_input.facing_mode'));
        self::assertSame('bootstrap_5_layout.html.twig', $container->getParameter('nowo_barcode_input.form_theme'));
    }

    public function testPrependSkipsWhenTwigExtensionMissing(): void
    {
        $extension = new NowoBarcodeInputExtension();
        $container = new ContainerBuilder();

        $extension->prepend($container);

        self::assertSame([], $container->getExtensionConfig('twig'));
    }

    public function testPrependAddsMappedTwigThemeAndFallback(): void
    {
        $extension = new NowoBarcodeInputExtension();

        $container = new ContainerBuilder();
        $container->registerExtension(new class extends Extension {
            public function load(array $configs, ContainerBuilder $container): void
            {
            }

            public function getAlias(): string
            {
                return 'twig';
            }
        });

        $container->prependExtensionConfig('nowo_barcode_input', [
            'form_theme' => 'bootstrap_5_layout.html.twig',
        ]);
        $extension->prepend($container);
        $twigConfigs = $container->getExtensionConfig('twig');
        self::assertSame('@NowoBarcodeInputBundle/Form/barcode_input_theme_bootstrap5.html.twig', $twigConfigs[0]['form_themes'][0]);

        $container2 = new ContainerBuilder();
        $container2->registerExtension(new class extends Extension {
            public function load(array $configs, ContainerBuilder $container): void
            {
            }

            public function getAlias(): string
            {
                return 'twig';
            }
        });
        $container2->prependExtensionConfig('nowo_barcode_input', [
            'form_theme' => 'unknown_theme.html.twig',
        ]);
        $extension->prepend($container2);
        $twigConfigs2 = $container2->getExtensionConfig('twig');
        self::assertSame('@NowoBarcodeInputBundle/Form/barcode_input_theme.html.twig', $twigConfigs2[0]['form_themes'][0]);
    }

    public function testPrependAddsNamedAssetPackageWhenFrameworkExtensionPresent(): void
    {
        $frameworkExtension = new class extends Extension {
            public function load(array $configs, ContainerBuilder $container): void
            {
            }

            public function getAlias(): string
            {
                return 'framework';
            }
        };
        $container = new ContainerBuilder();
        $container->registerExtension($frameworkExtension);

        $extension = new NowoBarcodeInputExtension();
        $extension->prepend($container);

        $frameworkConfig = $container->getExtensionConfig('framework');
        self::assertNotEmpty($frameworkConfig);
        self::assertSame(
            '/bundles/nowobarcodeinput',
            $frameworkConfig[0]['assets']['packages']['nowo_barcode_input']['base_path'] ?? null,
        );
    }

    public function testPrependSkipsAssetPackageWhenFrameworkExtensionMissing(): void
    {
        $extension = new NowoBarcodeInputExtension();
        $container = new ContainerBuilder();

        $extension->prepend($container);

        self::assertFalse($container->hasExtension('framework'));
        self::assertSame([], $container->getExtensionConfig('framework'));
    }
}
