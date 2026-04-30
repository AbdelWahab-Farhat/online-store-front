<template>
  <footer class="footer">
    <div class="container">
      <div class="footer-grid">
        <!-- Brand -->
        <div class="footer-section">
          <div class="footer-logo">
            <div class="footer-logo-avatar">
              <img src="/images/new-logo.jpeg" alt="متجر كيان" class="footer-logo-img" />
            </div>
            <span>{{ getSetting('store_name', 'متجر كيان') }}</span>
          </div>
          <p class="footer-desc">
            وجهتك الأولى لأحدث صيحات الموضة والجمال. منتجات أصلية بأسعار منافسة وتوصيل سريع.
          </p>
        </div>

        <!-- Links -->
        <div class="footer-section">
          <h4 class="footer-title">روابط سريعة</h4>
          <ul class="footer-links">
            <li><NuxtLink to="/">الرئيسية</NuxtLink></li>
            <li><NuxtLink to="/products">المنتجات</NuxtLink></li>
          </ul>
        </div>

        <!-- Contact -->
        <div class="footer-section">
          <h4 class="footer-title">تواصلي معنا</h4>
          <ul class="footer-links">
            <li v-if="getSetting('contact_email')">
              <Icon name="mdi:email-outline" />
              <span>{{ getSetting('contact_email') }}</span>
            </li>
            <li v-else>
              <Icon name="mdi:email-outline" />
              <span>info@anaqah.store</span>
            </li>

            <li v-if="getSetting('contact_phone')">
              <Icon name="mdi:phone-outline" />
              <span dir="ltr">{{ getSetting('contact_phone') }}</span>
            </li>
            <li v-else>
              <Icon name="mdi:phone-outline" />
              <span dir="ltr">+218 91 234 5678</span>
            </li>

            <li v-if="getSetting('location')">
              <Icon name="mdi:map-marker-outline" />
              <span>{{ getSetting('location') }}</span>
            </li>
            <li v-else>
              <Icon name="mdi:map-marker-outline" />
              <span>طرابلس، ليبيا</span>
            </li>
          </ul>
        </div>

        <!-- Social -->
        <div class="footer-section">
          <h4 class="footer-title">تابعينا</h4>
          <div class="footer-social">
            <a v-if="getSetting('instagram_url')" :href="getSetting('instagram_url')" target="_blank" aria-label="Instagram">
              <Icon name="mdi:instagram" />
            </a>
            <a v-if="getSetting('facebook_url')" :href="getSetting('facebook_url')" target="_blank" aria-label="Facebook">
              <Icon name="mdi:facebook" />
            </a>
            <a v-if="getSetting('whatsapp_number')" :href="'https://wa.me/' + getSetting('whatsapp_number').replace(/\D/g,'')" target="_blank" aria-label="WhatsApp">
              <Icon name="mdi:whatsapp" />
            </a>
          </div>
        </div>
      </div>

      <div class="footer-bottom">
        <p>© {{ new Date().getFullYear() }} {{ getSetting('store_name', 'متجر كيان') }}. جميع الحقوق محفوظة.</p>
      </div>
    </div>
  </footer>
</template>

<script setup lang="ts">
const { $axios } = useNuxtApp()

const { data: settings } = await useAsyncData('app-settings-footer', async () => {
  try {
    const res = await $axios.get('/settings')
    return res.data.data
  } catch (e) {
    return []
  }
})

function getSetting(key: string, fallback: string = '') {
  if (!settings.value) return fallback
  const item = settings.value.find((s: any) => s.key === key)
  return item?.value || fallback
}
</script>

<style scoped>
.footer {
  background: var(--color-bg-footer);
  color: rgba(255, 255, 255, 0.75);
  padding: var(--space-3xl) 0 var(--space-lg);
  margin-top: var(--space-3xl);
}

.footer-grid {
  display: grid;
  grid-template-columns: 2fr 1fr 1fr 1fr;
  gap: var(--space-2xl);
}

.footer-logo {
  display: flex;
  align-items: center;
  gap: var(--space-sm);
  font-size: var(--font-size-xl);
  font-weight: 800;
  color: var(--color-primary-light);
  margin-bottom: var(--space-md);
}

.footer-logo-avatar {
  width: 44px;
  height: 44px;
  border-radius: 50%;
  overflow: hidden;
  border: 2px solid var(--color-primary);
  flex-shrink: 0;
}

.footer-logo-img {
  width: 100%;
  height: 100%;
  object-fit: cover;
}

.footer-desc {
  font-size: var(--font-size-sm);
  line-height: 1.8;
  max-width: 280px;
}

.footer-title {
  color: #ffffff;
  font-size: var(--font-size-base);
  font-weight: 700;
  margin-bottom: var(--space-md);
}

.footer-links {
  display: flex;
  flex-direction: column;
  gap: var(--space-sm);
}

.footer-links li {
  display: flex;
  align-items: center;
  gap: var(--space-sm);
  font-size: var(--font-size-sm);
}

.footer-links a {
  color: rgba(255, 255, 255, 0.75);
  transition: color var(--transition-fast);
}

.footer-links a:hover {
  color: var(--color-primary-light);
}

.footer-social {
  display: flex;
  gap: var(--space-md);
}

.footer-social a {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 40px;
  height: 40px;
  border-radius: var(--radius-full);
  background: rgba(255, 255, 255, 0.08);
  color: rgba(255, 255, 255, 0.75);
  font-size: 1.2rem;
  transition: all var(--transition-base);
}

.footer-social a:hover {
  background: var(--color-primary);
  color: #fff;
  transform: translateY(-3px);
}

.footer-bottom {
  margin-top: var(--space-2xl);
  padding-top: var(--space-lg);
  border-top: 1px solid rgba(255, 255, 255, 0.08);
  text-align: center;
  font-size: var(--font-size-sm);
}

@media (max-width: 768px) {
  .footer-grid {
    grid-template-columns: 1fr 1fr;
    gap: var(--space-xl);
  }
}

@media (max-width: 480px) {
  .footer-grid {
    grid-template-columns: 1fr;
  }
}
</style>
