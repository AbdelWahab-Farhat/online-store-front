<template>
  <section v-if="slides.length" class="hero-carousel" @mouseenter="pauseAutoplay" @mouseleave="resumeAutoplay">
    <!-- Slides -->
    <div class="slides-wrapper">
      <div
        v-for="(slide, index) in slides"
        :key="slide.id"
        class="slide"
        :class="{
          active: currentSlide === index,
          'slide-exit': previousSlide === index && previousSlide !== currentSlide
        }"
      >
        <img
          v-if="!slideImageErrors[slide.id]"
          :src="slide.image?.url"
          :alt="slide.title"
          class="slide-image"
          @error="slideImageErrors[slide.id] = true"
        />
        <div v-else class="slide-image slide-image-fallback"></div>
        <div class="slide-overlay"></div>
        <div class="slide-content">
          <span class="slide-badge">{{ slide.link_text || 'تشكيلة مميزة' }}</span>
          <h2 class="slide-title">{{ slide.title }}</h2>
          <p v-if="slide.description" class="slide-subtitle">{{ slide.description }}</p>
          <NuxtLink v-if="slide.link" :to="slide.link" class="slide-cta">
            {{ slide.link_text || 'تسوّقي الآن' }}
            <Icon name="mdi:arrow-left" />
          </NuxtLink>
        </div>
      </div>
    </div>

    <!-- Progress bar -->
    <div class="carousel-progress">
      <div
        v-for="(slide, index) in slides"
        :key="slide.id"
        class="progress-item"
        :class="{ active: currentSlide === index, past: index < currentSlide }"
        @click="goToSlide(index)"
      >
        <div class="progress-bar">
          <div class="progress-fill" :style="currentSlide === index ? { animationDuration: autoplayDuration + 'ms' } : {}"></div>
        </div>
        <span class="progress-label">{{ slide.title }}</span>
      </div>
    </div>

    <!-- Arrows -->
    <button class="carousel-arrow arrow-right" @click="prevSlide" aria-label="السابق">
      <Icon name="mdi:chevron-right" />
    </button>
    <button class="carousel-arrow arrow-left" @click="nextSlide" aria-label="التالي">
      <Icon name="mdi:chevron-left" />
    </button>

    <!-- Slide counter -->
    <div class="slide-counter">
      <span class="counter-current">{{ String(currentSlide + 1).padStart(2, '0') }}</span>
      <span class="counter-separator">/</span>
      <span class="counter-total">{{ String(slides.length).padStart(2, '0') }}</span>
    </div>
  </section>
</template>

<script setup lang="ts">
import { useAnnouncementsStore } from '~/stores/announcements'

const announcementsStore = useAnnouncementsStore()
const { announcements: slides } = storeToRefs(announcementsStore)

const currentSlide = ref(0)
const previousSlide = ref(0)
const slideImageErrors = reactive<Record<number, boolean>>({})
const autoplayDuration = 4000
let autoplayTimer: ReturnType<typeof setInterval> | null = null
let isPaused = false

const totalSlides = computed(() => slides.value.length)

function nextSlide() {
  if (!totalSlides.value) return
  previousSlide.value = currentSlide.value
  currentSlide.value = (currentSlide.value + 1) % totalSlides.value
  resetAutoplay()
}

function prevSlide() {
  if (!totalSlides.value) return
  previousSlide.value = currentSlide.value
  currentSlide.value = (currentSlide.value - 1 + totalSlides.value) % totalSlides.value
  resetAutoplay()
}

function goToSlide(index: number) {
  if (index === currentSlide.value) return
  previousSlide.value = currentSlide.value
  currentSlide.value = index
  resetAutoplay()
}

function startAutoplay() {
  if (!totalSlides.value) return
  autoplayTimer = setInterval(() => {
    previousSlide.value = currentSlide.value
    currentSlide.value = (currentSlide.value + 1) % totalSlides.value
  }, autoplayDuration)
}

function resetAutoplay() {
  if (autoplayTimer) clearInterval(autoplayTimer)
  if (!isPaused) startAutoplay()
}

function pauseAutoplay() {
  isPaused = true
  if (autoplayTimer) clearInterval(autoplayTimer)
}

function resumeAutoplay() {
  isPaused = false
  startAutoplay()
}

onMounted(async () => {
  await announcementsStore.fetchAnnouncements()
  startAutoplay()
})

onUnmounted(() => {
  if (autoplayTimer) clearInterval(autoplayTimer)
})
</script>

<style scoped>
.hero-carousel {
  position: relative;
  width: 100%;
  height: 520px;
  overflow: hidden;
  background: #0f0f1a;
}

.slides-wrapper {
  position: relative;
  width: 100%;
  height: 100%;
}

/* --- Slide transitions --- */
.slide {
  position: absolute;
  inset: 0;
  opacity: 0;
  pointer-events: none;
  z-index: 1;
}

.slide .slide-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transform: scale(1.08);
  transition: transform 6s ease-out, opacity 1s ease;
}

.slide-image-fallback {
  background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);
  transform: none !important;
}

.slide.active {
  opacity: 1;
  pointer-events: all;
  z-index: 3;
  animation: slideIn 0.9s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
}

.slide.active .slide-image {
  transform: scale(1);
}

.slide.slide-exit {
  z-index: 2;
  opacity: 1;
  animation: slideOut 0.9s cubic-bezier(0.25, 0.46, 0.45, 0.94) forwards;
}

@keyframes slideIn {
  from {
    opacity: 0;
    transform: scale(1.03);
  }
  to {
    opacity: 1;
    transform: scale(1);
  }
}

@keyframes slideOut {
  from {
    opacity: 1;
    transform: scale(1);
  }
  to {
    opacity: 0;
    transform: scale(0.95);
  }
}

/* --- Overlay --- */
.slide-overlay {
  position: absolute;
  inset: 0;
  background: linear-gradient(
    160deg,
    rgba(0, 0, 0, 0.1) 0%,
    rgba(0, 0, 0, 0.35) 40%,
    rgba(0, 0, 0, 0.7) 100%
  );
}

/* --- Content --- */
.slide-content {
  position: absolute;
  bottom: 80px;
  right: 60px;
  z-index: 4;
  max-width: 520px;
}

.slide-badge {
  display: inline-block;
  background: rgba(255, 255, 255, 0.15);
  backdrop-filter: blur(12px);
  border: 1px solid rgba(255, 255, 255, 0.2);
  padding: 6px 18px;
  border-radius: 20px;
  font-size: 0.8rem;
  color: #fff;
  letter-spacing: 0.5px;
  margin-bottom: 16px;
}

.slide.active .slide-badge {
  animation: contentUp 0.7s 0.2s cubic-bezier(0.16, 1, 0.3, 1) both;
}

.slide-title {
  font-size: 2.6rem;
  font-weight: 800;
  color: #fff;
  margin-bottom: 12px;
  line-height: 1.2;
  text-shadow: 0 2px 20px rgba(0, 0, 0, 0.3);
}

.slide.active .slide-title {
  animation: contentUp 0.7s 0.3s cubic-bezier(0.16, 1, 0.3, 1) both;
}

.slide-subtitle {
  font-size: 1.1rem;
  color: rgba(255, 255, 255, 0.8);
  margin-bottom: 28px;
  line-height: 1.6;
  text-shadow: 0 1px 8px rgba(0, 0, 0, 0.2);
}

.slide.active .slide-subtitle {
  animation: contentUp 0.7s 0.4s cubic-bezier(0.16, 1, 0.3, 1) both;
}

.slide-cta {
  display: inline-flex;
  align-items: center;
  gap: 10px;
  padding: 14px 36px;
  background: var(--color-primary, #c2185b);
  color: #fff;
  border-radius: 30px;
  font-size: 1rem;
  font-weight: 600;
  text-decoration: none;
  transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
  box-shadow: 0 4px 20px rgba(194, 24, 91, 0.35);
}

.slide.active .slide-cta {
  animation: contentUp 0.7s 0.5s cubic-bezier(0.16, 1, 0.3, 1) both;
}

.slide-cta:hover {
  transform: translateY(-2px);
  box-shadow: 0 6px 28px rgba(194, 24, 91, 0.5);
  background: #a3164a;
}

@keyframes contentUp {
  from {
    opacity: 0;
    transform: translateY(30px);
  }
  to {
    opacity: 1;
    transform: translateY(0);
  }
}

/* --- Progress bar (bottom) --- */
.carousel-progress {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  display: flex;
  z-index: 10;
  background: rgba(0, 0, 0, 0.3);
  backdrop-filter: blur(10px);
}

.progress-item {
  flex: 1;
  padding: 12px 16px 10px;
  cursor: pointer;
  transition: background 0.3s ease;
  border-left: 1px solid rgba(255, 255, 255, 0.08);
}

.progress-item:first-child {
  border-left: none;
}

.progress-item:hover {
  background: rgba(255, 255, 255, 0.06);
}

.progress-bar {
  height: 2px;
  background: rgba(255, 255, 255, 0.15);
  border-radius: 2px;
  overflow: hidden;
  margin-bottom: 8px;
}

.progress-label {
  font-size: 0.78rem;
  color: rgba(255, 255, 255, 0.4);
  transition: color 0.3s ease;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  display: block;
}

.progress-item.active .progress-label {
  color: rgba(255, 255, 255, 0.9);
}

.progress-fill {
  height: 100%;
  width: 0;
  background: var(--color-primary, #c2185b);
  border-radius: 2px;
}

.progress-item.active .progress-fill {
  width: 100%;
  animation: progressGrow linear forwards;
}

.progress-item.past .progress-fill {
  width: 100%;
  background: rgba(255, 255, 255, 0.4);
  animation: none;
}

@keyframes progressGrow {
  from { width: 0; }
  to { width: 100%; }
}

.progress-label {
  font-size: 0.75rem;
  color: rgba(255, 255, 255, 0.45);
  transition: color 0.3s ease;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
  display: block;
}

.progress-item.active .progress-label {
  color: rgba(255, 255, 255, 0.9);
}

/* --- Arrows --- */
.carousel-arrow {
  position: absolute;
  top: 50%;
  transform: translateY(-50%);
  width: 48px;
  height: 48px;
  border-radius: 50%;
  background: rgba(255, 255, 255, 0.08);
  backdrop-filter: blur(12px);
  color: #fff;
  font-size: 1.5rem;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  z-index: 10;
  transition: all 0.35s cubic-bezier(0.16, 1, 0.3, 1);
  border: 1px solid rgba(255, 255, 255, 0.12);
  opacity: 0;
}

.hero-carousel:hover .carousel-arrow {
  opacity: 1;
}

.carousel-arrow:hover {
  background: rgba(255, 255, 255, 0.2);
  transform: translateY(-50%) scale(1.1);
  border-color: rgba(255, 255, 255, 0.3);
}

.arrow-right {
  right: 20px;
}

.arrow-left {
  left: 20px;
}

/* --- Counter --- */
.slide-counter {
  position: absolute;
  top: 24px;
  left: 28px;
  z-index: 10;
  display: flex;
  align-items: baseline;
  gap: 4px;
  font-family: 'Inter', sans-serif;
  direction: ltr;
}

.counter-current {
  font-size: 1.5rem;
  font-weight: 700;
  color: #fff;
}

.counter-separator {
  font-size: 1rem;
  color: rgba(255, 255, 255, 0.4);
  margin: 0 2px;
}

.counter-total {
  font-size: 0.85rem;
  color: rgba(255, 255, 255, 0.5);
}

/* --- Responsive --- */
@media (max-width: 768px) {
  .hero-carousel {
    height: 400px;
  }
  .slide-content {
    bottom: 70px;
    right: 24px;
    left: 24px;
    max-width: none;
  }
  .slide-title {
    font-size: 1.8rem;
  }
  .slide-subtitle {
    font-size: 0.95rem;
    margin-bottom: 20px;
  }
  .slide-cta {
    padding: 12px 28px;
    font-size: 0.9rem;
  }
  .carousel-arrow {
    width: 40px;
    height: 40px;
    font-size: 1.3rem;
  }
  .progress-label {
    display: none;
  }
  .progress-item {
    padding: 10px 12px 8px;
  }
  .slide-counter {
    top: 16px;
    left: 16px;
  }
}

@media (max-width: 480px) {
  .hero-carousel {
    height: 320px;
  }
  .slide-content {
    bottom: 56px;
    right: 16px;
    left: 16px;
  }
  .slide-title {
    font-size: 1.4rem;
  }
  .slide-subtitle {
    font-size: 0.85rem;
    margin-bottom: 16px;
  }
  .slide-cta {
    padding: 10px 22px;
    font-size: 0.85rem;
  }
  .carousel-arrow {
    display: none;
  }
  .slide-badge {
    font-size: 0.7rem;
    padding: 4px 12px;
  }
}
</style>
