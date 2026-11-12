<?php

declare(strict_types=1);

namespace Nowo\BarcodeInputBundle\Tests\Fixtures\Worker;

use Nowo\BarcodeInputBundle\Form\BarcodeType;
use Symfony\Component\Form\Extension\Core\Type\FormType;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Twig\Environment;

/**
 * Host controller that owns the form: renders it and reads the submission.
 */
final readonly class WorkerHostController
{
    public function __construct(
        private FormFactoryInterface $forms,
        private Environment $twig,
    ) {
    }

    public function __invoke(Request $request, string $who): Response
    {
        $form = $this->forms->createNamedBuilder('f', FormType::class)
            ->add('code', BarcodeType::class, $who === 'alpha'
                ? ['formats' => ['ean_13'], 'max_length' => 13, 'enable_scanner' => true]
                : ['formats' => ['code_128', 'code_39'], 'max_length' => 20, 'enable_scanner' => false, 'trim_whitespace' => false])
            ->getForm();
        $form->handleRequest($request);

        $html = $this->twig->render('host_form.html.twig', ['form' => $form->createView()]);
        $sent = $form->isSubmitted() ? (string) $form->get('code')->getData() : '';

        return new Response('who=' . $who . ';submitted=[' . $sent . '];locale=' . $request->getLocale() . ';' . $html);
    }
}
