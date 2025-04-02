import { getAllArticles } from '@/api/coordinator'
import type { CoordinatorArticle, CountData, Article, GuestList } from '@/types/coordinator'
import { defineStore } from 'pinia'
import { ref } from 'vue'
import { toast } from 'vue-sonner'

export const useCoordinatorStore = defineStore('coordinator-article', () => {
  const countData = ref<CountData | null>(null)
  const articles = ref<Article[]>([])
  const guestList = ref<GuestList[]>([])
  const prevLogin = ref('')
  const publicDate = ref(0)

  const isLoading = ref(false)
  const error = ref<string | null>(null)

  const fetchArticles = async () => {
    isLoading.value = true
    error.value = null

    await getAllArticles()
      .then((response) => {
        countData.value = response.countData
        articles.value = response.allArticles
        guestList.value = response.guestList
        prevLogin.value = response.prev_login
        publicDate.value = response.remaining_final_publish

        isLoading.value = false
      })
      .catch((error) => {
        isLoading.value = false
        toast.error(error.response.data.message)
        console.error('Error fetching articles:', error)
        error.value = 'Failed to load articles. Please try again.'
      })
  }

  return {
    countData,
    guestList,
    articles,
    prevLogin,
    publicDate,
    isLoading,

    fetchArticles,
  }
})
