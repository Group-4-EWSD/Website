<script setup lang="ts">
import AuthBaseLayout from '@/components/shared/AuthBaseLayout.vue'
import Logo from '@/assets/logo.png'
</script>

<template>
  <AuthBaseLayout>
    <div class="h-40 w-40 mx-auto md:mx-0">
      <img :src="Logo" alt="Aurora University'g Logo" class="object-cover h-full w-full" />
    </div>
    <h1 class="text-3xl text-center md:text-left">Welcome to AURORA University</h1>

    <p>This is an upcoming feature.</p>
  </AuthBaseLayout>
</template>
