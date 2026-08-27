<?php

declare(strict_types=1);

namespace Nowo\BarcodeInputBundle;

use Nowo\BarcodeInputBundle\DependencyInjection\Compiler\TwigPathsPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

final class NowoBarcodeInputBundle extends Bundle
{
    public function build(ContainerBuilder $container): void
    {
        $container->addCompilerPass(new TwigPathsPass());
    }
}
