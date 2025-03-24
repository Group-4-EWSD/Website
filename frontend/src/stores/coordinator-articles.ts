import type { CoordinatorArticle } from '@/types/article'
import { defineStore } from 'pinia'
import { ref } from 'vue'

export const useArticleStore = defineStore('coordinator-article', () => {
  const articles = ref<CoordinatorArticle[]>([])
})
