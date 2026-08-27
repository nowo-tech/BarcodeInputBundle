<?php

declare(strict_types=1);

namespace Nowo\BarcodeInputBundle\DependencyInjection;

use Nowo\BarcodeInputBundle\Form\BarcodeFormat;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    public const ALIAS = 'nowo_barcode_input';

    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder(self::ALIAS);

        $treeBuilder->getRootNode()
            ->children()
                ->booleanNode('enable_scanner')
                    ->defaultTrue()
                    ->info('Show the camera scan button in the widget by default')
                ->end()
                ->integerNode('max_length')
                    ->min(1)
                    ->max(256)
                    ->defaultValue(128)
                    ->info('Maximum barcode length accepted by the form type')
                ->end()
                ->booleanNode('trim_whitespace')
                    ->defaultTrue()
                    ->info('Trim leading and trailing whitespace on submit')
                ->end()
                ->arrayNode('formats')
                    ->scalarPrototype()->end()
                    ->defaultValue(['ean_13', 'ean_8', 'code_128', 'code_39', 'upc_a'])
                    ->info('Supported symbologies for camera scanner hints')
                    ->validate()
                        ->ifTrue(static fn (array $formats): bool => [] === array_filter(
                            $formats,
                            static fn (mixed $format): bool => is_string($format) && in_array($format, BarcodeFormat::values(), true),
                        ))
                        ->thenInvalid('formats must contain at least one supported symbology')
                    ->end()
                ->end()
                ->enumNode('facing_mode')
                    ->values(['environment', 'user'])
                    ->defaultValue('environment')
                    ->info('Camera facing mode when scanning: environment (rear) or user (front)')
                ->end()
                ->scalarNode('form_theme')
                    ->defaultValue('form_div_layout.html.twig')
                    ->info('Twig form theme mapped to bundle themes')
                ->end()
            ->end();

        return $treeBuilder;
    }
}
