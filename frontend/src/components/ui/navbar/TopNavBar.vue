<script setup>
import { ref } from 'vue'
import { Search, X, BellDot } from 'lucide-vue-next'
import Input from '../input/Input.vue'

const searchQuery = ref('')
const hasNotification = true
const isSearchActive = ref(false)
const toggleSearch = () => {
  isSearchActive.value = !isSearchActive.value
}
</script>

<template>
  <nav class="flex justify-between items-center bg-primary text-white">
    <div class="flex items-center">
      <img
        src="@/assets/nav-logo.png"
        alt="University magazine logo"
        class="h-12 sm:h-14 w-auto max-h-full object-contain pl-[4.5rem] sm:pl-10"
      />
    </div>

    <div class="sm:flex flex-1 sm:justify-center">
      <div class="hidden sm:flex w-1/2 relative sm:my-4">
        <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 text-black w-5 h-5" />
        <Input v-model="searchQuery" placeholder="Search..." class="pl-10" />
      </div>

      <button
        v-if="!isSearchActive"
        @click="toggleSearch"
        class="sm:hidden p-2 rounded-full bg-primary absolute right-[4.5rem] top-1/2 transform -translate-y-1/2"
      >
        <Search class="w-8 h-8 bg-primary text-white" />
      </button>

      <div
        v-else
        class="absolute left-0 w-[90vw] flex items-center bg-white border rounded-lg px-3 py-1 shadow-md"
      >
        <Search class="text-black w-5 h-5" />
        <Input
          v-model="searchQuery"
          placeholder="Search..."
          class="flex-1 pl-2 outline-none border-none"
        />
        <button @click="toggleSearch" class="ml-2 text-gray-500 hover:text-gray-700">
          <X class="w-5 h-5" />
        </button>
      </div>
    </div>

    <div class="flex items-center space-x-4 pr-2 sm:pr-6">
      <div class="relative">
        <button class="relative p-1 text-white h-[3rem]">
          <BellDot class="w-[3.2rem] h-8 sm:w-6 sm:h-6" />
        </button>

        <!-- notification dot above bell -->
        <span
          v-if="hasNotification"
          class="absolute top-[0.8rem] right-[1rem] sm:top-[1rem] sm:right-[0.4rem] w-[0.7rem] h-[0.7rem] sm:w-2 sm:h-2 bg-red-500 rounded-full"
        ></span>
      </div>

      <div class="hidden sm:flex text-white font-medium">Welcome, Username</div>

      <img
        src="@/assets/profile.png"
        alt="Profile"
        class="w-10 h-10 rounded-full border border-white hidden sm:flex"
      />
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

.my-icon {
  width: 24px;
  height: 24px;
}

.mobile-noti-dot {
}
</style>
