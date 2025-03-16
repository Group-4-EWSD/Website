import { logout } from '@/api/auth'
import router from '@/router'
import type { Updater } from '@tanstack/vue-table'
import type { Ref } from 'vue'
import { type ClassValue, clsx } from 'clsx'
import { twMerge } from 'tailwind-merge'
import { useCookies } from 'vue3-cookies'

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs))
}

export function valueUpdater<T extends Updater<any>>(updaterOrValue: T, ref: Ref) {
  ref.value = typeof updaterOrValue === 'function' ? updaterOrValue(ref.value) : updaterOrValue
}

/**
 * Force sign out by removing token from cookies and redirecting to login
 */
export async function forceSignOut() {
  await logout()

  // Remove token from cookies
  const { cookies } = useCookies()
  cookies.keys().forEach((element) => {
    console.log('element', element)
    cookies.remove('token')
  })

  // Redirect to login
  router.replace('/auth/login')
}
