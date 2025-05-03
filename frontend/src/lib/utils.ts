import type { Updater } from '@tanstack/vue-table'
import { type ClassValue, clsx } from 'clsx'
import { twMerge } from 'tailwind-merge'
import type { Ref } from 'vue'
import { useCookies } from 'vue3-cookies'

import { logout } from '@/api/auth'
import router from '@/router' // Import the router instance directly
import { setActivePinia } from 'pinia'
import { pinia } from '@/main'
import { resetAllStores } from '@/stores/resetStore'

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
  setActivePinia(pinia)

  if (clearToken) {
    await logout()
    // Remove token from cookies
    removeCookie('token')
    removeCookie('user')
    resetAllStores()
    // Redirect to login
    pushAndReload('/auth/login')
  } else {
    // Remove token from cookies
    removeCookie('token')
    removeCookie('user')
    resetAllStores()
    // Redirect to login
    pushAndReload('/auth/login')
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

// Helper function to calculate age from date of birth
export const calculateAge = (birthDate: string): number => {
  const today = new Date()
  const dob = new Date(birthDate)
  let age = today.getFullYear() - dob.getFullYear()
  const monthDiff = today.getMonth() - dob.getMonth()

  // If birth month is later in the year or same month but birth day is later, subtract one year
  if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
    age--
  }

  return age
}

export const getInitials = (name?: string): string => {
  if (!name) return ''
  return name
    .split(' ')
    .map((part) => part[0])
    .join('')
}

export function pushAndReload(path: string) {
  return new Promise((resolve) => {
    // Use router.push's promise to wait for navigation to complete
    router
      .push(path)
      .then(() => {
        // After navigation completes, reload the page
        window.location.reload()
        resolve(true)
      })
      .catch((err) => {
        console.error('Navigation error:', err)
        // Reload anyway in case of navigation errors
        window.location.reload()
        resolve(false)
      })
  })
}
