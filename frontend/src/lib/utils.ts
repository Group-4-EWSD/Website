import type { Updater } from '@tanstack/vue-table'
import { type ClassValue, clsx } from 'clsx'
import { twMerge } from 'tailwind-merge'
import type { Ref } from 'vue'
import { useCookies } from 'vue3-cookies'

import { logout } from '@/api/auth'
import router from '@/router' // Import the router instance directly

export function cn(...inputs: ClassValue[]) {
  return twMerge(clsx(inputs))
}

export function valueUpdater<T extends Updater<unknown>>(updaterOrValue: T, ref: Ref) {
  ref.value = typeof updaterOrValue === 'function' ? updaterOrValue(ref.value) : updaterOrValue
}

/**
 * Force sign out by removing token from cookies and redirecting to login
 */
export async function forceSignOut(clearToken: boolean = true) {
  console.log('forceSignOut')
  if (clearToken) {
    logout().then(() => {
      // Remove token from cookies
      removeCookie('token')
      removeCookie('user')
      // Redirect to login
      router.push('/auth/login')
    })
  } else {
    // Remove token from cookies
    removeCookie('token')
    removeCookie('user')
    // Redirect to login
    router.push('/auth/login')
  }
}

export function getCookie(name: string) {
  const { cookies } = useCookies()
  const cookieData = cookies.get(name)
  if (!cookieData) return null
  try {
    return JSON.parse(cookieData)
  } catch {
    return cookieData
  }
}

export function setCookie(
  name: string,
  value: string | Record<string, unknown>,
  expires: string = '365d',
) {
  const { cookies } = useCookies()

  if (typeof value === 'string') {
    cookies.set(name, value, expires, '/')
    return
  }

  cookies.set(name, JSON.stringify(value), expires, '/')
}

export function removeCookie(name: string) {
  const { cookies } = useCookies()
  cookies.remove(name, '/')
}
