<?php

declare(strict_types=1);

namespace Nowo\BarcodeInputBundle\Tests\Unit\DependencyInjection;

use Nowo\BarcodeInputBundle\DependencyInjection\Configuration;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Config\Definition\Processor;

/**
 * @covers \Nowo\BarcodeInputBundle\DependencyInjection\Configuration
 */
final class ConfigurationTest extends TestCase
{
    public function testGetConfigTreeBuilderReturnsTreeWithAlias(): void
    {
        $config = new Configuration();
        $tree   = $config->getConfigTreeBuilder();
        self::assertSame(Configuration::ALIAS, $tree->buildTree()->getName());
    }

    public function testProcessConfigurationWithDefaults(): void
    {
        $processor = new Processor();
        $config    = $processor->processConfiguration(new Configuration(), []);

        self::assertTrue($config['enable_scanner']);
        self::assertSame(128, $config['max_length']);
        self::assertTrue($config['trim_whitespace']);
        self::assertSame(['ean_13', 'ean_8', 'code_128', 'code_39', 'upc_a'], $config['formats']);
        self::assertSame('environment', $config['facing_mode']);
        self::assertSame('form_div_layout.html.twig', $config['form_theme']);
    }

    public function testProcessConfigurationWithCustomValues(): void
    {
        $processor = new Processor();
        $config    = $processor->processConfiguration(new Configuration(), [[
            'enable_scanner'  => false,
            'max_length'      => 32,
            'trim_whitespace' => false,
            'formats'         => ['code_39', 'itf'],
            'facing_mode'     => 'user',
            'form_theme'      => 'bootstrap_5_layout.html.twig',
        ]]);

        self::assertFalse($config['enable_scanner']);
        self::assertSame(32, $config['max_length']);
        self::assertFalse($config['trim_whitespace']);
        self::assertSame(['code_39', 'itf'], $config['formats']);
        self::assertSame('user', $config['facing_mode']);
        self::assertSame('bootstrap_5_layout.html.twig', $config['form_theme']);
    }
}
