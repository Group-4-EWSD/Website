<script setup lang="ts">
import AppSidebar from './AppSidebar.vue'
import TopNavbar from './navbar/TopNavbar.vue'
import SidebarProvider from './sidebar/SidebarProvider.vue'
import SidebarTrigger from './sidebar/SidebarTrigger.vue'
</script>

<template>
  <div class="flex flex-col h-screen">
    <TopNavbar class="fixed top-0 left-0 right-0 z-50 h-[78px]" />

    <div class="flex flex-1 pt-[78px] overflow-hidden">
      <SidebarProvider>
        <AppSidebar class="block lg:w-[220px] h-full pt-[78px]" />
        <SidebarTrigger
          class="sm:hidden absolute top-[1.3rem] left-[1.25rem] z-50 p-2 bg-primary rounded-md text-white w-8 h-8"
        />
      </SidebarProvider>
      <main class="flex-1 md:ml-60 lg:ml-[230px] p-6 overflow-y-auto">
        <slot />
      </main>
    </div>
  </div>
</template>
