<script setup lang="ts">
import Logo from '@/assets/logo.png'
import AuthBaseLayout from '@/components/shared/AuthBaseLayout.vue'
import { RouterLink } from 'vue-router'
</script>

<template>
  <AuthBaseLayout>
    <div class="h-40 w-40 mx-auto">
      <img :src="Logo" alt="Aurora University'g Logo" class="object-cover h-full w-full" />
    </div>
    <h1 class="text-3xl text-center my-4">Forgot Password?</h1>

    <div class="flex items-center flex-col gap-5">
      <p class="text-center">
        Please contact university admin via
        <a class="text-blue-400 italic" href="mailto:avery.morgan@gmail.com" target="_href"
          >avery.morgan@gmail.com</a
        >
      </p>
      <p class="opacity-50">or</p>
      <p class="text-center">
        Send a message from <RouterLink class="text-blue-400 italic" to="/contact-us">here</RouterLink>.
      </p>
    </div>
  </AuthBaseLayout>
</template>
