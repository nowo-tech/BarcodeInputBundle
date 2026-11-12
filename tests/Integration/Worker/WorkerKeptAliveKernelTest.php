<?php

declare(strict_types=1);

namespace Nowo\BarcodeInputBundle\Tests\Integration\Worker;

use Nowo\BarcodeInputBundle\Tests\Fixtures\Worker\WorkerHostKernel;
use PHPUnit\Framework\Attributes\CoversNothing;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\TerminableInterface;

use function gc_collect_cycles;
use function memory_get_usage;
use function sprintf;

/**
 * REQ-RUNTIME-002: ONE kept-alive kernel (FrankenPHP worker mode, kernel reset false). The `services_resetter` and
 * `kernel.reset` are never called. A host form with an `BarcodeType` field is rendered and submitted through
 * `http_kernel` (handle + terminate) N times, alternating two inputs with different options (formats, max length, scanner, trimming) and locales.
 * Each response must contain only its own data and memory must stay flat.
 */
#[CoversNothing]
final class WorkerKeptAliveKernelTest extends TestCase
{
    private const ITERATIONS = 1000;

    private const WARM_UP = 100;

    private const MEMORY_MARGIN_BYTES = 4 * 1024 * 1024;

    public function testFormRenderAndSubmitInOneKernelWithoutResetCarriesNoStateAndKeepsMemoryFlat(): void
    {
        $kernel = new WorkerHostKernel();
        $kernel->boot();
        $http = $kernel->getContainer()->get('http_kernel');
        self::assertInstanceOf(HttpKernelInterface::class, $http);
        self::assertInstanceOf(TerminableInterface::class, $http);

        $inputs = [
            'alpha' => ['locale' => 'en', 'code' => '4006381333931', 'formats' => 'ean_13', 'max' => '13', 'scanner' => 1],
            'beta'  => ['locale' => 'es', 'code' => 'ABC-123', 'formats' => 'code_128,code_39', 'max' => '20', 'scanner' => 0],
        ];

        $baseline = 0;
        for ($i = 0; $i < self::ITERATIONS; ++$i) {
            if ($i === self::WARM_UP) {
                gc_collect_cycles();
                $baseline = memory_get_usage();
            }

            $who   = $i % 2 === 0 ? 'alpha' : 'beta';
            $other = $who === 'alpha' ? 'beta' : 'alpha';
            $in    = $inputs[$who];

            // Render (GET) and submit (POST) the same host form.
            foreach ([Request::METHOD_GET, Request::METHOD_POST] as $method) {
                $request = Request::create('/host/' . $who, $method, $method === Request::METHOD_POST ? ['f' => ['code' => $in['code']]] : []);
                $request->setLocale($in['locale']);
                $response = $http->handle($request);
                $body     = (string) $response->getContent();
                $http->terminate($request, $response);

                $ctx = sprintf('iteration %d, %s %s', $i, $who, $method);
                self::assertSame(Response::HTTP_OK, $response->getStatusCode(), $ctx);
                self::assertStringContainsString('who=' . $who . ';', $body, $ctx);
                self::assertStringNotContainsString('who=' . $other . ';', $body, $ctx);
                self::assertStringContainsString('data-nowo-barcode-input-formats-value="' . $in['formats'] . '"', $body, $ctx);
                self::assertStringNotContainsString('data-nowo-barcode-input-formats-value="' . $inputs[$other]['formats'] . '"', $body, $ctx);
                self::assertStringContainsString('maxlength="' . $in['max'] . '"', $body, $ctx);
                self::assertStringNotContainsString('maxlength="' . $inputs[$other]['max'] . '"', $body, $ctx);
                self::assertSame($in['scanner'], substr_count($body, 'data-nowo-barcode-scan-trigger'), $ctx);
                if ($method === Request::METHOD_POST) {
                    self::assertStringContainsString('submitted=[' . $in['code'] . '];', $body, $ctx);
                    self::assertStringContainsString('value="' . $in['code'] . '"', $body, $ctx);
                    self::assertStringNotContainsString($inputs[$other]['code'], $body, $ctx);
                } else {
                    self::assertStringContainsString('submitted=[];', $body, $ctx);
                }
            }
        }

        gc_collect_cycles();
        $growth = memory_get_usage() - $baseline;
        self::assertLessThan(self::MEMORY_MARGIN_BYTES, $growth, sprintf('memory grew by %d bytes over %d iterations', $growth, self::ITERATIONS - self::WARM_UP));

        $kernel->shutdown();
    }
}
