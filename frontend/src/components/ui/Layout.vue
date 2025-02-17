<script setup>
import { ref } from 'vue'
import TopNavbar from './navbar/TopNavbar.vue'
import SidebarProvider from './sidebar/SidebarProvider.vue'
import AppSidebar from './AppSidebar.vue'
import { MenuIcon } from 'lucide-vue-next'

const isSidebarOpen = ref(false)

const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value
}
</script>

<template>
  <div class="flex flex-col h-screen">
    <TopNavbar class="fixed top-0 left-0 right-0 z-50 h-[78px]" />

    <div class="flex flex-1 pt-[78px] overflow-hidden">
      <button
        class="sm:hidden absolute top-[0.8rem] left-[0.8rem] z-50 p-2 bg-primary rounded-md text-white"
        @click="toggleSidebar"
      >
        <MenuIcon class="w-8 h-8" />
      </button>

      <SidebarProvider>
        <AppSidebar class="block md:w-60 lg:w-[220px] h-full pt-[78px]" />
      </SidebarProvider>

      <main class="flex-1 md:ml-60 lg:ml-[230px] p-6 overflow-y-auto">
        <slot />
      </main>
    </div>
  </div>
</template>
