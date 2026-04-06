<template>
  <div class="admin-layout">
    <AppSidebar />

    <div class="admin-main">
      <AppBar />

      <main class="admin-content">
        <slot />
      </main>
    </div>
  </div>
</template>

<style scoped>
.admin-layout {
  min-height: 100vh;
}

.admin-main {
  margin-inline-end: calc(var(--sidebar-width) + 40px);
  padding-block: 20px 24px;
  padding-inline: 0 20px;
}

.admin-content {
  margin-top: 18px;
}

@media (max-width: 920px) {
  .admin-main {
    margin-inline-end: 0;
    padding: 20px 12px 24px;
  }
}
</style>
