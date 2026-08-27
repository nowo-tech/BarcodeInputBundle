<?php

declare(strict_types=1);

namespace Nowo\BarcodeInputBundle\Form;

use Nowo\BarcodeInputBundle\Form\DataTransformer\BarcodeValueTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @extends AbstractType<string>
 */
final class BarcodeType extends AbstractType
{
    /**
     * @param list<string> $defaultFormats
     */
    public function __construct(
        private readonly bool $defaultEnableScanner = true,
        private readonly int $defaultMaxLength = 128,
        private readonly bool $defaultTrimWhitespace = true,
        private readonly array $defaultFormats = ['ean_13', 'ean_8', 'code_128', 'code_39', 'upc_a'],
        private readonly string $defaultFacingMode = 'environment',
    ) {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addModelTransformer(new BarcodeValueTransformer(
            $options['max_length'],
            $options['trim_whitespace'],
            $options['strip_non_printable'],
        ));
    }

    public function buildView(FormView $view, FormInterface $form, array $options): void
    {
        $view->vars['barcode_enable_scanner']  = $options['enable_scanner'];
        $view->vars['barcode_max_length']      = $options['max_length'];
        $view->vars['barcode_formats']         = $options['formats'];
        $view->vars['barcode_facing_mode']     = $options['facing_mode'];
        $view->vars['barcode_container_class'] = $options['container_class'];
        $view->vars['barcode_input_class']     = $options['input_class'];
        $view->vars['barcode_button_class']    = $options['button_class'];
        $view->vars['barcode_autofocus']       = $options['autofocus'];
        $view->vars['barcode_disabled']        = $options['disabled'];

        $view->vars['attr']['data-controller'] = trim(
            ($view->vars['attr']['data-controller'] ?? '') . ' nowo-barcode-input',
        );
        $view->vars['attr']['data-nowo-barcode-input-enable-scanner-value'] = $options['enable_scanner'] ? '1' : '0';
        $view->vars['attr']['data-nowo-barcode-input-formats-value']        = implode(',', $options['formats']);
        $view->vars['attr']['data-nowo-barcode-input-facing-mode-value']    = $options['facing_mode'];
        $view->vars['attr']['data-nowo-barcode-input-max-length-value']     = (string) $options['max_length'];
        $view->vars['attr']['class']                                        = trim(
            ($view->vars['attr']['class'] ?? '') . ' ' . $options['input_class'],
        );

        if ($options['autofocus']) {
            $view->vars['attr']['autofocus'] = 'autofocus';
        }

        if ($options['max_length'] > 0) {
            $view->vars['attr']['maxlength'] = (string) $options['max_length'];
        }

        $view->vars['attr']['inputmode']    = 'text';
        $view->vars['attr']['autocomplete'] = 'off';
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'enable_scanner'      => $this->defaultEnableScanner,
            'max_length'          => $this->defaultMaxLength,
            'trim_whitespace'     => $this->defaultTrimWhitespace,
            'strip_non_printable' => true,
            'formats'             => $this->defaultFormats,
            'facing_mode'         => $this->defaultFacingMode,
            'container_class'     => 'nowo-barcode-input__container',
            'input_class'         => 'nowo-barcode-input__input',
            'button_class'        => 'nowo-barcode-input__scan-button',
            'autofocus'           => false,
            'empty_data'          => '',
            'required'            => false,
            'translation_domain'  => 'NowoBarcodeInputBundle',
        ]);

        $resolver->setAllowedTypes('enable_scanner', ['bool']);
        $resolver->setAllowedTypes('max_length', ['int']);
        $resolver->setAllowedValues('max_length', static fn (int $length): bool => $length >= 1 && $length <= 256);
        $resolver->setAllowedTypes('trim_whitespace', ['bool']);
        $resolver->setAllowedTypes('strip_non_printable', ['bool']);
        $resolver->setAllowedTypes('formats', ['array']);
        $resolver->setAllowedTypes('facing_mode', ['string']);
        $resolver->setAllowedValues('facing_mode', ['environment', 'user']);
        $resolver->setAllowedTypes('container_class', ['string']);
        $resolver->setAllowedTypes('input_class', ['string']);
        $resolver->setAllowedTypes('button_class', ['string']);
        $resolver->setAllowedTypes('autofocus', ['bool']);
    }

    public function getParent(): string
    {
        return TextType::class;
    }

    public function getBlockPrefix(): string
    {
        return 'nowo_barcode_input';
    }
}
