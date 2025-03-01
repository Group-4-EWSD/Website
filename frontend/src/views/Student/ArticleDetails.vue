<script setup lang="ts">
import { useRoute } from 'vue-router'
import Layout from '@/components/ui/Layout.vue'
import Button from '@/components/ui/button/Button.vue'
import { Heart, Eye, Download, FileText, Send } from 'lucide-vue-next'

const route = useRoute()

const articleId = route.params.id
const article = {
  id: 1,
  title: 'ARTICLES 1.1 - The Power of Picture by Zar Li',
  description:
    'Eight months after the Civil War began, in December 1861, Frederick Douglass spoke at Boston’s Tremont...',
  author: 'Zar Li',
  publishedDate: '15 Jan 2025',
  content:
    'Eight months after the Civil War began, in December 1861, Frederick Douglass spoke at Boston’s Tremont Temple about photography, emphasizing its power to reshape American perceptions and challenge racist stereotypes.Eight months after the Civil War began, in December 1861, Frederick Douglass spoke at Boston’s Tremont Temple about photography, emphasizing its power to reshape American perceptions and challenge racist stereotypes.Eight months after the Civil War began, in December 1861, Frederick Douglass spoke at Boston’s Tremont Temple about photography, emphasizing its power to reshape American perceptions and challenge racist stereotypes.Eight months after the Civil War began, in December 1861, Frederick Douglass spoke at Boston’s Tremont Temple about photography.',
  likeCount: 25,
  viewCount: 30,
}
const newComment = ''
const comments = [
  { id: 1, name: 'Swe Thu Htet', text: 'Great article!' },
  { id: 2, name: 'Diana Zar', text: 'I found this very insightful.' },
]
</script>

<template>
  <Layout>
    <div class="flex justify-between items-center pb-2">
      <h2 class="text-2xl font-bold uppercase">{{ article.title }}</h2>
      <div class="flex gap-3 text-gray-600">
        <Heart class="w-6 h-6 cursor-pointer hover:text-black" />{{ article.likeCount }}
        <span class="hidden sm:inline">Likes</span>
        <Eye class="w-6 h-6 cursor-pointer hover:text-black" />{{ article.viewCount }}
        <span class="hidden sm:inline">Views</span>
      </div>
    </div>
    <div class="flex justify-between items-center py-4 border-b">
      <div class="text-gray-700">
        <p class="font-semibold">{{ article.author }}</p>
        <p class="text-sm text-gray-500">{{ article.publishedDate }}</p>
      </div>

      <div class="flex gap-4">
        <button
          class="flex items-center gap-2 px-3 py-2 border rounded-lg bg-gray-50 text-gray-700 hover:bg-gray-100"
        >
          <Download class="w-4 h-4 text-gray-700" />
          <span class="hidden sm:inline text-sm">Download Doc</span>
        </button>
        <button
          class="flex items-center gap-2 px-3 py-2 border rounded-lg bg-gray-50 text-gray-700 hover:bg-gray-100"
        >
          <FileText class="w-4 h-4 text-gray-700" />
          <span class="hidden sm:inline text-sm">View Feedback</span>
        </button>
      </div>
    </div>
    <div class="mt-4">
      <div class="flex gap-3">
        <img
          src="@/assets/Pasted Graphic.png"
          alt="Article Image"
          class="w-[550px] max-h-[400px]"
        />
        <img
          src="@/assets/Pasted Graphic 1.png"
          alt="Article Image"
          class="w-[550px] max-h-[400px]"
        />
      </div>

      <p class="mt-4 text-lg text-gray-700 leading-relaxed">
        {{ article.content }}
      </p>
    </div>
    <div class="border-t pt-4 mt-6">
      <h3 class="text-lg font-semibold mb-3">Comments</h3>

      <!-- comment input -->
      <div class="flex items-start gap-3 mb-4">
        <img src="@/assets/profile.png" alt="User Avatar" class="w-10 h-10 rounded-full" />
        <div class="flex items-center space-x-2 w-full">
          <input
            v-model="newComment"
            placeholder="Write a comment..."
            class="flex-1 p-2 border rounded-md focus:outline-none focus:ring"
          />
          <Button @click="" class="py-5"><Send class="w-5 h-5" /></Button>
        </div>
      </div>

      <!-- comment list -->
      <div v-for="comment in comments" :key="comment.id" class="flex items-start gap-3 mb-4">
        <img src="@/assets/profile.png" alt="User Avatar" class="w-10 h-10 rounded-full" />
        <div class="flex-1">
          <p class="font-semibold">{{ comment.name }}</p>
          <p class="text-gray-700">{{ comment.text }}</p>
        </div>
      </div>
    </div>
  </Layout>
</template>
