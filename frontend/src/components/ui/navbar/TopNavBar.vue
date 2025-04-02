<script setup lang="ts">
import { BellDot, Search, X } from 'lucide-vue-next'
import { computed, ref } from 'vue'

import { useUserStore } from '@/stores/user'

import Button from '../button/Button.vue'
import Input from '../input/Input.vue'

const searchQuery = ref('')
const hasNotification = true
const isSearchActive = ref(false)
const toggleSearch = () => {
  isSearchActive.value = !isSearchActive.value
}
const userStore = useUserStore()

const notiUrl = computed(() => {
  switch (userStore.currentUser?.user_type_name) {
    case 'Student':
      return '/student/notifications'
    case 'Coordinator':
      return '/coordinator/notifications'
    default:
      return ''
  }
})
</script>

<template>
  <nav class="flex justify-between items-center bg-primary text-white h-[65px]">
    <div class="flex items-center">
      <img
        src="@/assets/nav-logo.png"
        alt="University magazine logo"
        class="h-12 sm:h-14 w-auto max-h-full object-contain pl-[4.5rem] sm:pl-10"
      />
      <span class="text-xl font-bold uppercase pl-[18px] uni-color">Aurora</span>
      <span class="text-md font-bold uppercase pl-[7px] name-color">University</span>
    </div>

    <!-- <div class="flex flex-1 justify-center">
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
        class="absolute top-3 sm:left-0 w-[45vw] sm:w-[90vw] flex items-center bg-white border rounded-lg px-3 py-1 shadow-md"
      >
        <Search class="text-black w-5 h-5" />
        <Input
          v-model="searchQuery"
          placeholder="Search..."
          class="flex-1 pl-2 outline-none border-none"
        />
        <button @click="toggleSearch" class="ml-2 text-gray-700">
          <X class="w-5 h-5" />
        </button>
      </div>
    </div> -->

    <div class="flex items-center space-x-4 pr-2 sm:pr-6">
      <div class="relative">
        <RouterLink :to="notiUrl">
          <button class="relative p-1 text-white h-[3rem]">
            <BellDot class="w-[3.2rem] h-8 sm:w-6 sm:h-6" />
          </button>

          <span
            v-if="hasNotification"
            class="absolute top-[0.8rem] right-[1rem] sm:top-[1rem] sm:right-[0.37rem] w-[0.7rem] h-[0.7rem] sm:w-2 sm:h-2 bg-red-500 rounded-full"
          ></span>
        </RouterLink>
      </div>

      <!-- <div class="hidden sm:flex text-white font-medium">Welcome, Username</div> -->

      <div class="hidden sm:flex flex-col text-white font-medium">
        <p>Welcome, {{ userStore.currentUser?.user_name }}</p>
        <p class="text-sm text-gray-300">
          {{ userStore.currentUser?.faculty_name }} - {{ userStore.currentUser?.user_type_name }}
        </p>
      </div>

      <img
        :src="userStore.currentUser?.user_photo_path"
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

.name-color {
  color: #8aa8b2;
}

.uni-color {
  color: #e5e0da;
}
</style>
