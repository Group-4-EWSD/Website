import { getActivePinia } from 'pinia'

export function resetAllStores() {
  const pinia = getActivePinia() as any
  if (pinia && pinia._s) {
    pinia._s.forEach((store: any) => {
      store.reset?.()
    })
  }
}
