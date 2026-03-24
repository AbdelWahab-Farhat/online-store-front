<template>
  <section class="hero">
    <div class="hero-bg"></div>
    <div class="container hero-content">
      <span class="hero-badge animate-fade-in-up">✨ وصل حديثاً</span>
      <h1 class="hero-title animate-fade-in-up">
        أناقتك <span class="highlight">تبدأ</span> من هنا
      </h1>
      <p class="hero-subtitle animate-fade-in-up">
        اكتشفي أحدث صيحات الموضة والجمال بأسعار لا تُقاوم
      </p>
      <div class="hero-actions animate-fade-in-up">
        <NuxtLink to="/products" class="btn btn-primary btn-lg">
          تسوّقي الآن
          <Icon name="mdi:arrow-left" />
        </NuxtLink>
      </div>
    </div>
  </section>
</template>

<style scoped>
.hero {
  position: relative;
  min-height: 480px;
  display: flex;
  align-items: center;
  overflow: hidden;
}

.hero-bg {
  position: absolute;
  inset: 0;
  background: linear-gradient(
    135deg,
    #fce4ec 0%,
    #f8bbd0 30%,
    #f48fb1 60%,
    #ec407a 100%
  );
  z-index: 0;
}

.hero-bg::before {
  content: '';
  position: absolute;
  inset: 0;
  background:
    radial-gradient(circle at 20% 80%, rgba(212, 168, 83, 0.3) 0%, transparent 50%),
    radial-gradient(circle at 80% 20%, rgba(255, 255, 255, 0.2) 0%, transparent 50%);
}

.hero-bg::after {
  content: '✿ ❀ ✾ ❁ ✿ ❀ ✾';
  position: absolute;
  bottom: 20px;
  left: 50%;
  transform: translateX(-50%);
  font-size: 2rem;
  letter-spacing: 2rem;
  opacity: 0.15;
  white-space: nowrap;
}

.hero-content {
  position: relative;
  z-index: 1;
  text-align: center;
  padding: var(--space-3xl) 0;
}

.hero-badge {
  display: inline-block;
  padding: var(--space-sm) var(--space-lg);
  background: rgba(255, 255, 255, 0.25);
  backdrop-filter: blur(8px);
  border-radius: var(--radius-full);
  font-size: var(--font-size-sm);
  font-weight: 700;
  color: #fff;
  margin-bottom: var(--space-lg);
  animation-delay: 0.1s;
}

.hero-title {
  font-size: var(--font-size-4xl);
  font-weight: 800;
  color: #fff;
  margin-bottom: var(--space-md);
  line-height: 1.3;
  animation-delay: 0.2s;
}

.highlight {
  background: linear-gradient(135deg, var(--color-accent-light), var(--color-accent));
  -webkit-background-clip: text;
  -webkit-text-fill-color: transparent;
  background-clip: text;
}

.hero-subtitle {
  font-size: var(--font-size-lg);
  color: rgba(255, 255, 255, 0.9);
  max-width: 500px;
  margin: 0 auto var(--space-xl);
  animation-delay: 0.3s;
}

.hero-actions {
  animation-delay: 0.4s;
}

.btn-lg {
  padding: var(--space-md) var(--space-2xl);
  font-size: var(--font-size-base);
}

@media (max-width: 768px) {
  .hero {
    min-height: 380px;
  }
  .hero-title {
    font-size: var(--font-size-3xl);
  }
  .hero-subtitle {
    font-size: var(--font-size-base);
  }
}

@media (max-width: 480px) {
  .hero {
    min-height: 320px;
  }
  .hero-title {
    font-size: var(--font-size-2xl);
  }
}
</style>
