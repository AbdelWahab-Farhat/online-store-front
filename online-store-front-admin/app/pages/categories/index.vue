<script setup lang="ts">
import {
  Tag,
  Eye,
  Pencil,
  Package,
  Plus,
  Trash2,
} from 'lucide-vue-next'
import type { Category } from '~/types/category'

definePageMeta({
  title: 'التصنيفات',
  description: 'إدارة وترتيب أقسام المتجر الخاصة بك.',
})

const categoriesStore = useCategoriesStore()
const productsStore = useProductsStore()
const router = useRouter()
const dialog = useDialog()
const categoryImageErrors = reactive<Record<number, boolean>>({})

onMounted(() => {
  categoriesStore.fetchCategories()
})

function goToProducts(categoryId: number) {
  productsStore.selectedCategoryId = categoryId
  router.push({ path: '/products', query: { category_id: categoryId } })
}

function goToEditCategory(categoryId: number) {
  router.push({ path: '/categories/manage', query: { id: categoryId } })
}

function hasCategoryImage(category: Category) {
  return Boolean(category.image?.url) && !categoryImageErrors[category.id]
}

function handleDeleteCategory(category: Category) {
  const productsCount = category.products_count ?? 0

  if (productsCount > 0) {
    dialog.info({
      title: 'تعذر حذف التصنيف',
      message: `لا يمكن حذف تصنيف "${category.name}" لأنه يحتوي على ${productsCount} منتج.`,
    })
    return
  }

  dialog.confirm({
    title: 'حذف التصنيف',
    message: `هل أنت متأكد من حذف تصنيف "${category.name}"؟ لا يمكن التراجع عن هذا الإجراء.`,
    confirmText: 'حذف',
    onConfirm: async () => {
      const result = await categoriesStore.deleteCategory(category.id)

      if (result.success) {
        dialog.success({
          title: 'تم الحذف',
          message: result.message,
        })
        return
      }

      dialog.error({
        title: 'فشل الحذف',
        message: result.message,
      })
    },
  })
}
</script>

<template>
  <div class="categories-page">
    <!-- Page Header -->
    <div class="page-header">
      <div class="header-actions" v-if="!categoriesStore.loading && !categoriesStore.error">
        <span class="category-count-badge" v-if="categoriesStore.categories.length">
          <Tag :size="15" :stroke-width="2.2" />
          {{ categoriesStore.categories.length }} تصنيف
        </span>
        <NuxtLink to="/categories/manage" class="button-primary add-btn">
          <Plus :size="18" :stroke-width="2.5" />
          إضافة تصنيف
        </NuxtLink>
      </div>
    </div>

    <!-- ═══════ Loading State ═══════ -->
    <Transition name="fade" mode="out-in">
      <LoadingState v-if="categoriesStore.loading" message="جاري تحميل التصنيفات..." key="loading" />

      <!-- ═══════ Error State ═══════ -->
      <ErrorState
        v-else-if="categoriesStore.error"
        title="تعذر تحميل التصنيفات"
        :message="categoriesStore.error"
        @retry="categoriesStore.fetchCategories()"
        key="error"
      />

      <!-- ═══════ Empty State ═══════ -->
      <EmptyState
        v-else-if="categoriesStore.categories.length === 0"
        title="لا توجد تصنيفات"
        description="ابدأ بإضافة تصنيفات لترتيب منتجاتك في أقسام."
        action-label="إضافة تصنيف"
        action-to="/categories/manage"
        key="empty"
      >
        <template #icon>
          <Tag :size="40" :stroke-width="1.5" />
        </template>
      </EmptyState>

      <!-- ═══════ Success State (Categories Grid) ═══════ -->
      <div v-else class="categories-grid" key="success">
        <article
          v-for="category in categoriesStore.categories"
          :key="category.id"
          class="category-card surface-card"
          @click="goToProducts(category.id)"
        >
          <!-- Image -->
          <div class="card-image-wrap">
            <img
              v-if="hasCategoryImage(category)"
              :src="category.image.url"
              :alt="category.name"
              class="card-image"
              loading="lazy"
              @error="categoryImageErrors[category.id] = true"
            />
            <div v-else class="card-image-placeholder">
              <Tag :size="36" :stroke-width="1.4" />
            </div>

            <!-- Status Badge -->
            <span
              class="status-chip"
              :class="category.is_active ? 'status-active' : 'status-inactive'"
            >
              {{ category.is_active ? 'نشط' : 'معطّل' }}
            </span>
          </div>

          <!-- Card Body -->
          <div class="card-body">
            <h3 class="card-title">{{ category.name }}</h3>

            <div class="card-meta">
              <span class="meta-products">
                <Package :size="14" :stroke-width="2" />
                {{ category.products_count ?? 0 }} منتجات
              </span>
            </div>

            <div class="card-footer">
              <div class="card-actions">
                <button class="action-btn" title="تعديل" @click.stop="goToEditCategory(category.id)">
                  <Pencil :size="16" :stroke-width="2" />
                </button>
                <button
                  class="action-btn delete-btn"
                  :class="{ 'delete-btn--blocked': (category.products_count ?? 0) > 0 }"
                  :title="(category.products_count ?? 0) > 0 ? 'لا يمكن حذف تصنيف يحتوي على منتجات' : 'حذف'"
                  :disabled="categoriesStore.isDeleting(category.id)"
                  @click.stop="handleDeleteCategory(category)"
                >
                  <Trash2 :size="16" :stroke-width="2" />
                </button>
              </div>
            </div>
          </div>
        </article>
      </div>
    </Transition>
  </div>
</template>

<style scoped>
.categories-page {
  display: grid;
  gap: 24px;
}

/* ─── Header ─── */
.page-header {
  display: flex;
  align-items: flex-end;
  justify-content: space-between;
  gap: 16px;
  flex-wrap: wrap;
}

.page-header h1 {
  margin: 8px 0 0;
  font-size: 1.5rem;
  color: var(--color-slate-950);
}

.header-desc {
  margin: 4px 0 0;
  color: var(--color-slate-700);
  font-size: 0.92rem;
}

.category-count-badge {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 8px 16px;
  border-radius: 999px;
  background: rgba(20, 32, 51, 0.06);
  font-size: 0.88rem;
  font-weight: 800;
  color: var(--color-slate-800);
}

.header-actions {
  display: flex;
  align-items: center;
  gap: 12px;
  flex: 1;
  width: 100%;
  justify-content: space-between;
}

.add-btn {
  font-size: 0.88rem;
  min-height: 42px;
  padding: 0 20px;
}

/* ─── Fade Transition ─── */
.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.3s ease;
}

.fade-enter-from,
.fade-leave-to {
  opacity: 0;
}

/* ─── Categories Grid ─── */
.categories-grid {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(260px, 1fr));
  gap: 18px;
}

/* ─── Category Card ─── */
.category-card {
  display: flex;
  flex-direction: column;
  border-radius: var(--radius-lg);
  overflow: hidden;
  cursor: pointer;
  transition: transform 0.28s ease, box-shadow 0.28s ease;
}

.category-card:hover {
  transform: translateY(-4px);
  box-shadow:
    0 20px 50px rgba(20, 32, 51, 0.12),
    0 8px 16px rgba(20, 32, 51, 0.06);
}

/* Image */
.card-image-wrap {
  position: relative;
  aspect-ratio: 16 / 9;
  background: var(--color-slate-100);
  overflow: hidden;
}

.card-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.4s ease;
}

.category-card:hover .card-image {
  transform: scale(1.04);
}

.card-image-placeholder {
  width: 100%;
  height: 100%;
  display: flex;
  align-items: center;
  justify-content: center;
  color: var(--color-slate-500);
  background: linear-gradient(135deg, var(--color-slate-100), var(--color-slate-200));
}

/* Badges on Image */
.status-chip {
  position: absolute;
  top: 12px;
  right: 12px;
  padding: 4px 12px;
  border-radius: 999px;
  font-size: 0.76rem;
  font-weight: 800;
  backdrop-filter: blur(8px);
}

.status-active {
  background: rgba(32, 128, 94, 0.16);
  color: var(--color-green-600);
}

.status-inactive {
  background: rgba(202, 61, 84, 0.14);
  color: var(--color-red-600);
}

/* Body */
.card-body {
  display: flex;
  flex-direction: column;
  gap: 8px;
  padding: 16px 18px 18px;
  flex: 1;
}

.card-title {
  margin: 0;
  font-size: 1.05rem;
  font-weight: 800;
  color: var(--color-slate-950);
  line-height: 1.5;
  display: -webkit-box;
  -webkit-line-clamp: 2;
  line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}

.card-meta {
  display: flex;
  align-items: center;
  gap: 8px;
  margin-top: 4px;
}

.meta-products {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 4px 10px;
  border-radius: 8px;
  background: rgba(20, 32, 51, 0.05);
  color: var(--color-slate-700);
  font-size: 0.82rem;
  font-weight: 700;
}

/* Footer */
.card-footer {
  display: flex;
  align-items: center;
  justify-content: flex-end;
  margin-top: auto;
  padding-top: 14px;
  border-top: 1px solid rgba(213, 222, 234, 0.5);
}

.card-actions {
  display: flex;
  gap: 6px;
}

.action-btn {
  display: flex;
  align-items: center;
  justify-content: center;
  width: 36px;
  height: 36px;
  border-radius: 10px;
  background: rgba(20, 32, 51, 0.05);
  color: var(--color-slate-700);
  cursor: pointer;
  transition: all var(--transition-base);
}

.action-btn:hover {
  background: rgba(20, 32, 51, 0.1);
  color: var(--color-slate-950);
  transform: scale(1.08);
}

.delete-btn {
  color: var(--color-red-600);
}

.delete-btn:hover {
  background: rgba(202, 61, 84, 0.12);
  color: var(--color-red-700);
}

.delete-btn--blocked {
  opacity: 0.65;
}

.delete-btn:disabled {
  opacity: 0.55;
  cursor: wait;
  transform: none;
}

/* States are now handled by reusable components */

/* ─── Responsive ─── */
@media (max-width: 640px) {
  .categories-grid {
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 12px;
  }

  .page-header {
    flex-direction: column;
    align-items: flex-start;
  }
}
</style>
