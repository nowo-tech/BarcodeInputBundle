<?php

declare(strict_types=1);

namespace Nowo\BarcodeInputBundle\Tests\Unit\Form;

use Nowo\BarcodeInputBundle\Form\BarcodeType;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\Forms;
use Symfony\Component\Form\FormView;

/**
 * @covers \Nowo\BarcodeInputBundle\Form\BarcodeType
 */
final class BarcodeTypeTest extends TestCase
{
    public function testSubmitNormalizesTrimmedString(): void
    {
        $factory = Forms::createFormFactoryBuilder()
            ->addType(new BarcodeType())
            ->getFormFactory();

        $form = $factory->create(BarcodeType::class, '');
        $form->submit('  40170725  ');

        self::assertTrue($form->isSynchronized());
        self::assertSame('40170725', $form->getData());
    }

    public function testSubmitStripsNonPrintableCharacters(): void
    {
        $factory = Forms::createFormFactoryBuilder()
            ->addType(new BarcodeType())
            ->getFormFactory();

        $form = $factory->create(BarcodeType::class, '', ['strip_non_printable' => true]);
        $form->submit("40170725\x00");

        self::assertTrue($form->isSynchronized());
        self::assertSame('40170725', $form->getData());
    }

    public function testSubmitRespectsMaxLength(): void
    {
        $factory = Forms::createFormFactoryBuilder()
            ->addType(new BarcodeType())
            ->getFormFactory();

        $form = $factory->create(BarcodeType::class, '', ['max_length' => 8]);
        $form->submit('1234567890123');

        self::assertSame('12345678', $form->getData());
    }

    public function testBuildViewExposesBarcodeVariablesAndDataAttributes(): void
    {
        $factory = Forms::createFormFactoryBuilder()
            ->addType(new BarcodeType())
            ->getFormFactory();

        $form = $factory->create(BarcodeType::class, '40170725', [
            'enable_scanner'  => true,
            'max_length'      => 32,
            'formats'         => ['ean_13', 'code_128'],
            'facing_mode'     => 'user',
            'container_class' => 'custom-container',
            'input_class'     => 'custom-input',
            'button_class'    => 'custom-button',
            'autofocus'       => true,
            'attr'            => ['data-controller' => 'existing-controller'],
        ]);

        $view = $form->createView();

        self::assertTrue($view->vars['barcode_enable_scanner']);
        self::assertSame(32, $view->vars['barcode_max_length']);
        self::assertSame(['ean_13', 'code_128'], $view->vars['barcode_formats']);
        self::assertSame('user', $view->vars['barcode_facing_mode']);
        self::assertSame('custom-container', $view->vars['barcode_container_class']);
        self::assertSame('custom-input', $view->vars['barcode_input_class']);
        self::assertSame('custom-button', $view->vars['barcode_button_class']);
        self::assertTrue($view->vars['barcode_autofocus']);
        self::assertSame('existing-controller nowo-barcode-input', $view->vars['attr']['data-controller']);
        self::assertSame('1', $view->vars['attr']['data-nowo-barcode-input-enable-scanner-value']);
        self::assertSame('ean_13,code_128', $view->vars['attr']['data-nowo-barcode-input-formats-value']);
        self::assertSame('user', $view->vars['attr']['data-nowo-barcode-input-facing-mode-value']);
        self::assertSame('32', $view->vars['attr']['data-nowo-barcode-input-max-length-value']);
        self::assertStringContainsString('custom-input', $view->vars['attr']['class']);
        self::assertSame('autofocus', $view->vars['attr']['autofocus']);
    }

    public function testGetParentAndBlockPrefix(): void
    {
        $type = new BarcodeType();

        self::assertSame(TextType::class, $type->getParent());
        self::assertSame('nowo_barcode_input', $type->getBlockPrefix());
    }

    public function testBuildViewMarksDisabledState(): void
    {
        $type                = new BarcodeType();
        $view                = new FormView();
        $view->vars['value'] = '40170725';
        $view->vars['attr']  = [];

        $form    = $this->createMock(FormInterface::class);
        $options = [
            'enable_scanner'  => false,
            'max_length'      => 128,
            'formats'         => ['ean_13'],
            'facing_mode'     => 'environment',
            'container_class' => 'container',
            'input_class'     => 'input',
            'button_class'    => 'button',
            'autofocus'       => false,
            'disabled'        => true,
        ];

        $type->buildView($view, $form, $options);

        self::assertTrue($view->vars['barcode_disabled']);
    }
}
