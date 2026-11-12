<?php

declare(strict_types=1);

namespace App\Controller;

use App\Form\BarcodeDemoType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

use function sprintf;

final class BarcodeDemoController extends AbstractController
{
    /**
     * @var array<string, array{title: string, enable_scanner: bool, max_length: int, formats: list<string>, help: string}>
     */
    private const EXAMPLES = [
        'ean-scanner' => [
            'title'          => 'EAN scanner (camera + manual)',
            'enable_scanner' => true,
            'max_length'     => 13,
            'formats'        => ['ean_13', 'ean_8', 'upc_a'],
            'help'           => 'Scan a product barcode with the camera or type it manually. Works with hardware scanners too.',
        ],
        'warehouse-code128' => [
            'title'          => 'Warehouse Code 128',
            'enable_scanner' => true,
            'max_length'     => 64,
            'formats'        => ['code_128', 'code_39', 'itf'],
            'help'           => 'Longer alphanumeric warehouse labels with camera scanning enabled.',
        ],
        'manual-only' => [
            'title'          => 'Manual entry only',
            'enable_scanner' => false,
            'max_length'     => 32,
            'formats'        => ['code_128'],
            'help'           => 'Text input without camera button — ideal for keyboard wedge scanners.',
        ],
    ];

    #[Route(path: '/', name: 'app_root', methods: ['GET'])]
    public function root(): RedirectResponse
    {
        return $this->redirectToRoute('app_demo_index');
    }

    #[Route(path: '/demo', name: 'app_demo_index', methods: ['GET'])]
    public function index(): Response
    {
        return $this->render('barcode_demo/index.html.twig', [
            'examples' => self::EXAMPLES,
        ]);
    }

    #[Route(path: '/demo/barcode/{slug}', name: 'app_demo_barcode', methods: ['GET', 'POST'], requirements: ['slug' => '[a-z0-9\-]+'])]
    public function barcode(Request $request, string $slug): Response
    {
        if (!isset(self::EXAMPLES[$slug])) {
            throw $this->createNotFoundException(sprintf('Unknown demo: %s', $slug));
        }

        $cfg  = self::EXAMPLES[$slug];
        $form = $this->createForm(BarcodeDemoType::class, ['barcode' => ''], [
            'enable_scanner' => $cfg['enable_scanner'],
            'max_length'     => $cfg['max_length'],
            'formats'        => $cfg['formats'],
            'barcode_help'   => $cfg['help'],
        ]);

        $form->handleRequest($request);

        $barcodeValue = null;
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var array{barcode: string} $data */
            $data         = $form->getData();
            $barcodeValue = $data['barcode'];
        }

        return $this->render('barcode_demo/show.html.twig', [
            'form'          => $form,
            'barcode_value' => $barcodeValue,
            'demo_title'    => $cfg['title'],
            'demo_slug'     => $slug,
            'examples'      => self::EXAMPLES,
        ]);
    }
}
