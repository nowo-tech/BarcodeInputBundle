<?php

declare(strict_types=1);

namespace Nowo\BarcodeInputBundle\Tests\Unit\Form;

use Nowo\BarcodeInputBundle\Form\BarcodeFormat;
use PHPUnit\Framework\TestCase;

/**
 * @covers \Nowo\BarcodeInputBundle\Form\BarcodeFormat
 */
final class BarcodeFormatTest extends TestCase
{
    public function testValuesReturnsAllCases(): void
    {
        self::assertContains('ean_13', BarcodeFormat::values());
        self::assertContains('code_128', BarcodeFormat::values());
    }

    public function testFromStringsIgnoresUnknownFormats(): void
    {
        $formats = BarcodeFormat::fromStrings(['ean_13', 'unknown', 'code_39']);

        self::assertCount(2, $formats);
        self::assertSame(BarcodeFormat::Ean13, $formats[0]);
        self::assertSame(BarcodeFormat::Code39, $formats[1]);
    }

    public function testFromStringsFallsBackToDefaultsWhenEmpty(): void
    {
        $formats = BarcodeFormat::fromStrings([]);

        self::assertNotEmpty($formats);
        self::assertContains(BarcodeFormat::Ean13, $formats);
    }
}
