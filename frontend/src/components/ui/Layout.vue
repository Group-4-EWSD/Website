<script setup>
import TopNavbar from './navbar/TopNavbar.vue'
import SideNavbar from './navbar/SideNavbar.vue'
</script>

<template>
  <div class="flex flex-col h-screen">
    <TopNavbar class="fixed top-0 left-0 right-0 z-50" />

    <div class="flex flex-1 pt-16">
      <SideNavbar />
      <main class="flex-1 pl-48 overflow-y-auto p-6">
        <slot />
      </main>
    </div>
  </div>
</template>
