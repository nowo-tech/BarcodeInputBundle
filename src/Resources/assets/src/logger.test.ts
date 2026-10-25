import { afterEach, describe, expect, it, vi } from 'vitest';
import { createBundleLogger } from './logger';

describe('createBundleLogger', () => {
  afterEach(() => {
    vi.restoreAllMocks();
  });

  it('prints the script-loaded line with and without build id', () => {
    const log = vi.spyOn(console, 'log').mockImplementation(() => undefined);

    createBundleLogger('barcode-input', { buildTime: '2026-10-20' }).scriptLoaded();
    createBundleLogger('barcode-input', { buildTime: '' }).scriptLoaded();
    createBundleLogger('barcode-input').scriptLoaded();

    expect(log).toHaveBeenCalledTimes(3);
    expect(String(log.mock.calls[0][0])).toContain('build: ');
    expect(String(log.mock.calls[1][0])).not.toContain('build: ');
  });

  it('stays silent until debug is enabled, then formats object arguments', () => {
    const spies = {
      debug: vi.spyOn(console, 'debug').mockImplementation(() => undefined),
      info: vi.spyOn(console, 'info').mockImplementation(() => undefined),
      warn: vi.spyOn(console, 'warn').mockImplementation(() => undefined),
      error: vi.spyOn(console, 'error').mockImplementation(() => undefined),
    };
    const logger = createBundleLogger('barcode-input');

    logger.debug('a');
    logger.info('a');
    logger.warn('a');
    logger.error('a');
    Object.values(spies).forEach((spy) => expect(spy).not.toHaveBeenCalled());

    logger.setDebug(true);
    const failure = new Error('boom');
    logger.debug('d', { k: 1 });
    logger.info('i', null);
    logger.warn('w', failure);
    logger.error('e');

    expect(spies.debug.mock.calls[0].slice(2)).toEqual(['d', '{"k":1}']);
    expect(spies.info.mock.calls[0].slice(2)).toEqual(['i', null]);
    expect(spies.warn.mock.calls[0].slice(2)).toEqual(['w', failure]);
    expect(spies.error).toHaveBeenCalledTimes(1);
  });
});
