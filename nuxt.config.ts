// https://nuxt.com/docs/api/configuration/nuxt-config
export default defineNuxtConfig({
  compatibilityDate: '2025-07-15',
  devtools: { enabled: true },

  modules: ['@nuxt/icon', '@nuxt/image', '@nuxt/fonts'],

  fonts: {
    families: [
      { name: 'Cairo', provider: 'google', weights: [400, 600, 700, 800] },
    ],
  },

  css: ['~/assets/css/main.css'],

  app: {
    head: {
      htmlAttrs: { lang: 'ar', dir: 'rtl' },
      title: 'أناقة — متجر المرأة العصرية',
      meta: [
        { charset: 'utf-8' },
        { name: 'viewport', content: 'width=device-width, initial-scale=1' },
        {
          name: 'description',
          content:
            'أناقة — وجهتك الأولى لأحدث صيحات الموضة والجمال للمرأة العصرية. تسوّقي الآن!',
        },
      ],
      link: [{ rel: 'icon', type: 'image/x-icon', href: '/favicon.ico' }],
    },
  },
})
