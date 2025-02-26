<script setup lang="ts">
import AppSidebar from '@/components/ui/AppSidebar.vue'
import TopNavbar from '@/components/ui/navbar/TopNavBar.vue'
import { SidebarProvider, SidebarTrigger } from '@/components/ui/sidebar'
import { TooltipProvider } from '@/components/ui/tooltip'
</script>

<template>
  <div class="flex flex-col h-screen">
    <SidebarProvider>
      <TopNavbar class="fixed top-0 left-0 right-0 z-50 h-[78px]" />

      <div class="flex flex-1 pt-[78px] overflow-hidden">
        <AppSidebar class="block h-full pt-[78px]" />
        <SidebarTrigger
          class="sm:hidden absolute top-[1.3rem] left-[1.25rem] z-50 p-2 bg-primary rounded-md text-white w-8 h-8"
        />
        <TooltipProvider v-bind:delay-duration="100">
          <main class="flex-1 p-6 overflow-y-auto mx-auto lg:max-w-[1500px]">
            <slot />
          </main>
        </TooltipProvider>
      </div>
    </SidebarProvider>
  </div>
</template>
