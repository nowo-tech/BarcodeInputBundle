<?php

declare(strict_types=1);

namespace Nowo\BarcodeInputBundle\Tests\Unit\Form\DataTransformer;

use Nowo\BarcodeInputBundle\Form\DataTransformer\BarcodeValueTransformer;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Nowo\BarcodeInputBundle\Form\DataTransformer\BarcodeValueTransformer
 */
final class BarcodeValueTransformerTest extends TestCase
{
    public function testTransformAndReverseTransformNormalizeValue(): void
    {
        $transformer = new BarcodeValueTransformer(13, true, true);

        self::assertSame('40170725', $transformer->transform(' 40170725 '));
        self::assertSame('40170725', $transformer->reverseTransform("40170725\x00"));
    }

    public function testReverseTransformTruncatesToMaxLength(): void
    {
        $transformer = new BarcodeValueTransformer(8, true, true);

        self::assertSame('12345678', $transformer->reverseTransform('1234567890123'));
    }

    public function testTransformReturnsEmptyStringForNonString(): void
    {
        $transformer = new BarcodeValueTransformer();

        self::assertSame('', $transformer->transform(null));
        self::assertSame('', $transformer->reverseTransform(null));
    }
}
