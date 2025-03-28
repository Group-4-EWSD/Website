import { defineStore } from 'pinia'
import { ref } from 'vue'

import type { CoordinatorArticle } from '@/types/article'

export const useArticleStore = defineStore('coordinator-article', () => {
  const articles = ref<CoordinatorArticle[]>([])
})
