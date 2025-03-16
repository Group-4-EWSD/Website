import { defineStore } from 'pinia'
import { ref } from 'vue'
import type { Article, CountData } from '@/types/article'
import { getArticles } from '@/api/articles'

export const useArticleStore = defineStore('article', () => {
  const countData = ref<CountData | null>(null)
  const articles = ref<Article[]>([])
  const currentPage = ref<number>(1)
  const displayNumber = 5
  const isFetched = ref(false)
  const totalPages = ref(1)
  const sortOption = ref<string>('')

  const fetchArticles = async (page: number) => {
    try {
      const response = await getArticles({
        displayNumber,
        pageNumber: page,
        status: 4,
      })
      countData.value = response.countData
      articles.value = response.allArticles
      isFetched.value = true
    } catch (error) {
      console.error('Error fetching articles:', error)
    }
  }

  return {
    countData,
    articles,
    currentPage,
    displayNumber,
    isFetched,
    totalPages,
    sortOption,
    fetchArticles,
  }
})
