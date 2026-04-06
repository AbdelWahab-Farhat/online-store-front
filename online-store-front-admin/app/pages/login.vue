<script setup lang="ts">
import { z } from "zod";

definePageMeta({
  layout: false,
  title: "تسجيل الدخول",
});

useHead({
  title: "تسجيل الدخول | لوحة تحكم المتجر",
});

const authStore = useAuthStore();

const form = reactive({
  email: "",
  password: "",
});

const showPassword = ref(false);

const loginSchema = z.object({
  email: z
    .string()
    .min(1, "يرجى إدخال البريد الإلكتروني.")
    .email("البريد الإلكتروني غير صالح."),
  password: z.string().min(1, "يرجى إدخال كلمة المرور."),
});

const {
  errors: validationErrors,
  validate,
  validateField,
} = useValidator(loginSchema);

async function handleSubmit() {
  if (!validate(form)) {
    return;
  }

  try {
    await authStore.login({
      email: form.email.trim(),
      password: form.password,
    });

    await navigateTo("/");
  } catch (error) {
    // Error is handled in authStore.error automatically.
  }
}
</script>

<template>
  <div class="login-page">
    <!-- decorative background shapes -->
    <div class="bg-shape bg-shape-1"></div>
    <div class="bg-shape bg-shape-2"></div>
    <div class="bg-shape bg-shape-3"></div>

    <main class="login-shell">
      <section class="login-card">
        <div class="card-accent"></div>

        <div class="login-heading">
          <span class="section-eyebrow">لوحة تحكم المتجر</span>
          <h2>تسجيل الدخول</h2>
          <p class="heading-desc">
            استخدم البريد الإلكتروني وكلمة المرور الخاصة بحساب الإدارة أو
            الموظف.
          </p>
        </div>

        <form class="login-form" @submit.prevent="handleSubmit">
          <CustomTextfield
            id="email"
            v-model="form.email"
            type="email"
            label="البريد الإلكتروني"
            placeholder="admin@example.com"
            dir="ltr"
            autocomplete="email"
            :error="validationErrors.email"
            @blur="validateField('email', form.email)"
          />

          <CustomTextfield
            id="password"
            v-model="form.password"
            :type="showPassword ? 'text' : 'password'"
            label="كلمة المرور"
            placeholder="••••••••"
            autocomplete="current-password"
            inputClass="password-field"
            :error="validationErrors.password"
            @blur="validateField('password', form.password)"
          >
            <template #suffix>
              <button
                type="button"
                class="toggle-password"
                @click="showPassword = !showPassword"
              >
                {{ showPassword ? "إخفاء" : "إظهار" }}
              </button>
            </template>
          </CustomTextfield>

          <p class="input-hint">
            الدخول متاح فقط للحسابات المخوّلة بإدارة المتجر.
          </p>

          <!-- always rendered to prevent layout shift -->
          <Transition name="error-fade">
            <p v-if="authStore.error" class="form-error form-error-block">
              {{ authStore.error }}
            </p>
          </Transition>

          <button
            type="submit"
            class="button-primary submit-button"
            :disabled="authStore.loading"
          >
            {{ authStore.loading ? "جارٍ التحقق..." : "دخول لوحة التحكم" }}
          </button>
        </form>

        <p class="login-footer">
          جميع الحقوق محفوظة &copy; {{ new Date().getFullYear() }}
        </p>
      </section>
    </main>
  </div>
</template>

<style scoped>
/* ── override body & grid overlay for this page ── */
.login-page {
  position: relative;
  min-height: 100dvh;
  display: flex;
  justify-content: center;
  align-items: center;
  padding: 24px 16px;
  overflow: hidden;

  /* override the body background with our own */
  background: linear-gradient(145deg, #f0e8df 0%, #e6ddd4 35%, #ede4db 65%, #f7f2ec 100%);
  isolation: isolate;
}

/* Hide the body::before grid overlay on this page */
.login-page::after {
  content: '';
  position: fixed;
  inset: 0;
  background: linear-gradient(145deg, #f0e8df 0%, #e6ddd4 35%, #ede4db 65%, #f7f2ec 100%);
  z-index: -2;
}

/* ── decorative shapes ── */
.bg-shape {
  position: absolute;
  border-radius: 50%;
  pointer-events: none;
  z-index: 0;
}

.bg-shape-1 {
  top: -120px;
  right: -80px;
  width: 480px;
  height: 480px;
  background: radial-gradient(circle, rgba(236, 159, 67, 0.22) 0%, transparent 65%);
}

.bg-shape-2 {
  bottom: -100px;
  left: -60px;
  width: 400px;
  height: 400px;
  background: radial-gradient(circle, rgba(196, 107, 23, 0.12) 0%, transparent 65%);
}

.bg-shape-3 {
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  width: 700px;
  height: 700px;
  background: radial-gradient(circle, rgba(255, 255, 255, 0.55) 0%, transparent 55%);
}

/* ── shell ── */
.login-shell {
  position: relative;
  z-index: 1;
  width: min(440px, 100%);
}

/* ── card ── */
.login-card {
  position: relative;
  padding: 38px 34px 30px;
  border-radius: 26px;
  background: rgba(255, 255, 255, 0.92);
  backdrop-filter: blur(24px);
  -webkit-backdrop-filter: blur(24px);
  border: 1px solid rgba(255, 255, 255, 0.7);
  box-shadow:
    0 8px 40px rgba(0, 0, 0, 0.07),
    0 2px 8px rgba(0, 0, 0, 0.03),
    inset 0 1px 0 rgba(255, 255, 255, 0.9);
  overflow: hidden;
}

/* thin gradient line at the top */
.card-accent {
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  height: 4px;
  background: linear-gradient(90deg, var(--color-amber-700), var(--color-amber-500), var(--color-amber-700));
}

/* ── heading — override global .page-heading flex ── */
.login-heading {
  display: block !important;
  margin-bottom: 28px;
}

.login-heading h2 {
  margin: 8px 0 10px;
  font-size: 1.6rem;
  color: var(--color-slate-950);
  line-height: 1.3;
}

.heading-desc {
  margin: 0;
  color: var(--color-slate-700);
  font-size: 0.92rem;
  line-height: 1.8;
}

/* ── form ── */
.login-form {
  display: grid;
  gap: 18px;
}

.password-field {
  padding-inline-end: 88px;
}

.toggle-password {
  position: absolute;
  inset-block-start: 50%;
  inset-inline-end: 12px;
  transform: translateY(-50%);
  min-width: 64px;
  min-height: 36px;
  border-radius: 999px;
  background: rgba(28, 43, 67, 0.08);
  color: var(--color-slate-900);
  font-size: 0.84rem;
  font-weight: 800;
  cursor: pointer;
  transition: background var(--transition-base);
}

.toggle-password:hover {
  background: rgba(28, 43, 67, 0.14);
}

/* ── error block with smooth transition ── */
.form-error-block {
  padding: 12px 16px;
  border-radius: 14px;
  background: rgba(202, 61, 84, 0.08);
  margin: 0;
}

.error-fade-enter-active,
.error-fade-leave-active {
  transition: opacity 0.2s ease, transform 0.2s ease;
}

.error-fade-enter-from,
.error-fade-leave-to {
  opacity: 0;
  transform: translateY(-4px);
}

/* ── submit ── */
.submit-button {
  width: 100%;
  margin-top: 2px;
}

.submit-button:disabled {
  cursor: wait;
  opacity: 0.8;
}

/* ── footer ── */
.login-footer {
  margin-top: 26px;
  text-align: center;
  font-size: 0.8rem;
  color: rgba(0, 0, 0, 0.32);
}

/* ── responsive ── */
@media (max-width: 640px) {
  .login-card {
    padding: 28px 20px 22px;
    border-radius: 20px;
  }
}
</style>

