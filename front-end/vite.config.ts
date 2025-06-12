import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import path from 'node:path'


// https://vite.dev/config/
export default defineConfig({
  plugins: [vue()],
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './src'),
    }
  },
  build: {
    rollupOptions: {
      input: {
        main: path.resolve(__dirname, 'index.html'),
        form: path.resolve(__dirname, 'form.html'),
      },
      output: {
        entryFileNames: ({ name }) => `assets/${name}/[name].js`,
        chunkFileNames: 'assets/[name].js',
        assetFileNames: 'assets/[name][extname]',
        format: 'es',
      }
    },
    emptyOutDir: true,
  }
})
