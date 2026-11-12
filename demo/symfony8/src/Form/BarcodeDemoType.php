<?php

declare(strict_types=1);

namespace App\Form;

use Nowo\FormKitBundle\Attribute\FormKitConfig;
use Nowo\FormKitBundle\Form\FormOptionsTrait;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * REQ-DEMO-015: a FormKit form (profile `demo`); the bundle's BarcodeType is added through FormKit's addBarcode().
 *
 * @extends AbstractType<array{barcode: string}>
 */
#[FormKitConfig('demo')]
final class BarcodeDemoType extends AbstractType
{
    use FormOptionsTrait;

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $this->addBarcode($builder, 'barcode', [
            'label'          => 'Barcode',
            'help'           => $options['barcode_help'],
            'placeholder'    => false,
            'enable_scanner' => $options['enable_scanner'],
            'max_length'     => $options['max_length'],
            'formats'        => $options['formats'],
            'button_class'   => 'nowo-ui-btn nowo-ui-btn--outline',
        ]);
        $this->addWithDefaults($builder, 'submit', SubmitType::class, [
            'label' => 'Submit barcode',
            'attr'  => ['class' => 'nowo-ui-btn nowo-ui-btn--primary'],
        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'enable_scanner' => true,
            'max_length'     => 128,
            'formats'        => ['code_128'],
            'barcode_help'   => '',
        ]);
        $resolver->setAllowedTypes('enable_scanner', 'bool');
        $resolver->setAllowedTypes('max_length', 'int');
        $resolver->setAllowedTypes('formats', 'array');
        $resolver->setAllowedTypes('barcode_help', 'string');
    }
}
