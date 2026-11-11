<?php

declare(strict_types=1);

namespace Nowo\BarcodeInputBundle\Tests\Unit\Form;

use InvalidArgumentException;
use Nowo\BarcodeInputBundle\Form\BarcodeType;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\CallbackTransformer;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\Forms;

/**
 * Both host kinds of the architecture bar: a standard host binds the field to an entity-like string property, a
 * DDD host binds it to a command DTO holding a value object through its own model transformer.
 */
#[CoversClass(BarcodeType::class)]
final class BarcodeTypeHostPathsTest extends TestCase
{
    public function testStandardHostBindsToStringProperty(): void
    {
        $product = new BarcodeHostProduct();
        $form    = $this->factory()->createBuilder(FormType::class, $product, ['data_class' => BarcodeHostProduct::class])
            ->add('barcode', BarcodeType::class)
            ->getForm();

        $form->submit(['barcode' => ' 40170725 ']);

        self::assertTrue($form->isSynchronized());
        self::assertSame('40170725', $product->barcode);
    }

    public function testDddHostBindsToValueObjectThroughModelTransformer(): void
    {
        $command = new BarcodeHostCommand();
        $builder = $this->factory()->createBuilder(FormType::class, $command, ['data_class' => BarcodeHostCommand::class])
            ->add('gtin', BarcodeType::class, ['formats' => ['ean_8']]);
        $builder->get('gtin')->addModelTransformer(new CallbackTransformer(
            static fn (?BarcodeHostGtin $gtin): string => $gtin === null ? '' : $gtin->value,
            static function (?string $code): ?BarcodeHostGtin {
                if ($code === null || $code === '') {
                    return null;
                }
                try {
                    return BarcodeHostGtin::fromString($code);
                } catch (InvalidArgumentException $e) {
                    throw new TransformationFailedException($e->getMessage(), 0, $e);
                }
            },
        ));
        $form = $builder->getForm();

        $form->submit(['gtin' => ' 40170725 ']);

        self::assertTrue($form->isSynchronized());
        self::assertInstanceOf(BarcodeHostGtin::class, $command->gtin);
        self::assertSame('40170725', $command->gtin->value);
        self::assertSame('40170725', $form->createView()['gtin']->vars['value']);
    }

    public function testDddHostValueObjectRejectionIsATransformationFailure(): void
    {
        $command = new BarcodeHostCommand();
        $builder = $this->factory()->createBuilder(FormType::class, $command, ['data_class' => BarcodeHostCommand::class])
            ->add('gtin', BarcodeType::class);
        $builder->get('gtin')->addModelTransformer(new CallbackTransformer(
            static fn (?BarcodeHostGtin $gtin): string => $gtin === null ? '' : $gtin->value,
            static function (?string $code): ?BarcodeHostGtin {
                try {
                    return $code === null || $code === '' ? null : BarcodeHostGtin::fromString($code);
                } catch (InvalidArgumentException $e) {
                    throw new TransformationFailedException($e->getMessage(), 0, $e);
                }
            },
        ));
        $form = $builder->getForm();

        $form->submit(['gtin' => 'not-a-gtin']);

        self::assertFalse($form->get('gtin')->isSynchronized());
        self::assertNull($command->gtin);
    }

    private function factory(): FormFactoryInterface
    {
        return Forms::createFormFactoryBuilder()->addType(new BarcodeType())->getFormFactory();
    }
}

/** Standard host: entity-like object with a plain string property. */
final class BarcodeHostProduct
{
    public ?string $barcode = null;
}

/** DDD host: command DTO carrying a value object. */
final class BarcodeHostCommand
{
    public ?BarcodeHostGtin $gtin = null;
}

/** DDD host value object (digits only, 8 to 14 characters). */
final readonly class BarcodeHostGtin
{
    private function __construct(public string $value)
    {
    }

    public static function fromString(string $value): self
    {
        if (preg_match('/^\d{8,14}$/', $value) !== 1) {
            throw new InvalidArgumentException('Not a GTIN.');
        }

        return new self($value);
    }
}
