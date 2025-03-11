<script setup lang="ts">
import { EyeIcon, HeartIcon } from 'lucide-vue-next'
import { defineProps } from 'vue'
import { Card } from '@/components/ui/card'

interface Article {
  title: string
  date: string
  totalLikes: number
  totalViews: number
  status: string
  description: string
  id: string | number
}

// define props
const props = defineProps<{
  article: Article
}>()

const article = props.article
</script>

<template>
  <router-link :to="`/articles/${article.id}`" class="block">
    <Card 
      :key="article.title" 
      class="p-4 flex flex-col gap-4 cursor-pointer hover:shadow-md transition-shadow"
    >
      <div class="flex justify-between items-top">
        <div class="flex flex-col gap-2">
          <h3 class="font-semibold">{{ article.title }}</h3>
          <p class="text-sm">{{ article.date }}</p>
        </div>
        <div class="flex flex-row gap-3 h-fit">
          <div class="flex gap-2 items-center">
            <HeartIcon class="h-4 w-4" />
            <p class="text-sm">{{ article.totalLikes }}</p>
          </div>
          <div class="flex gap-2 items-center">
            <EyeIcon class="h-4 w-4" />
            <p class="text-sm">{{ article.totalViews }}</p>
          </div>
          <div class="text-sm w-fit p-1 px-2 bg-secondary text-white rounded-md">
            {{ article.status }}
          </div>
        </div>
      </div>
      <p v-if="article.description" class="text-sm mb-0">{{ article.description }}</p>
    </Card>
  </router-link>
</template>