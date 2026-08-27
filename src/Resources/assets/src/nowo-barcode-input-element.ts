import {
  ATTR_INIT,
  initBarcodeContainer,
  TAG_NOWO_BARCODE_INPUT,
  type BarcodeContainer,
} from './barcode-input-lib';

let defined = false;

export function ensureNowoBarcodeInputDefined(): void {
  if (defined || typeof customElements === 'undefined') {
    return;
  }

  if (!customElements.get(TAG_NOWO_BARCODE_INPUT)) {
    class NowoBarcodeInputElement extends HTMLElement {
      connectedCallback(): void {
        initBarcodeContainer(this as BarcodeContainer);
      }

      disconnectedCallback(): void {
        this.removeAttribute(ATTR_INIT);
      }
    }

    customElements.define(TAG_NOWO_BARCODE_INPUT, NowoBarcodeInputElement);
  }

  defined = true;
}
