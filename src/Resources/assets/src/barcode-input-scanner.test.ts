import { afterEach, describe, expect, it, vi } from 'vitest';

const zxing = vi.hoisted(() => ({
  decodeFromConstraints: vi.fn(),
  reset: vi.fn(),
  stop: vi.fn(),
}));

vi.mock('@zxing/browser', () => ({
  BrowserMultiFormatReader: vi.fn().mockImplementation(function () {
    return {
      decodeFromConstraints: zxing.decodeFromConstraints,
      reset: zxing.reset,
    };
  }),
}));

import {
  getLogger,
  initBarcodeContainer,
  runInitAndObserve,
  setBundleLogger,
  startCameraScanner,
  stopActiveScanner,
  stopObserving,
} from './barcode-input-lib';
import type { BundleLogger } from './logger';

type DecodeCallback = (result: { getText: () => string } | undefined) => void;

const flush = async (): Promise<void> => {
  await new Promise((resolve) => {
    setTimeout(resolve, 0);
  });
};

const scannerMarkup = (facingAttr: string): string => `
  <nowo-barcode-input data-nowo-barcode-container="1" data-nowo-barcode-enable-scanner="1" ${facingAttr}>
    <input data-controller="nowo-barcode-input" data-nowo-barcode-input-max-length-value="8" />
    <button type="button" data-nowo-barcode-scan-trigger>Scan</button>
    <div hidden data-nowo-barcode-scanner>
      <video data-nowo-barcode-video></video>
    </div>
  </nowo-barcode-input>
`;

const spyLogger = (): BundleLogger & { warn: ReturnType<typeof vi.fn> } => ({
  scriptLoaded: vi.fn(),
  setDebug: vi.fn(),
  debug: vi.fn(),
  info: vi.fn(),
  warn: vi.fn(),
  error: vi.fn(),
});

describe('barcode-input-lib camera scanner', () => {
  afterEach(async () => {
    await stopActiveScanner();
    stopObserving();
    document.body.innerHTML = '';
    vi.clearAllMocks();
  });

  it('getLogger returns the injected logger', () => {
    const logger = spyLogger();
    setBundleLogger(logger);
    expect(getLogger()).toBe(logger);
  });

  it('fills the input with the normalized scan result and closes the overlay', async () => {
    let callback: DecodeCallback = () => undefined;
    zxing.decodeFromConstraints.mockImplementation(async (_constraints: unknown, _video: unknown, cb: DecodeCallback) => {
      callback = cb;
      return { stop: zxing.stop };
    });
    document.body.innerHTML = scannerMarkup('');
    const container = document.querySelector('nowo-barcode-input') as HTMLElement;
    const input = container.querySelector('input') as HTMLInputElement;
    const overlay = container.querySelector('[data-nowo-barcode-scanner]') as HTMLElement;
    const video = container.querySelector('video') as HTMLVideoElement;

    initBarcodeContainer(container);
    (container.querySelector('[data-nowo-barcode-scan-trigger]') as HTMLButtonElement).click();
    await flush();
    expect(overlay.hidden).toBe(false);
    expect(zxing.decodeFromConstraints.mock.calls[0][0]).toEqual({ video: { facingMode: 'environment' } });

    callback(undefined);
    expect(input.value).toBe('');

    callback({ getText: () => ' 4017072512345 ' });
    await flush();

    expect(input.value).toBe('40170725');
    expect(overlay.hidden).toBe(true);
    expect(video.srcObject).toBeNull();
    expect(zxing.stop).toHaveBeenCalledTimes(1);
    expect(zxing.reset).toHaveBeenCalledTimes(1);
  });

  it('logs and closes the overlay when the camera cannot start', async () => {
    const logger = spyLogger();
    setBundleLogger(logger);
    zxing.decodeFromConstraints.mockRejectedValue(new Error('NotAllowedError'));
    document.body.innerHTML = scannerMarkup('');
    const container = document.querySelector('nowo-barcode-input') as HTMLElement;
    const overlay = container.querySelector('[data-nowo-barcode-scanner]') as HTMLElement;

    initBarcodeContainer(container);
    (container.querySelector('[data-nowo-barcode-scan-trigger]') as HTMLButtonElement).click();
    await flush();

    expect(logger.warn).toHaveBeenCalledWith('camera scanner failed', { error: 'Error: NotAllowedError' });
    expect(overlay.hidden).toBe(true);
  });

  it('uses the container facing mode and the front camera when requested', async () => {
    zxing.decodeFromConstraints.mockResolvedValue({ stop: zxing.stop });
    const video = document.createElement('video');

    await startCameraScanner(video, () => undefined, 'user');
    expect(zxing.decodeFromConstraints.mock.calls[0][0]).toEqual({ video: { facingMode: 'user' } });

    document.body.innerHTML = scannerMarkup('data-nowo-barcode-facing-mode="user"');
    const container = document.querySelector('nowo-barcode-input') as HTMLElement;
    initBarcodeContainer(container);
    (container.querySelector('[data-nowo-barcode-scan-trigger]') as HTMLButtonElement).click();
    await flush();
    expect(zxing.decodeFromConstraints.mock.calls[1][0]).toEqual({ video: { facingMode: 'user' } });
  });

  it('normalizes on blur and ignores a second init of the same container', async () => {
    document.body.innerHTML = `
      <nowo-barcode-input data-nowo-barcode-container="1">
        <input data-controller="nowo-barcode-input" />
      </nowo-barcode-input>
    `;
    const container = document.querySelector('nowo-barcode-input') as HTMLElement;
    const input = container.querySelector('input') as HTMLInputElement;

    initBarcodeContainer(container);
    initBarcodeContainer(container);
    input.value = ' abc ';
    input.dispatchEvent(new Event('blur'));

    expect(input.value).toBe('abc');
    expect(container.getAttribute('data-nowo-barcode-init')).toBe('1');
  });

  it('initializes containers added to the DOM after runInitAndObserve', async () => {
    runInitAndObserve();
    runInitAndObserve();

    const wrapper = document.createElement('div');
    wrapper.innerHTML = '<nowo-barcode-input data-nowo-barcode-container="1"><input data-controller="nowo-barcode-input" /></nowo-barcode-input>';
    document.body.appendChild(wrapper);
    document.body.appendChild(document.createTextNode('text node'));
    await flush();

    expect(wrapper.querySelector('nowo-barcode-input')?.getAttribute('data-nowo-barcode-init')).toBe('1');
  });
});
