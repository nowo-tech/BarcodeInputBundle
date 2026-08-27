import { defineConfig } from 'vite';

export default defineConfig({
  define: {
    __BARCODE_INPUT_BUILD_TIME__: JSON.stringify(new Date().toISOString()),
  },
  build: {
    outDir: 'src/Resources/public',
    emptyOutDir: false,
    rollupOptions: {
      input: 'src/Resources/assets/src/barcode-input.ts',
      output: {
        format: 'iife',
        entryFileNames: 'barcode-input.js',
        assetFileNames: 'barcode-input.[ext]',
      },
    },
    minify: true,
    sourcemap: false,
  },
});
