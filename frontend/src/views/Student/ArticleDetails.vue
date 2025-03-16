<script setup lang="ts">
import dayjs from 'dayjs'
import relativeTime from 'dayjs/plugin/relativeTime'
import { Download, Eye, Heart, Send } from 'lucide-vue-next'
import { ref, onMounted } from 'vue'
import { useRoute } from 'vue-router'
import { getArticleDetails } from '@/api/articles'
import type { ArticleResponse } from '@/types/article'
import { createComment, type actionParams } from '@/api/notification'

import Button from '@/components/ui/button/Button.vue'
import Layout from '@/components/ui/Layout.vue'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs'

const route = useRoute()

const activeTab = ref('comments')
const article = ref<ArticleResponse>({
  articleDetail: null,
  articleContent: [],
  articlePhotos: {},
  commentList: [],
  feedbackList: [],
})
const isLiked = ref(false)
const newComment = ref('')
const comments: any = ref([])
const feedbacks: any = ref([])
const authorizedFeedbacks = ref<boolean>(false)
const articleContent: any = ref([])
const articlePhotos = ref<Record<string, string>>({
  '67cbf0b9c7b5d_Exercise Activity 4.jpg':
    'https://ewsdcloud.s3.ap-southeast-1.amazonaws.com/documents/1741418681_victor shoes.jpg',
})

dayjs.extend(relativeTime)

// const comments = ref([
//   { id: 1, name: 'Alice', text: 'This is a great post!', timestamp: '2025-03-01 14:30' },
//   { id: 2, name: 'Bob', text: 'I totally agree with this.', timestamp: '2025-03-05 21:30' },
// ])

// const feedbacks = ref([
//   {
//     id: 1,
//     name: 'Charlie',
//     text: 'This article could be improved with more examples.',
//     timestamp: '2025-03-01 14:30',
//   },
//   {
//     id: 2,
//     name: 'David',
//     text: 'Great explanation, but I had some confusion with one part.',
//     timestamp: '2025-03-05 21:30',
//   },
// ])

const addComment = () => {
  if (newComment.value.trim()) {
    comments.value.push({
      id: Date.now(),
      name: 'You',
      text: newComment.value,
      timestamp: dayjs().toISOString(),
    })

    try {
      createComment({
        actionId: null,
        actionType: 2,
      })
    } catch (error) {
      console.error('Error updating notification:', error)
    }
    newComment.value = ''
  }
}

const fetchArticleDetails = async (articleId: string) => {
  try {
    const response = await getArticleDetails(articleId)
    article.value = response.data

    articleContent.value = article.value.articleContent
    articlePhotos.value = article.value.articlePhotos
    comments.value = article.value.commentList

    authorizedFeedbacks.value = article.value.feedbackList.length > 0 ? true : false
  } catch (error) {
    console.error('Error fetching article details:', error)
  }
}

const updateLike = () => {
  isLiked.value = !isLiked.value
}

onMounted(() => {
  const articleId = route.params.id as string
  fetchArticleDetails(articleId)
})
</script>

<template>
  <Layout>
    <div v-if="article.articleDetail">
      <div class="flex justify-between items-center pb-2">
        <h2 class="text-2xl font-bold uppercase">{{ article.articleDetail.article_title }}</h2>
        <div class="flex gap-2 text-gray-600">
          <button @click="updateLike">
            <Heart
              class="w-6 h-6 cursor-pointer transition-colors duration-200"
              :class="{ 'text-red-500 fill-red-500': isLiked, 'hover:text-black': !isLiked }"
            />
          </button>
          {{ article.articleDetail.like_count }}
          <span class="hidden sm:inline">Likes</span>
          <Eye class="w-6 h-6 cursor-pointer hover:text-black" />{{
            article.articleDetail.view_count
          }}
          <span class="hidden sm:inline">Views</span>
        </div>
      </div>
      <div class="flex justify-between items-center py-4 border-b">
        <div class="text-gray-700">
          <p class="font-semibold">{{ article.articleDetail.creator_name }}</p>
          <p class="text-sm text-gray-500">
            {{ dayjs(article.articleDetail.created_at).format('MMM D, YYYY') }}
          </p>
        </div>

        <div class="flex gap-4">
          <Button
            class="flex items-center gap-2 px-3 py-2 border rounded-lg bg-gray-50 text-gray-700 hover:bg-gray-100"
          >
            <Download class="w-4 h-4 text-gray-700" />
            <span class="hidden sm:inline text-sm">Download Doc</span>
          </Button>
        </div>
      </div>
      <div class="mt-4">
        <div class="flex gap-3">
          <img
            v-for="(url, name) in articlePhotos"
            :key="name"
            :src="url"
            :alt="`Article Image - ${name}`"
            class="w-[350px] max-h-[350px]"
          />
        </div>

        <p class="mt-4 text-lg text-gray-700 leading-relaxed">
          <template v-if="articleContent.length > 0">
            {{ articleContent }}
          </template>
          <template v-else>
            <div class="flex justify-center w-full py-4">
              No content available for this article.
            </div>
          </template>
        </p>
      </div>
      <div class="border-t pt-4 mt-6">
        <Tabs v-model="activeTab">
          <TabsList class="mb-4 border-b">
            <TabsTrigger value="comments" class="p-2">Comments</TabsTrigger>
            <TabsTrigger v-if="true" value="feedbacks" class="p-2">Feedbacks</TabsTrigger>
          </TabsList>
          <div class="w-full min-h-[250px]">
            <!-- Comments Tab -->
            <TabsContent value="comments">
              <div
                v-for="comment in comments"
                :key="comment.id"
                class="flex items-start gap-3 mb-4"
              >
                <img src="@/assets/profile.png" alt="User Avatar" class="w-10 h-10 rounded-full" />
                <div class="flex-1">
                  <div class="flex justify-between items-start">
                    <p class="font-semibold">{{ comment.name }}</p>
                    <p class="text-gray-700">{{ dayjs(comment.timestamp).fromNow() }}</p>
                  </div>
                  <p class="text-gray-700">{{ comment.text }}</p>
                </div>
              </div>

              <div class="flex items-start gap-3 mb-4">
                <img src="@/assets/profile.png" alt="User Avatar" class="w-10 h-10 rounded-full" />
                <div class="flex items-center space-x-2 w-full">
                  <input
                    v-model="newComment"
                    placeholder="Write a comment..."
                    class="flex-1 p-2 border rounded-md focus:outline-none focus:ring focus:ring-secondary"
                    @keydown.enter="addComment()"
                  />
                  <Button
                    @click="addComment"
                    class="flex items-center justify-center px-4 py-5 bg-primary text-white rounded-lg hover:bg-secondary focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition"
                  >
                    <Send class="w-5 h-5" />
                    <span>Send</span>
                  </Button>
                </div>
              </div>
            </TabsContent>

            <!-- Feedbacks Tab -->
            <div v-if="authorizedFeedbacks">
              <TabsContent value="feedbacks">
                <div
                  v-for="feedback in feedbacks"
                  :key="feedback.id"
                  class="flex items-start gap-3 mb-4"
                >
                  <img
                    src="@/assets/profile.png"
                    alt="User Avatar"
                    class="w-10 h-10 rounded-full"
                  />
                  <div class="flex-1">
                    <div class="flex justify-between items-start">
                      <p class="font-semibold">{{ feedback.name }}</p>
                      <p class="text-gray-700">{{ dayjs(feedback.timestamp).fromNow() }}</p>
                    </div>
                    <p class="text-gray-700">{{ feedback.text }}</p>
                  </div>
                </div>
              </TabsContent>
            </div>
          </div>
        </Tabs>
      </div>
    </div>
  </Layout>
</template>
