import { defineConfig } from 'vite'
import react from '@vitejs/plugin-react'
import path from 'node:path'
import { fileURLToPath } from 'node:url'

const __dirname = path.dirname(fileURLToPath(import.meta.url))

// https://vitejs.dev/config/
export default defineConfig(({ command, mode }) => {
  const isWordPressBuild = mode === 'wordpress' || process.env.BUILD_TARGET === 'wordpress'
  
  // Режим разработки или обычная сборка - стандартный React режим
  if (command === 'serve' || !isWordPressBuild) {
    return {
      plugins: [react()],
      resolve: {
        alias: {
          "@": path.resolve(__dirname, "./src"),
        },
      },
      server: {
        proxy: {
          '/wp-json/dmc/v1/filter': {
            target: 'https://kubiki.ai',
            changeOrigin: true,
            secure: true,
          },
        },
      },
    }
  }
  
  // Режим сборки для WordPress - библиотека в IIFE формате
  return {
    plugins: [react()],
    define: {
      'process.env': '{}',
      'process.env.NODE_ENV': JSON.stringify('production'),
    },
    build: {
      lib: {
        entry: path.resolve(__dirname, 'src/main.tsx'),
        name: 'InssmartForm',
        fileName: () => `inssmart-form.js`,
        formats: ['iife']
      },
      rollupOptions: {
        output: {
          inlineDynamicImports: true,
          assetFileNames: (assetInfo) => {
            if (assetInfo.name === 'style.css') {
              return 'inssmart-form.css'
            }
            // Сохраняем шрифты в папку fonts
            if (assetInfo.name && assetInfo.name.endsWith('.ttf')) {
              return 'fonts/[name][extname]'
            }
            return assetInfo.name || 'asset'
          },
          // Убеждаемся, что функция доступна глобально
          globals: {}
        },
        external: []
      },
      outDir: 'dist',
      emptyOutDir: true,
      cssCodeSplit: false
    },
    resolve: {
      alias: {
        "@": path.resolve(__dirname, "./src"),
      },
    },
  }
})

