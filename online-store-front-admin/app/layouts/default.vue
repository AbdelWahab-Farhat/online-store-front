<script setup lang="ts">
const dialog = useDialog()
</script>

<template>
  <div class="admin-layout">
    <AppBar />

    <main class="admin-content">
      <slot />
    </main>

    <!-- Global Dialog -->
    <AppDialog
      :open="dialog.state.open"
      :variant="dialog.state.variant"
      :title="dialog.state.title"
      :message="dialog.state.message"
      :confirm-text="dialog.state.confirmText"
      :cancel-text="dialog.state.cancelText"
      :hide-cancel="dialog.state.hideCancel"
      @confirm="dialog.handleConfirm()"
      @cancel="dialog.handleCancel()"
      @update:open="dialog.state.open = $event"
    />
  </div>
</template>

<style scoped>
.admin-layout {
  min-height: 100vh;
  display: flex;
  flex-direction: column;
}

.admin-content {
  flex: 1;
  width: min(var(--container-max), 100%);
  margin: 0 auto;
  padding: 20px 24px 32px;
}

@media (max-width: 640px) {
  .admin-content {
    padding: 14px 12px 24px;
  }
}
</style>
