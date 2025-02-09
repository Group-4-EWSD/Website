<script setup lang="ts">
import SideImage from '@/assets/login-side-image.webp'
</script>
<template>
  <div class="grid grid-cols-1 md:grid-cols-2 h-screen">
    <div class="w-full h-full hidden md:block">
      <img :src="SideImage" alt="Aurora University" class="object-cover h-full w-full" />
    </div>
    <div class="flex items-center justify-center">
      <div class="w-full px-4 flex flex-col gap-4 xl:max-w-lg xl:mx-0 mx-6">
        <slot></slot>
      </div>
    </div>
  </div>
</template>
