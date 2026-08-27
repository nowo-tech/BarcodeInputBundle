<?php

declare(strict_types=1);

namespace Nowo\BarcodeInputBundle\Form\DataTransformer;

use Symfony\Component\Form\DataTransformerInterface;

use function is_string;
use function preg_replace;
use function strlen;
use function substr;
use function trim;

/**
 * Normalizes barcode strings between model and view.
 *
 * @implements DataTransformerInterface<string, string>
 */
final class BarcodeValueTransformer implements DataTransformerInterface
{
    public function __construct(
        private readonly int $maxLength = 128,
        private readonly bool $trimWhitespace = true,
        private readonly bool $stripNonPrintable = true,
    ) {
    }

    public function transform(mixed $value): string
    {
        if (!is_string($value)) {
            return '';
        }

        return $this->normalize($value);
    }

    public function reverseTransform(mixed $value): string
    {
        if (!is_string($value)) {
            return '';
        }

        return $this->normalize($value);
    }

    private function normalize(string $value): string
    {
        if ($this->trimWhitespace) {
            $value = trim($value);
        }

        if ($this->stripNonPrintable) {
            $value = (string) preg_replace('/[^\x20-\x7E]/', '', $value);
        }

        if (strlen($value) > $this->maxLength) {
            return substr($value, 0, $this->maxLength);
        }

        return $value;
    }
}
