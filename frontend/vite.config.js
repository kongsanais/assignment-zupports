import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

export default defineConfig({
  plugins: [vue()],
  optimizeDeps: {
    /*
     Author : Hatsanai
     Date : 2023-10-27
     Description  : Add to Debug error 
    */
    include: [
      "@fawmi/vue-google-maps",
      "fast-deep-equal",
    ],
  },
})
