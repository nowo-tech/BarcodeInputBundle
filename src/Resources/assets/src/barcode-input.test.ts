import { afterEach, describe, expect, it, vi } from 'vitest';

type NowoBarcodeInputGlobal = { runInit: (root?: ParentNode) => number };

describe('barcode-input entry', () => {
  afterEach(() => {
    vi.restoreAllMocks();
    vi.resetModules();
  });

  it('exposes the API on window and initializes immediately when the DOM is ready', async () => {
    vi.spyOn(console, 'log').mockImplementation(() => undefined);
    document.body.innerHTML = '<div data-nowo-barcode-container="1"><input data-controller="nowo-barcode-input" /></div>';

    await import('./barcode-input');

    const api = (window as unknown as { NowoBarcodeInput?: NowoBarcodeInputGlobal }).NowoBarcodeInput;
    expect(typeof api?.runInit).toBe('function');
    expect(document.querySelector('[data-nowo-barcode-container]')?.getAttribute('data-nowo-barcode-init')).toBe('1');
    (await import('./barcode-input-lib')).stopObserving();
  });

  it('defers initialization to DOMContentLoaded while the document is loading', async () => {
    vi.spyOn(console, 'log').mockImplementation(() => undefined);
    vi.spyOn(document, 'readyState', 'get').mockReturnValue('loading');
    document.body.innerHTML = '<div data-nowo-barcode-container="1"><input data-controller="nowo-barcode-input" /></div>';
    const host = document.querySelector('[data-nowo-barcode-container]') as HTMLElement;

    await import('./barcode-input');
    expect(host.getAttribute('data-nowo-barcode-init')).toBeNull();

    document.dispatchEvent(new Event('DOMContentLoaded'));
    expect(host.getAttribute('data-nowo-barcode-init')).toBe('1');
    (await import('./barcode-input-lib')).stopObserving();
  });
});
