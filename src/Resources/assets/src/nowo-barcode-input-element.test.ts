import { beforeEach, describe, expect, it } from 'vitest';
import { ensureNowoBarcodeInputDefined } from './nowo-barcode-input-element';
import { initBarcodeContainer, TAG_NOWO_BARCODE_INPUT } from './barcode-input-lib';

describe('nowo-barcode-input-element', () => {
  beforeEach(() => {
    document.body.innerHTML = '';
    ensureNowoBarcodeInputDefined();
  });

  it('defines the custom element once', () => {
    ensureNowoBarcodeInputDefined();
    expect(customElements.get(TAG_NOWO_BARCODE_INPUT)).toBeDefined();
  });

  it('initializes barcode container with text input', () => {
    document.body.innerHTML = `
      <nowo-barcode-input data-nowo-barcode-container="1" data-nowo-barcode-enable-scanner="0">
        <div class="nowo-barcode-input__row">
          <input data-controller="nowo-barcode-input" data-nowo-barcode-input-max-length-value="13" value="" />
        </div>
      </nowo-barcode-input>
    `;

    const container = document.querySelector('nowo-barcode-input') as HTMLElement;
    initBarcodeContainer(container);

    expect(container.getAttribute('data-nowo-barcode-init')).toBe('1');
  });

  it('initializes on connect and clears the init flag on disconnect', () => {
    const element = document.createElement(TAG_NOWO_BARCODE_INPUT);
    element.innerHTML = '<input data-controller="nowo-barcode-input" />';

    document.body.appendChild(element);
    expect(element.getAttribute('data-nowo-barcode-init')).toBe('1');

    element.remove();
    expect(element.getAttribute('data-nowo-barcode-init')).toBeNull();
  });
});
