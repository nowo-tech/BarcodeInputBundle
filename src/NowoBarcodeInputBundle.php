<?php

declare(strict_types=1);

namespace Nowo\BarcodeInputBundle;

use Nowo\BarcodeInputBundle\DependencyInjection\Compiler\TwigPathsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Symfony bundle entry point for the barcode input form type (text field + optional camera scanner).
 */
final class NowoBarcodeInputBundle extends Bundle
{
    /**
     * Registers the compiler pass that appends the bundle Twig views path.
     *
     * @param ContainerBuilder $container Service container being built
     */
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new TwigPathsPass());
    }
}
