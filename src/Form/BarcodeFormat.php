<?php

declare(strict_types=1);

namespace Nowo\BarcodeInputBundle\Form;

use function in_array;

/**
 * Supported barcode symbologies for camera scanning and validation hints.
 */
enum BarcodeFormat: string
{
    case Ean13   = 'ean_13';
    case Ean8    = 'ean_8';
    case UpcA    = 'upc_a';
    case UpcE    = 'upc_e';
    case Code128 = 'code_128';
    case Code39  = 'code_39';
    case Itf     = 'itf';
    case Codabar = 'codabar';

    /**
     * @return list<string>
     */
    public static function values(): array
    {
        return array_map(static fn (self $case): string => $case->value, self::cases());
    }

    /**
     * @param list<string> $formats
     *
     * @return list<self>
     */
    public static function fromStrings(array $formats): array
    {
        $allowed = self::values();
        $parsed  = [];

        foreach ($formats as $format) {
            if (!in_array($format, $allowed, true)) {
                continue;
            }

            $parsed[] = self::from($format);
        }

        return $parsed !== [] ? $parsed : [self::Ean13, self::Ean8, self::Code128, self::Code39, self::UpcA];
    }
}
