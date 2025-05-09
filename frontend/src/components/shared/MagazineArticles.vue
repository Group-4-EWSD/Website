<script setup lang="ts">
import maleAvatar from '@/assets/male-avatar.png'
import femaleAvatar from '@/assets/female-avatar.png'
import Card from '@/components/ui/card/Card.vue'
import type { Article } from '@/types/coordinator'

const props = defineProps<{
  articles: Article[]
  isLoading: boolean
  year?: string | null
}>()
</script>

<template>
  <Card class="p-4">
    <h2 v-if="props.year" class="text-lg font-bold">
      AURORA Magazine Articles of the Year {{ year }}
    </h2>
    <h2 v-if="!props.year" class="text-lg font-bold">AURORA Magazine Articles of the Year</h2>
    <div class="space-y-2 mt-4">
      <template v-if="isLoading">
        <div
          v-for="index in 10"
          :key="index"
          class="border-b rounded-md p-2 animate-pulse flex items-center space-x-4"
        >
          <div class="w-12 h-12 bg-gray-200 rounded-full"></div>
          <div class="space-y-2 flex-1">
            <div class="h-4 bg-gray-200 rounded w-3/4"></div>
            <div class="h-3 bg-gray-200 rounded w-1/2"></div>
          </div>
        </div>
      </template>

      <template v-else>
        <div class="max-h-[700px] overflow-y-auto">
          <div
            v-if="props.articles.length > 0"
            v-for="(article, index) in articles"
            :key="article.article_id"
            class="border-b rounded-md hover:bg-gray-100 transition-all cursor-pointer p-2"
          >
            <RouterLink
              :to="`/articles/${article.article_id}`"
              class="text-primary font-semibold cursor-pointer"
            >
              <div class="flex items-center space-x-4">
                <img
                  :src="
                    article.user_photo_path || (article.gender === 1 ? maleAvatar : femaleAvatar)
                  "
                  alt="Author"
                  class="w-12 h-12 rounded-full object-cover aspect-square"
                />
                <div>
                  {{ article.article_title }} by {{ article.user_name }}
                  <p class="text-gray-500 text-sm">{{ article.article_description }}</p>
                </div>
              </div>
            </RouterLink>
          </div>
          <div
            v-else
            class="flex items-center justify-center h-64 text-gray-500 text-lg font-semibold"
          >
            There is no article to show.
          </div>
        </div>
      </template>
    </div>
  </Card>
</template>
