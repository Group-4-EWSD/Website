<script setup lang="ts">
import { BellDot, Search, X } from 'lucide-vue-next'
import { ref } from 'vue'

import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import { getInitials } from '@/lib/utils'
import { useUserStore } from '@/stores/user'

const searchQuery = ref('')
const hasNotification = true
const isSearchActive = ref(false)
const toggleSearch = () => {
  isSearchActive.value = !isSearchActive.value
}
const userStore = useUserStore()
</script>

<template>
  <nav class="flex justify-between items-center bg-primary text-white h-[65px]">
    <RouterLink class="flex items-center" to="/home">
      <img
        src="@/assets/nav-logo.png"
        alt="University magazine logo"
        class="h-12 sm:h-14 w-auto max-h-full object-contain pl-[4.5rem] sm:pl-10"
      />
      <span class="text-xl font-bold uppercase pl-[18px] uni-color">Aurora</span>
      <span class="text-md font-bold uppercase pl-[7px] name-color">University</span>
    </RouterLink>

    <div class="flex items-center space-x-4 pr-2 sm:pr-6">
      <div class="relative hidden sm:block">
        <RouterLink :to="`/notifications`">
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
          <span
            v-if="
              userStore.currentUser?.user_type_name !== 'Admin' &&
              userStore.currentUser?.user_type_name !== 'Marketing Manager'
            "
            >{{ userStore.currentUser?.faculty_name }} - </span
          >{{ userStore.currentUser?.user_type_name }}
        </p>
      </div>

      <Avatar>
        <AvatarImage :src="userStore.currentUser?.user_photo_path || ''" />
        <AvatarFallback class="text-white">{{
          getInitials(userStore.currentUser?.user_name || '')
        }}</AvatarFallback>
      </Avatar>
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
