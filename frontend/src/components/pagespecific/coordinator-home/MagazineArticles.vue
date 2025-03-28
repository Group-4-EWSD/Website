<script setup lang="ts">
import { ref } from 'vue'
import Card from '@/components/ui/card/Card.vue'

const articleImageUrl = '@/assets/profile.png'

const articles = ref([
  {
    id: 1,
    title: 'The Power of Picture',
    author: 'Zar Li',
    preview: 'Eight months after the Civil War began, in December 1861 ......',
    avatar: '/avatars/avatar1.png',
  },
  {
    id: 2,
    title: 'Will AI replace the Arts?',
    author: 'Swe',
    preview: 'The rise of artificial intelligence (AI) has sparked numerous .....',
    avatar: '/avatars/avatar2.png',
  },
  {
    id: 3,
    title: 'The Power of Picture',
    author: 'Zar Li',
    preview: 'Eight months after the Civil War began, in December 1861 ....',
    avatar: '/avatars/avatar1.png',
  },
  {
    id: 4,
    title: 'The Power of Picture',
    author: 'Zar Li',
    preview: 'Eight months after the Civil War began, in December 1861 ....',
    avatar: '/avatars/avatar1.png',
  },
  {
    id: 5,
    title: 'The Power of Picture',
    author: 'Zar Li',
    preview: 'Eight months after the Civil War began, in December 1861 ....',
    avatar: '/avatars/avatar1.png',
  },
  {
    id: 6,
    title: 'The Power of Picture',
    author: 'Zar Li',
    preview: 'Eight months after the Civil War began, in December 1861 ....',
    avatar: '/avatars/avatar1.png',
  },
  {
    id: 7,
    title: 'The Power of Picture',
    author: 'Zar Li',
    preview: 'Eight months after the Civil War began, in December 1861 ....',
    avatar: '/avatars/avatar1.png',
  },
  {
    id: 8,
    title: 'The Power of Picture',
    author: 'Zar Li',
    preview: 'Eight months after the Civil War began, in December 1861 ....',
    avatar: '/avatars/avatar1.png',
  },
])
</script>

<template>
  <Card class="p-4">
    <h2 class="text-lg font-bold">AURORA Magazine Articles of the Year</h2>
    <div class="mt-2 space-y-4">
      <div
        v-for="(article, index) in articles"
        :key="article.id"
        class="border-b rounded-md hover:bg-gray-100 transition-all cursor-pointer p-2"
      >
        <RouterLink
          :to="`/articles/${article.id}`"
          class="text-primary font-semibold cursor-pointer"
        >
          <div class="flex items-center space-x-4">
            <img src="@/assets/profile.png" alt="Author" class="w-12 h-12 rounded-full" />
            <div>
              {{ article.title }} by {{ article.author }}
              <p class="text-gray-500 text-sm">{{ article.preview }}</p>
            </div>
          </div>
        </RouterLink>
      </div>
    </div>
  </Card>
</template>
