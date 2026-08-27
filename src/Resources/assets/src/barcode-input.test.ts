import { describe, expect, it } from 'vitest';

describe('barcode-input entry', () => {
  it('loads without throwing', async () => {
    await expect(import('./barcode-input')).resolves.toBeDefined();
  });
});
