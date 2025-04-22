<script setup lang="ts">
import { ref } from 'vue'

import { useUserStore } from '@/stores/user'

const isMenuOpen = ref(false)

const userStore = useUserStore()

const toggleMenu = () => {
  isMenuOpen.value = !isMenuOpen.value
}
</script>

<template>
  <nav
    class="relative flex justify-between items-center bg-primary text-white h-[65px] px-4 sm:px-6 lg:px-10"
  >
    <!-- Logo and brand name -->
    <RouterLink class="flex items-center" to="/home">
      <img
        src="@/assets/nav-logo.png"
        alt="University magazine logo"
        class="h-10 sm:h-12 lg:h-14 w-auto max-h-full object-contain"
      />
      <span class="text-lg sm:text-xl font-bold uppercase pl-2 sm:pl-4 uni-color">Aurora</span>
      <span class="text-sm sm:text-md font-bold uppercase pl-1 sm:pl-2 name-color">University</span>
    </RouterLink>

    <!-- Mobile menu button -->
    <button class="block md:hidden focus:outline-none" @click="toggleMenu" aria-label="Toggle menu">
      <svg
        xmlns="http://www.w3.org/2000/svg"
        class="h-6 w-6"
        fill="none"
        viewBox="0 0 24 24"
        stroke="currentColor"
      >
        <path
          v-if="!isMenuOpen"
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M4 6h16M4 12h16M4 18h16"
        />
        <path
          v-else
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M6 18L18 6M6 6l12 12"
        />
      </svg>
    </button>

    <!-- Desktop navigation -->
    <div class="hidden absolute left-0 w-full justify-center md:flex md:items-center space-x-6 text-sm">
      <RouterLink to="/home" class="text-white hover:text-gray-300">Home</RouterLink>
      <RouterLink to="/about" class="text-white hover:text-gray-300">About</RouterLink>
      <RouterLink to="/contact-us" class="text-white hover:text-gray-300">Contact Us</RouterLink>
    </div>

    <div class="hidden md:flex text-sm">
      <RouterLink
        v-if="!userStore.currentUser"
        to="/auth/login"
        class="text-white hover:text-gray-300 ml-4"
        >Login</RouterLink
      >
      <RouterLink v-else to="/auth/login" class="text-white hover:text-gray-300 ml-4">
        Welcome, {{ userStore.currentUser?.user_name }}
      </RouterLink>
    </div>

    <!-- Mobile menu -->
    <div
      v-if="isMenuOpen"
      class="absolute top-[65px] left-0 right-0 bg-primary flex flex-col md:hidden z-50 text-sm"
    >
      <RouterLink
        to="/home"
        class="text-white hover:text-gray-300 py-4 px-6 border-b border-gray-700"
        >Home</RouterLink
      >
      <RouterLink
        to="/about-s"
        class="text-white hover:text-gray-300 py-4 px-6 border-b border-gray-700"
        >About</RouterLink
      >
      <RouterLink
        to="/contact-us"
        class="text-white hover:text-gray-300 py-4 px-6 border-b border-gray-700"
        >Contact Us</RouterLink
      >
      <RouterLink
        v-if="!userStore.currentUser"
        to="/auth/login"
        class="text-white hover:text-gray-300 py-4 px-6"
        >Login</RouterLink
      >
      <RouterLink v-else to="/auth/login" class="text-white hover:text-gray-300 py-4 px-6"
        >Account</RouterLink
      >
    </div>
  </nav>
</template>

<style scoped>
.name-color {
  color: #8aa8b2;
}

.uni-color {
  color: #e5e0da;
}
</style>
