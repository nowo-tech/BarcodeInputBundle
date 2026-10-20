<?php

declare(strict_types=1);

namespace Nowo\BarcodeInputBundle\Form\DataTransformer;

use Stringable;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;

use function is_float;
use function is_int;
use function is_string;
use function preg_replace;
use function sprintf;
use function strlen;
use function trim;

/**
 * Normalizes barcode strings between model and view.
 *
 * Submitted values longer than max_length are rejected (never silently truncated).
 *
 * @implements DataTransformerInterface<float|int|string|Stringable, string>
 */
final readonly class BarcodeValueTransformer implements DataTransformerInterface
{
    /**
     * @param int $maxLength Maximum accepted length after normalization
     * @param bool $trimWhitespace Trim leading and trailing whitespace
     * @param bool $stripNonPrintable Remove characters outside printable ASCII
     */
    public function __construct(
        private int $maxLength = 128,
        private bool $trimWhitespace = true,
        private bool $stripNonPrintable = true,
    ) {
    }

    /**
     * Converts the model value (string, number or Stringable) into the normalized view string.
     *
     * @param mixed $value Model value
     *
     * @return string Normalized string ('' for unsupported values)
     */
    public function transform(mixed $value): string
    {
        if (is_int($value) || is_float($value) || $value instanceof Stringable) {
            $value = (string) $value;
        }

        if (!is_string($value)) {
            return '';
        }

        return $this->normalize($value);
    }

    /**
     * Normalizes the submitted string and rejects values longer than `maxLength`.
     *
     * @param mixed $value Submitted view value
     *
     * @throws TransformationFailedException When the normalized barcode exceeds `maxLength`
     *
     * @return string Normalized barcode
     */
    public function reverseTransform(mixed $value): string
    {
        if (!is_string($value)) {
            return '';
        }

        $value = $this->normalize($value);

        if (strlen($value) > $this->maxLength) {
            throw new TransformationFailedException(sprintf('The barcode is longer than %d characters.', $this->maxLength), 0, null, 'This value is too long. It should have {{ limit }} characters or less.', ['{{ limit }}' => (string) $this->maxLength]);
        }

        return $value;
    }

    private function normalize(string $value): string
    {
        if ($this->trimWhitespace) {
            $value = trim($value);
        }

        if ($this->stripNonPrintable) {
            $value = (string) preg_replace('/[^\x20-\x7E]/', '', $value);
        }

        return $value;
    }
}
