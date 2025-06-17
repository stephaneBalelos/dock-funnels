import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import path from 'node:path'
import Components from 'unplugin-vue-components/vite';
import { PrimeVueResolver } from '@primevue/auto-import-resolver';


// https://vite.dev/config/
export default defineConfig({
  plugins: [vue(),
  Components({
    resolvers: [
      PrimeVueResolver()
    ]
  })
  ],
  resolve: {
    alias: {
      '@': path.resolve(__dirname, './src'),
    }
  },
  build: {
    lib: {
      entry: path.resolve(__dirname, './src/dashboard/index.ts'),
      name: 'DockFunnelsDashboard',
      fileName: (format, entryName) => {
        return `assets/dashboard/${entryName}.${format}.js`;
      },
      formats: ['iife'], // Use IIFE format for compatibility with WordPress
    },
    rollupOptions: {
      external: ['vue'],
      output: {
        assetFileNames: 'assets/dashboard/[name].[ext]', // Ensure assets are placed in the correct directory
        globals: {
          vue: 'Vue', // Global variable for Vue
        },
      }
    },
    emptyOutDir: false, // Prevent Vite from emptying the output directory
  }
})
