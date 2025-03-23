import { defineStore } from 'pinia'
import { ref } from 'vue'
import { toast } from 'vue-sonner'

import { getArticles } from '@/api/articles'
import type { Article, CountData } from '@/types/article'

export const useArticleStore = defineStore('article', () => {
  const countData = ref<CountData | null>(null)
  const articles = ref<Article[]>([])
  const currentPage = ref<number>(1)
  const displayNumber = 5
  const isLoading = ref(false)
  const isFetched = ref(false)
  const totalPages = ref(1)
  const sortOption = ref<string>('')

  const fetchArticles = async (page: number) => {
    isLoading.value = true
    await getArticles({
      displayNumber,
      pageNumber: page,
      status: 4,
    })
      .then((response) => {
        countData.value = response.countData
        articles.value = response.allArticles
        isFetched.value = true
        isLoading.value = false
      })
      .catch((error) => {
        isLoading.value = false
        toast.error(error.response.data.message)
      })
  }

  return {
    countData,
    articles,
    isLoading,
    currentPage,
    displayNumber,
    isFetched,
    totalPages,
    sortOption,
    fetchArticles,
  }
})
