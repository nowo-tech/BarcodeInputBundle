import { afterEach, beforeEach, describe, expect, it, vi } from 'vitest';
import {
  HOST_SELECTOR,
  initBarcodeContainer,
  normalizeBarcode,
  parseFormats,
  runInit,
  runInitAndObserve,
  setInputValue,
  stopActiveScanner,
  stopObserving,
  toBool,
} from './barcode-input-lib';

vi.mock('@zxing/browser', () => ({
  BrowserMultiFormatReader: vi.fn().mockImplementation(() => ({
    decodeFromConstraints: vi.fn().mockResolvedValue({
      stop: vi.fn(),
    }),
    reset: vi.fn(),
  })),
}));

describe('barcode-input-lib helpers', () => {
  afterEach(() => {
    stopObserving();
    document.body.innerHTML = '';
    vi.clearAllMocks();
  });

  it('toBool parses truthy flags', () => {
    expect(toBool('1')).toBe(true);
    expect(toBool('true')).toBe(true);
    expect(toBool('0')).toBe(false);
  });

  it('parseFormats returns defaults when empty', () => {
    expect(parseFormats('')).toContain('ean_13');
    expect(parseFormats('code_39,ean_8')).toEqual(['code_39', 'ean_8']);
  });

  it('normalizeBarcode trims and truncates', () => {
    expect(normalizeBarcode('  40170725  ', 13)).toBe('40170725');
    expect(normalizeBarcode('1234567890123', 8)).toBe('12345678');
    expect(normalizeBarcode('40170725\x00', 13)).toBe('40170725');
  });

  it('setInputValue updates input and dispatches events', () => {
    document.body.innerHTML = '<input id="barcode" value="" />';
    const input = document.getElementById('barcode') as HTMLInputElement;
    const events: string[] = [];
    input.addEventListener('input', () => events.push('input'));
    input.addEventListener('change', () => events.push('change'));

    setInputValue(input, '40170725');

    expect(input.value).toBe('40170725');
    expect(events).toEqual(['input', 'change']);
  });

  it('does not mark incomplete hosts as initialized', () => {
    const host = document.createElement('nowo-barcode-input');
    host.setAttribute('data-nowo-barcode-container', '1');
    initBarcodeContainer(host);
    expect(host.getAttribute('data-nowo-barcode-init')).toBeNull();
    expect(HOST_SELECTOR).toContain('nowo-barcode-input');
  });

  it('normalizes value on blur', () => {
    document.body.innerHTML = `
      <nowo-barcode-input data-nowo-barcode-container="1">
        <input data-controller="nowo-barcode-input" data-nowo-barcode-input-max-length-value="13" value="" />
      </nowo-barcode-input>
    `;
    const container = document.querySelector('nowo-barcode-input') as HTMLElement;
    const input = container.querySelector('input') as HTMLInputElement;

    initBarcodeContainer(container);
    input.value = '  40170725  ';
    input.dispatchEvent(new Event('blur'));

    expect(input.value).toBe('40170725');
  });

  it('runInit initializes matching containers', () => {
    document.body.innerHTML = `
      <nowo-barcode-input data-nowo-barcode-container="1" data-nowo-barcode-enable-scanner="0">
        <input data-controller="nowo-barcode-input" data-nowo-barcode-input-max-length-value="13" />
      </nowo-barcode-input>
    `;

    expect(runInit()).toBe(1);
    expect(document.querySelector('nowo-barcode-input')?.getAttribute('data-nowo-barcode-init')).toBe('1');
  });

  it('runInitAndObserve and stopObserving are safe to call', () => {
    expect(() => runInitAndObserve()).not.toThrow();
    expect(() => stopObserving()).not.toThrow();
  });

  it('opens and closes camera scanner when enabled', async () => {
    document.body.innerHTML = `
      <nowo-barcode-input data-nowo-barcode-container="1" data-nowo-barcode-enable-scanner="1" data-nowo-barcode-facing-mode="environment">
        <div class="nowo-barcode-input__row">
          <input data-controller="nowo-barcode-input" data-nowo-barcode-input-max-length-value="13" data-nowo-barcode-input-enable-scanner-value="1" data-nowo-barcode-input-facing-mode-value="environment" />
          <button type="button" data-nowo-barcode-scan-trigger>Scan</button>
        </div>
        <div hidden data-nowo-barcode-scanner>
          <video data-nowo-barcode-video></video>
          <button type="button" data-nowo-barcode-close>Close</button>
        </div>
      </nowo-barcode-input>
    `;

    const container = document.querySelector('nowo-barcode-input') as HTMLElement;
    const trigger = container.querySelector('[data-nowo-barcode-scan-trigger]') as HTMLButtonElement;
    const overlay = container.querySelector('[data-nowo-barcode-scanner]') as HTMLElement;
    const close = container.querySelector('[data-nowo-barcode-close]') as HTMLButtonElement;

    initBarcodeContainer(container);
    trigger.click();

    await new Promise((resolve) => {
      setTimeout(resolve, 0);
    });

    expect(overlay.hidden).toBe(false);

    close.click();
    await stopActiveScanner();

    expect(overlay.hidden).toBe(true);
  });
});
