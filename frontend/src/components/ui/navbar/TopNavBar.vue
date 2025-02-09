<script setup>
import { ref } from 'vue'
import { Search } from 'lucide-vue-next'
import Input from '../input/Input.vue'
import Button from '../button/Button.vue'
</script>

<template>
  <nav class="flex justify-between items-center p-4 bg-gray-900 text-white">
    <div class="flex items-center space-x-4">
      <div class="text-xl font-bold">University logo or image</div>
    </div>
    <div class="hidden sm:flex flex-1 justify-center">
      <div class="w-1/2 relative">
        <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 text-black w-5 h-5" />
        <Input v-model="searchQuery" placeholder="Search..." class="pl-10" />
      </div>
    </div>
    <div class="flex items-center space-x-4">
      <Button class="p-0 w-6 h-6">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          style="width: 24px; height: 24px"
          fill="none"
          viewBox="0 0 24 24"
          stroke-width="1.5"
          stroke="currentColor"
          class="size-6"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M21 8.25c0-2.485-2.099-4.5-4.688-4.5-1.935 0-3.597 1.126-4.312 2.733-.715-1.607-2.377-2.733-4.313-2.733C5.1 3.75 3 5.765 3 8.25c0 7.22 9 12 9 12s9-4.78 9-12Z"
          />
        </svg>
      </Button>
      <Button class="p-0 w-6 h-6">
        <svg
          xmlns="http://www.w3.org/2000/svg"
          style="width: 24px; height: 24px"
          fill="none"
          viewBox="0 0 24 24"
          stroke-width="1.5"
          stroke="currentColor"
          class="size-6"
        >
          <path
            stroke-linecap="round"
            stroke-linejoin="round"
            d="M14.857 17.082a23.848 23.848 0 0 0 5.454-1.31A8.967 8.967 0 0 1 18 9.75V9A6 6 0 0 0 6 9v.75a8.967 8.967 0 0 1-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 0 1-5.714 0m5.714 0a3 3 0 1 1-5.714 0"
          />
        </svg>
      </Button>
      <div class="text-white font-medium">Welcome, Username</div>

      <button @click="goToProfile">
        <img
          src="@/assets/profile.png"
          alt="Profile"
          class="w-10 h-10 rounded-full border border-white"
        />
      </button>
    </div>
  </nav>
</template>

<style scoped>
.search-container {
  max-width: 800px;
  margin: 0 auto;
}

input {
  width: 100%;
}
</style>
