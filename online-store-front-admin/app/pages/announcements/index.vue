<script setup lang="ts">
import {
  Megaphone,
  Pencil,
  Plus,
  Trash2,
  ToggleLeft,
  ToggleRight,
  Link,
  GripVertical,
} from 'lucide-vue-next'

definePageMeta({
  title: 'اللوحات الإعلانية',
  description: 'إدارة اللوحات والبانرات الإعلانية في المتجر.',
})

const announcementsStore = useAnnouncementsStore()
const router = useRouter()
const dialog = useDialog()
const announcementImageErrors = reactive<Record<number, boolean>>({})

onMounted(() => {
  announcementsStore.fetchAnnouncements()
})

function goToEditAnnouncement(id: number) {
  router.push({ path: '/announcements/manage', query: { id } })
}

function handleToggleActive(id: number, currentState: boolean) {
  dialog.confirm({
    title: currentState ? 'تعطيل اللوحة الإعلانية' : 'تفعيل اللوحة الإعلانية',
    message: currentState
      ? 'هل أنت متأكد من تعطيل هذه اللوحة؟ لن تظهر للزوار.'
      : 'هل أنت متأكد من تفعيل هذه اللوحة؟ ستظهر للزوار.',
    confirmText: currentState ? 'تعطيل' : 'تفعيل',
    onConfirm: async () => {
      const success = await announcementsStore.toggleActive(id)
      if (!success) {
        dialog.error({
          title: 'خطأ',
          message: 'حدث خطأ أثناء تغيير حالة اللوحة الإعلانية.',
        })
      }
    },
  })
}

function handleDelete(id: number) {
  dialog.confirm({
    title: 'حذف اللوحة الإعلانية',
    message: 'هل أنت متأكد من حذف هذه اللوحة الإعلانية؟ لا يمكن التراجع عن هذا الإجراء.',
    confirmText: 'حذف',
    onConfirm: async () => {
      const success = await announcementsStore.deleteAnnouncement(id)
      if (success) {
        dialog.success({
          title: 'تم الحذف',
          message: 'تم حذف اللوحة الإعلانية بنجاح.',
        })
      } else {
        dialog.error({
          title: 'خطأ',
          message: 'حدث خطأ أثناء حذف اللوحة الإعلانية.',
        })
      }
    },
  })
}

function hasAnnouncementImage(announcement: { id: number; image: { url: string } | null }) {
  return Boolean(announcement.image?.url) && !announcementImageErrors[announcement.id]
}
</script>

<template>
  <div class="announcements-page">
    <!-- Page Header -->
    <div class="page-header">
      <div class="header-actions" v-if="!announcementsStore.loading && !announcementsStore.error">
        <span class="announcement-count-badge" v-if="announcementsStore.announcements.length">
          <Megaphone :size="15" :stroke-width="2.2" />
          {{ announcementsStore.announcements.length }} لوحة إعلانية
        </span>
        <NuxtLink to="/announcements/manage" class="button-primary add-btn">
          <Plus :size="18" :stroke-width="2.5" />
          إضافة لوحة
        </NuxtLink>
      </div>
    </div>

    <!-- ═══════ Loading State ═══════ -->
    <Transition name="fade" mode="out-in">
      <LoadingState v-if="announcementsStore.loading" message="جاري تحميل اللوحات الإعلانية..." key="loading" />

      <!-- ═══════ Error State ═══════ -->
      <ErrorState
        v-else-if="announcementsStore.error"
        title="تعذر تحميل اللوحات الإعلانية"
        :message="announcementsStore.error"
        @retry="announcementsStore.fetchAnnouncements()"
        key="error"
      />

      <!-- ═══════ Empty State ═══════ -->
      <EmptyState
        v-else-if="announcementsStore.announcements.length === 0"
        title="لا توجد لوحات إعلانية"
        description="ابدأ بإضافة لوحة إعلانية لعرضها للزوار في واجهة المتجر."
        action-label="إضافة لوحة إعلانية"
        action-to="/announcements/manage"
        key="empty"
      >
        <template #icon>
          <Megaphone :size="40" :stroke-width="1.5" />
        </template>
      </EmptyState>

      <!-- ═══════ Success State (Announcements List) ═══════ -->
      <div v-else class="announcements-list" key="success">
        <article
          v-for="announcement in announcementsStore.announcements"
          :key="announcement.id"
          class="announcement-card surface-card"
        >
          <!-- Image -->
          <div class="card-image-wrap">
            <img
              v-if="hasAnnouncementImage(announcement)"
              :src="announcement.image.url"
              :alt="announcement.title"
              class="card-image"
              loading="lazy"
              @error="announcementImageErrors[announcement.id] = true"
            />
            <div v-else class="card-image-placeholder">
              <Megaphone :size="36" :stroke-width="1.4" />
            </div>

            <!-- Status Badge -->
            <span
              class="status-chip"
              :class="announcement.is_active ? 'status-active' : 'status-inactive'"
            >
              {{ announcement.is_active ? 'نشط' : 'معطّل' }}
            </span>

            <!-- Sort Order Badge -->
            <span class="sort-chip" v-if="announcement.sort_order != null">
              <GripVertical :size="12" :stroke-width="2.5" />
              {{ announcement.sort_order }}
            </span>
          </div>

          <!-- Card Body -->
          <div class="card-body">
            <h3 class="card-title">{{ announcement.title }}</h3>

            <p class="card-desc" v-if="announcement.description">
              {{ announcement.description }}
            </p>

            <div class="card-meta" v-if="announcement.link">
              <span class="meta-link">
                <Link :size="13" :stroke-width="2.2" />
                {{ announcement.link_text || announcement.link }}
              </span>
            </div>

            <div class="card-footer">
              <div class="card-actions">
                <button
                  class="action-btn toggle-btn"
                  :class="announcement.is_active ? 'toggle-active' : 'toggle-inactive'"
                  :title="announcement.is_active ? 'تعطيل' : 'تفعيل'"
                  @click.stop="handleToggleActive(announcement.id, announcement.is_active)"
                >
                  <ToggleRight v-if="announcement.is_active" :size="18" :stroke-width="2" />
                  <ToggleLeft v-else :size="18" :stroke-width="2" />
                </button>

                <button class="action-btn" title="تعديل" @click.stop="goToEditAnnouncement(announcement.id)">
                  <Pencil :size="16" :stroke-width="2" />
                </button>

                <button class="action-btn delete-btn" title="حذف" @click.stop="handleDelete(announcement.id)">
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
.announcements-page {
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

.announcement-count-badge {
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

/* ─── Announcements List ─── */
.announcements-list {
  display: grid;
  grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
  gap: 18px;
}

/* ─── Announcement Card ─── */
.announcement-card {
  display: flex;
  flex-direction: column;
  border-radius: var(--radius-lg);
  overflow: hidden;
  transition: transform 0.28s ease, box-shadow 0.28s ease;
}

.announcement-card:hover {
  transform: translateY(-4px);
  box-shadow:
    0 20px 50px rgba(20, 32, 51, 0.12),
    0 8px 16px rgba(20, 32, 51, 0.06);
}

/* Image */
.card-image-wrap {
  position: relative;
  aspect-ratio: 21 / 9;
  background: var(--color-slate-100);
  overflow: hidden;
}

.card-image {
  width: 100%;
  height: 100%;
  object-fit: cover;
  transition: transform 0.4s ease;
}

.announcement-card:hover .card-image {
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

.sort-chip {
  position: absolute;
  top: 12px;
  left: 12px;
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 4px 10px;
  border-radius: 999px;
  font-size: 0.74rem;
  font-weight: 800;
  background: rgba(0, 0, 0, 0.4);
  color: white;
  backdrop-filter: blur(8px);
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

.card-desc {
  margin: 0;
  font-size: 0.88rem;
  color: var(--color-slate-600);
  line-height: 1.7;
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

.meta-link {
  display: inline-flex;
  align-items: center;
  gap: 6px;
  padding: 4px 10px;
  border-radius: 8px;
  background: rgba(20, 32, 51, 0.05);
  color: var(--color-slate-700);
  font-size: 0.8rem;
  font-weight: 700;
  max-width: 100%;
  overflow: hidden;
  text-overflow: ellipsis;
  white-space: nowrap;
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

.toggle-btn.toggle-active {
  color: var(--color-green-600);
}

.toggle-btn.toggle-active:hover {
  background: rgba(32, 128, 94, 0.12);
}

.toggle-btn.toggle-inactive {
  color: var(--color-slate-500);
}

.delete-btn:hover {
  background: rgba(202, 61, 84, 0.12);
  color: var(--color-red-600);
}

/* States are now handled by reusable components */

/* ─── Responsive ─── */
@media (max-width: 640px) {
  .announcements-list {
    grid-template-columns: 1fr;
    gap: 12px;
  }

  .page-header {
    flex-direction: column;
    align-items: flex-start;
  }
}
</style>
