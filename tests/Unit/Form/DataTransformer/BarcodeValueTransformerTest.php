<?php

declare(strict_types=1);

namespace Nowo\BarcodeInputBundle\Tests\Unit\Form\DataTransformer;

use Nowo\BarcodeInputBundle\Form\DataTransformer\BarcodeValueTransformer;
use PHPUnit\Framework\TestCase;
use Stringable;
use Symfony\Component\Form\Exception\TransformationFailedException;

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

    public function testReverseTransformRejectsValueOverMaxLengthInsteadOfTruncating(): void
    {
        $transformer = new BarcodeValueTransformer(8, true, true);

        try {
            $transformer->reverseTransform('1234567890123');
            self::fail('Over-long barcode must not be truncated silently.');
        } catch (TransformationFailedException $e) {
            self::assertSame(['{{ limit }}' => '8'], $e->getInvalidMessageParameters());
            self::assertNotNull($e->getInvalidMessage());
        }
    }

    public function testReverseTransformAcceptsValueAtMaxLengthAfterNormalization(): void
    {
        $transformer = new BarcodeValueTransformer(8, true, true);

        self::assertSame('12345678', $transformer->reverseTransform("  1234\x005678  "));
    }

    public function testTransformCastsScalarModelValues(): void
    {
        $transformer = new BarcodeValueTransformer();

        self::assertSame('40170725', $transformer->transform(40170725));
        self::assertSame('1.5', $transformer->transform(1.5));
        self::assertSame('ABC-1', $transformer->transform(new class implements Stringable {
            public function __toString(): string
            {
                return ' ABC-1 ';
            }
        }));
        foreach ([true, ['x']] as $unsupported) {
            /* @var mixed $unsupported */
            self::assertSame('', $transformer->transform($unsupported));
        }
    }

    public function testTransformDoesNotTruncateExistingModelData(): void
    {
        $transformer = new BarcodeValueTransformer(4);

        self::assertSame('123456', $transformer->transform('123456'));
    }

    public function testTransformReturnsEmptyStringForNonString(): void
    {
        $transformer = new BarcodeValueTransformer();

        self::assertSame('', $transformer->transform(null));
        self::assertSame('', $transformer->reverseTransform(null));
    }
}
