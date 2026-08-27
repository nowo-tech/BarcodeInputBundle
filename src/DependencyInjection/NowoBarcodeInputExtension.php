<?php

declare(strict_types=1);

namespace Nowo\BarcodeInputBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Extension\Extension;
use Symfony\Component\DependencyInjection\Extension\PrependExtensionInterface;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

final class NowoBarcodeInputExtension extends Extension implements PrependExtensionInterface
{
    private const FORM_THEME_MAP = [
        'form_div_layout.html.twig'               => '@NowoBarcodeInputBundle/Form/barcode_input_theme.html.twig',
        'form_table_layout.html.twig'             => '@NowoBarcodeInputBundle/Form/barcode_input_theme_table.html.twig',
        'bootstrap_5_layout.html.twig'            => '@NowoBarcodeInputBundle/Form/barcode_input_theme_bootstrap5.html.twig',
        'bootstrap_5_horizontal_layout.html.twig' => '@NowoBarcodeInputBundle/Form/barcode_input_theme_bootstrap5_horizontal.html.twig',
        'bootstrap_4_layout.html.twig'            => '@NowoBarcodeInputBundle/Form/barcode_input_theme_bootstrap4.html.twig',
        'bootstrap_4_horizontal_layout.html.twig' => '@NowoBarcodeInputBundle/Form/barcode_input_theme_bootstrap4_horizontal.html.twig',
        'bootstrap_3_layout.html.twig'            => '@NowoBarcodeInputBundle/Form/barcode_input_theme_bootstrap3.html.twig',
        'bootstrap_3_horizontal_layout.html.twig' => '@NowoBarcodeInputBundle/Form/barcode_input_theme_bootstrap3_horizontal.html.twig',
        'foundation_5_layout.html.twig'           => '@NowoBarcodeInputBundle/Form/barcode_input_theme_foundation5.html.twig',
        'foundation_6_layout.html.twig'           => '@NowoBarcodeInputBundle/Form/barcode_input_theme_foundation6.html.twig',
        'tailwind_2_layout.html.twig'             => '@NowoBarcodeInputBundle/Form/barcode_input_theme_tailwind2.html.twig',
    ];

    public function load(array $configs, ContainerBuilder $container): void
    {
        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yaml');

        $config = $this->processConfiguration(new Configuration(), $configs);
        $container->setParameter(Configuration::ALIAS . '.enable_scanner', $config['enable_scanner']);
        $container->setParameter(Configuration::ALIAS . '.max_length', $config['max_length']);
        $container->setParameter(Configuration::ALIAS . '.trim_whitespace', $config['trim_whitespace']);
        $container->setParameter(Configuration::ALIAS . '.formats', $config['formats']);
        $container->setParameter(Configuration::ALIAS . '.facing_mode', $config['facing_mode']);
        $container->setParameter(Configuration::ALIAS . '.form_theme', $config['form_theme']);
    }

    public function prepend(ContainerBuilder $container): void
    {
        $configs   = $container->getExtensionConfig(Configuration::ALIAS);
        $config    = $this->processConfiguration(new Configuration(), $configs);
        $formTheme = $config['form_theme'] ?? 'form_div_layout.html.twig';
        $themePath = self::FORM_THEME_MAP[$formTheme] ?? self::FORM_THEME_MAP['form_div_layout.html.twig'];

        if ($container->hasExtension('twig')) {
            $container->prependExtensionConfig('twig', [
                'form_themes' => [$themePath],
            ]);
        }

        if ($container->hasExtension('framework')) {
            $container->prependExtensionConfig('framework', [
                'assets' => [
                    'packages' => [
                        Configuration::ALIAS => [
                            'base_path' => '/bundles/nowobarcodeinput',
                        ],
                    ],
                ],
            ]);
        }
    }

    public function getAlias(): string
    {
        return Configuration::ALIAS;
    }
}
