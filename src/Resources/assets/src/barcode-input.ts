/**
 * Barcode input standalone entry.
 * Defines the `nowo-barcode-input` custom element and auto-inits hosts on DOM ready.
 */

import { createBundleLogger } from './logger';
import { ensureNowoBarcodeInputDefined } from './nowo-barcode-input-element';
import {
  getLogger,
  initBarcodeContainer,
  runInit,
  runInitAndObserve,
  setBundleLogger,
  stopObserving,
} from './barcode-input-lib';

import './barcode-input.css';

ensureNowoBarcodeInputDefined();

declare const __BARCODE_INPUT_BUILD_TIME__: string;

const log = createBundleLogger('barcode-input', {
  buildTime: typeof __BARCODE_INPUT_BUILD_TIME__ !== 'undefined' ? __BARCODE_INPUT_BUILD_TIME__ : undefined,
});
log.scriptLoaded();
setBundleLogger(log);

if (typeof window !== 'undefined') {
  getLogger().debug('standalone entry: exposing NowoBarcodeInput on window');
  (window as unknown as {
    NowoBarcodeInput?: {
      initBarcodeContainer: typeof initBarcodeContainer;
      runInit: typeof runInit;
      runInitAndObserve: typeof runInitAndObserve;
      stopObserving: typeof stopObserving;
    };
  }).NowoBarcodeInput = {
    initBarcodeContainer,
    runInit,
    runInitAndObserve,
    stopObserving,
  };
}

if (document.readyState === 'loading') {
  getLogger().debug('standalone entry: DOM loading, scheduling runInitAndObserve on DOMContentLoaded');
  document.addEventListener('DOMContentLoaded', () => {
    runInitAndObserve();
  });
} else {
  getLogger().debug('standalone entry: DOM ready, running runInitAndObserve now');
  runInitAndObserve();
}
