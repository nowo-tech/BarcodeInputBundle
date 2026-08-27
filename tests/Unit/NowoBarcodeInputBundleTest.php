<?php

declare(strict_types=1);

namespace Nowo\BarcodeInputBundle\Tests\Unit;

use Nowo\BarcodeInputBundle\DependencyInjection\Compiler\TwigPathsPass;
use Nowo\BarcodeInputBundle\Form\DataTransformer\BarcodeValueTransformer;
use Nowo\BarcodeInputBundle\NowoBarcodeInputBundle;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * @covers \Nowo\BarcodeInputBundle\NowoBarcodeInputBundle
 * @covers \Nowo\BarcodeInputBundle\Form\DataTransformer\BarcodeValueTransformer
 */
final class NowoBarcodeInputBundleTest extends TestCase
{
    public function testBundleRegistersTwigCompilerPass(): void
    {
        $bundle    = new NowoBarcodeInputBundle();
        $container = new ContainerBuilder();
        $bundle->build($container);

        $passes = $container->getCompilerPassConfig()->getPasses();
        $found  = false;
        foreach ($passes as $pass) {
            if ($pass instanceof TwigPathsPass) {
                $found = true;
                break;
            }
        }

        self::assertTrue($found);
    }

    public function testTransformerNormalizesBarcode(): void
    {
        $transformer = new BarcodeValueTransformer(13, true, true);

        self::assertSame('40170725', $transformer->reverseTransform(' 40170725 '));
        self::assertSame('40170725', $transformer->transform('40170725'));
    }

    public function testTransformerTruncatesToMaxLength(): void
    {
        $transformer = new BarcodeValueTransformer(8, true, true);

        self::assertSame('12345678', $transformer->reverseTransform('1234567890'));
    }

    public function testTransformerHandlesEmptyInput(): void
    {
        $transformer = new BarcodeValueTransformer();

        self::assertSame('', $transformer->transform(null));
        self::assertSame('', $transformer->reverseTransform(''));
    }
}
