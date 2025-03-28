import { defineStore } from 'pinia'
import { toast } from 'vue-sonner'

import { deleteProfilePhoto } from '@/api/user'
import { setCookie } from '@/lib/utils'
import type { User } from '@/types/user'

interface UserState {
  user: User | null
  isAuthenticated: boolean
  loading: boolean
  error: string | null
}

export const useUserStore = defineStore('user', {
  state: (): UserState => ({
    user: null,
    isAuthenticated: false,
    loading: false,
    error: null,
  }),

  getters: {
    currentUser: (state): User | null => state.user,

    loggedIn: (state): boolean => state.isAuthenticated,

    fullName: (state): string => {
      if (!state.user) return ''
      return state.user.user_name
    },

    nickname: (state): string => {
      return state.user?.nickname || ''
    },

    // isAdmin: (state): boolean => state.user?.user_type_id === '1',

    profilePhotoUrl: (state): string => {
      return state.user?.user_photo_path || ''
    },
  },

  actions: {
    setUser(user: User): void {
      this.user = user
      setCookie('user', JSON.stringify(user))
      this.isAuthenticated = true
    },
    setProfilePhoto(photoPath: string): void {
      if (this.user) {
        this.user.user_photo_path = photoPath
        setCookie('user', JSON.stringify(this.user))
      }
    },
    async removeProfilePhoto(): Promise<void> {
      if (this.user) {
        try {
          await deleteProfilePhoto()
          this.user.user_photo_path = ''
          setCookie('user', JSON.stringify(this.user))
        } catch (error) {
          console.error(error)
          toast.error('Failed to delete profile photo. Please try again')
        }
      }
    },
  },
})
