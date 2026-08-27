/**
 * Barcode input library shared by the custom element and the standalone IIFE.
 */

import { createBundleLogger } from './logger';
import type { BundleLogger } from './logger';

export const TAG_NOWO_BARCODE_INPUT = 'nowo-barcode-input';
export const ATTR_INIT = 'data-nowo-barcode-init';
export const HOST_SELECTOR = `${TAG_NOWO_BARCODE_INPUT}, [data-nowo-barcode-container="1"]`;

export type BarcodeContainer = HTMLElement & {
  dataset: DOMStringMap;
};

let bundleLogger: BundleLogger | null = null;
let activeReader: { stop: () => Promise<void> } | null = null;

export function setBundleLogger(logger: BundleLogger): void {
  bundleLogger = logger;
}

export function getLogger(): BundleLogger {
  if (bundleLogger !== null) {
    return bundleLogger;
  }

  return createBundleLogger('barcode-input');
}

export function toBool(value: string | undefined): boolean {
  return value === '1' || value === 'true';
}

export function parseFormats(raw: string | undefined): string[] {
  if (!raw || raw.trim() === '') {
    return ['ean_13', 'ean_8', 'code_128', 'code_39', 'upc_a'];
  }

  return raw.split(',').map((item) => item.trim()).filter((item) => item !== '');
}

export function normalizeBarcode(value: string, maxLength: number): string {
  const trimmed = value.trim().replace(/[^\x20-\x7E]/g, '');

  if (maxLength > 0 && trimmed.length > maxLength) {
    return trimmed.slice(0, maxLength);
  }

  return trimmed;
}

export function setInputValue(input: HTMLInputElement, value: string): void {
  input.value = value;
  input.dispatchEvent(new Event('input', { bubbles: true }));
  input.dispatchEvent(new Event('change', { bubbles: true }));
}

export async function stopActiveScanner(): Promise<void> {
  if (activeReader !== null) {
    await activeReader.stop();
    activeReader = null;
  }
}

export async function startCameraScanner(
  video: HTMLVideoElement,
  onDetected: (value: string) => void,
  facingMode: string,
): Promise<{ stop: () => Promise<void> }> {
  const { BrowserMultiFormatReader } = await import('@zxing/browser');

  const reader = new BrowserMultiFormatReader();
  const controls = await reader.decodeFromConstraints(
    {
      video: {
        facingMode: facingMode === 'user' ? 'user' : 'environment',
      },
    },
    video,
    (result) => {
      if (result) {
        onDetected(result.getText());
      }
    },
  );

  const stop = async (): Promise<void> => {
    controls.stop();
    reader.reset();
  };

  activeReader = { stop };

  return { stop };
}

export function initBarcodeContainer(container: BarcodeContainer): void {
  if (container.getAttribute(ATTR_INIT) === '1') {
    return;
  }

  const input = container.querySelector('input[data-controller*="nowo-barcode-input"]') as HTMLInputElement | null;
  const scanTrigger = container.querySelector('[data-nowo-barcode-scan-trigger]') as HTMLButtonElement | null;
  const scannerOverlay = container.querySelector('[data-nowo-barcode-scanner]') as HTMLElement | null;
  const video = container.querySelector('[data-nowo-barcode-video]') as HTMLVideoElement | null;
  const closeButton = container.querySelector('[data-nowo-barcode-close]') as HTMLButtonElement | null;

  if (!input) {
    getLogger().warn('container skipped: barcode input not found');
    return;
  }

  container.setAttribute(ATTR_INIT, '1');

  const enableScanner = toBool(container.dataset.nowoBarcodeEnableScanner ?? input.dataset.nowoBarcodeInputEnableScannerValue);
  const maxLength = Number.parseInt(input.dataset.nowoBarcodeInputMaxLengthValue ?? '128', 10);
  const facingMode = input.dataset.nowoBarcodeInputFacingModeValue ?? container.dataset.nowoBarcodeFacingMode ?? 'environment';

  input.addEventListener('blur', () => {
    setInputValue(input, normalizeBarcode(input.value, maxLength));
  });

  const closeScanner = async (): Promise<void> => {
    await stopActiveScanner();
    if (scannerOverlay) {
      scannerOverlay.hidden = true;
    }
    if (video) {
      video.srcObject = null;
    }
  };

  if (!enableScanner || !scanTrigger || !scannerOverlay || !video) {
    return;
  }

  scanTrigger.addEventListener('click', async () => {
    scannerOverlay.hidden = false;

    try {
      await startCameraScanner(
        video,
        (value) => {
          setInputValue(input, normalizeBarcode(value, maxLength));
          void closeScanner();
        },
        facingMode,
      );
    } catch (error) {
      getLogger().warn('camera scanner failed', { error: String(error) });
      await closeScanner();
    }
  });

  closeButton?.addEventListener('click', () => {
    void closeScanner();
  });
}

export function runInit(root: ParentNode = document): number {
  const containers = Array.from(root.querySelectorAll<BarcodeContainer>(HOST_SELECTOR));
  containers.forEach((container) => {
    initBarcodeContainer(container);
  });

  return containers.length;
}

let observer: MutationObserver | null = null;

export function runInitAndObserve(root: ParentNode = document): void {
  runInit(root);

  if (observer !== null || typeof MutationObserver === 'undefined') {
    return;
  }

  observer = new MutationObserver((mutations) => {
    mutations.forEach((mutation) => {
      mutation.addedNodes.forEach((node) => {
        if (node instanceof HTMLElement) {
          runInit(node);
        }
      });
    });
  });

  observer.observe(document.documentElement, { childList: true, subtree: true });
}

export function stopObserving(): void {
  observer?.disconnect();
  observer = null;
}
