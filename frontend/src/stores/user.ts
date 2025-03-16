import { defineStore } from 'pinia'
import type { User } from '@/types/user'
import { ref } from 'vue'

export const useUserStore = defineStore('user', () => {
  const current_user = ref<User>()
  const set_current_user = (user: User) => {
    current_user.value = user
  }

  return {
    current_user,
    set_current_user,
  }
})
