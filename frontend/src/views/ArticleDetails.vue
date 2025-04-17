<script setup lang="ts">
import dayjs from 'dayjs'
import relativeTime from 'dayjs/plugin/relativeTime'
import { Download, Eye, Heart, Send } from 'lucide-vue-next'
import { computed, onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import { toast } from 'vue-sonner'

import {
  type ActionParams,
  type Comment,
  createComment,
  createFeedback,
  getArticleDetails,
  updateStatus,
} from '@/api/articles'
import Button from '@/components/ui/button/Button.vue'
import Layout from '@/components/ui/Layout.vue'
import Skeleton from '@/components/ui/skeleton/Skeleton.vue'
import { Tabs, TabsContent, TabsList, TabsTrigger } from '@/components/ui/tabs'
import { useUserStore } from '@/stores/user'
import type { ArticleResponse } from '@/types/article'

const route = useRoute()
const userStore = useUserStore()

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
const newFeedback = ref('')
const articleId = ref('')
const comments = ref<Comment[]>([])
const feedbacks = ref<Comment[]>([])
const isLoading = ref(false)
const approveLoading = ref(false)
const rejectLoading = ref(false)

const articleContent: any = ref([])
const articlePhotos = ref<Record<string, string>>({
  '67cbf0b9c7b5d_Exercise Activity 4.jpg':
    'https://ewsdcloud.s3.ap-southeast-1.amazonaws.com/documents/1741418681_victor shoes.jpg',
})

const params = ref<ActionParams>()

dayjs.extend(relativeTime)

const fetchArticleDetails = async (articleId: string) => {
  isLoading.value = true

  try {
    const response = await getArticleDetails(articleId)
    article.value = response.data

    articleContent.value = article.value.articleContent
    articlePhotos.value = article.value.articlePhotos
    comments.value = article.value.commentList
    feedbacks.value = article.value.feedbackList

    isLoading.value = false
  } catch (error) {
    isLoading.value = false
    console.error('Error fetching article details:', error)
    toast.error('Error fetching article details')
  }
}

const addComment = () => {
  if (newComment.value.trim()) {
    comments.value.push({
      id: Date.now(),
      user_name: userStore.user?.user_name || 'User',
      message: newComment.value,
      created_at: dayjs().toISOString(),
    })

    try {
      params.value = {
        articleId: articleId.value,
        message: newComment.value,
      }
      createComment(params.value)
    } catch (error) {
      console.error('Error updating notification:', error)
      toast.error('Failed to comment')
    }
    newComment.value = ''
  }
}

const addFeedback = () => {
  if (newFeedback.value.trim()) {
    feedbacks.value.push({
      id: Date.now(),
      user_name: userStore.user?.user_name || 'User',
      message: newFeedback.value,
      created_at: dayjs().toISOString(),
    })

    try {
      params.value = {
        articleId: articleId.value,
        message: newFeedback.value,
      }
      createFeedback(params.value)
    } catch (error) {
      console.error('Error updating notification:', error)
      toast.error('Failed to comment')
    }
    newFeedback.value = ''
  }
}

const updateLike = () => {
  isLiked.value = !isLiked.value
}

onMounted(() => {
  articleId.value = route.params.id as string
  fetchArticleDetails(articleId.value)

  activeTab.value =
    userStore.currentUser?.user_type_name === 'Marketing Coordinator' ? 'feedbacks' : 'comments'
})

const handleApprove = async () => {
  approveLoading.value = true
  try {
    await updateStatus(2, articleId.value)
  } catch (error) {
    console.error('Error during status update:', error)
    toast.error('Failed to update article status.')
  } finally {
    approveLoading.value = false
  }
}

const handleReject = async () => {
  rejectLoading.value = true
  try {
    await updateStatus(3, articleId.value)
  } catch (error) {
    console.error('Error during status update:', error)
    toast.error('Failed to update article status.')
  } finally {
    rejectLoading.value = false
  }
}

const sortedComments = computed(() =>
  comments.value
    .slice()
    .sort((a, b) => new Date(a.created_at).getTime() - new Date(b.created_at).getTime()),
)

const isCoordinator = computed(() => {
  return userStore.currentUser?.user_type_name === 'Marketing Coordinator'
})

const isStudent = computed(() => {
  return userStore.currentUser?.user_type_name === 'Student'
})

const isManager = computed(() => {
  return userStore.currentUser?.user_type_name === 'Marketing Manager'
})

const isGuest = computed(() => {
  return userStore.currentUser?.user_type_name === 'Guest'
})

const isOwnArticle = computed(() => {
  return userStore.currentUser?.user_name === article.value.articleDetail?.creator_name
})

const articleStatus = computed(() => article.value.articleDetail?.article_status)

const isApproved = computed(() => articleStatus.value === 2)
const isRejected = computed(() => articleStatus.value === 3)
const isPublished = computed(() => articleStatus.value === 4)
</script>

<template>
  <Layout>
    <template v-if="isLoading">
      <div class="space-y-6">
        <!-- top title & like count & view count -->
        <div class="flex items-center justify-between">
          <Skeleton class="w-32 h-6" />
          <div class="flex space-x-4">
            <Skeleton class="w-6 h-6 rounded-full" />
            <Skeleton class="w-16 h-6" />
            <Skeleton class="w-6 h-6 rounded-full" />
            <Skeleton class="w-16 h-6" />
          </div>
        </div>
        <!-- author, date & download -->
        <div class="flex justify-between items-center">
          <div class="space-y-2">
            <Skeleton class="w-24 h-4" />
            <Skeleton class="w-20 h-4" />
          </div>

          <Skeleton class="w-28 h-10 rounded-lg" />
        </div>
        <!-- content -->
        <div class="h-40 flex items-center justify-center border rounded-lg">
          <Skeleton class="w-48 h-10" />
        </div>
        <!-- comment & feedback tabs -->
        <div class="flex space-x-4">
          <Skeleton class="w-20 h-8 rounded-lg" />
          <Skeleton class="w-20 h-8 rounded-lg" />
        </div>
        <!-- comment -->
        <div class="flex items-center space-x-4">
          <Skeleton class="w-10 h-10 rounded-full" />
          <Skeleton class="flex-1 h-10 rounded-lg" />
          <Skeleton class="w-16 h-10 rounded-lg" />
        </div>
      </div>
    </template>
    <template v-else>
      <div v-if="article.articleDetail && !isLoading">
        <div class="flex justify-between items-center pb-2">
          <h2 class="text-2xl font-bold uppercase">{{ article.articleDetail.article_title }}</h2>
          <div v-if="isStudent" class="flex gap-2 text-gray-600">
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
            <!-- Approve -->
            <Button
              v-if="isCoordinator && !isPublished && !isRejected"
              :disabled="false"
              :processing="false"
              class="px-4 py-2 rounded-md border bg-green-100 text-gray-800 hover:bg-green-200 text-sm font-medium"
              @click="!isApproved ? null : handleApprove"
            >
              {{ isApproved ? 'Approved' : 'Approve' }}
            </Button>

            <!-- Reject -->
            <Button
              v-if="isCoordinator && !isPublished && !isApproved"
              :disabled="false"
              :processing="false"
              class="px-4 py-2 rounded-md border bg-red-100 text-gray-800 hover:bg-red-200 text-sm font-medium"
              @click="!isApproved ? null : handleReject"
            >
              {{ isRejected ? 'Rejected' : 'Reject' }}
            </Button>

            <!-- Download -->
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
              class="w-[600px] max-h-[600px]"
            />
          </div>

          <p class="mt-4 text-lg text-gray-700 leading-relaxed">
            <template v-if="articleContent">
              {{ Object.values(articleContent)[0] }}
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
              <TabsTrigger
                v-if="(isStudent && !isCoordinator) || isOwnArticle || isManager || isGuest"
                value="comments"
                class="p-2"
                >Comments</TabsTrigger
              >
              <TabsTrigger v-if="isCoordinator || isOwnArticle" value="feedbacks" class="p-2"
                >Feedbacks</TabsTrigger
              >
            </TabsList>
            <div class="w-full min-h-[250px]">
              <!-- Comments Tab -->
              <TabsContent
                v-if="activeTab === 'comments'"
                value="comments"
                class="shadow-md border border-gray-200 rounded-lg p-4 flex flex-col max-h-[500px]"
              >
                <div class="flex-1 overflow-y-auto pr-2">
                  <div
                    v-for="comment in sortedComments"
                    :key="comment.id"
                    class="flex items-start gap-3 mb-4"
                  >
                    <img
                      src="@/assets/profile.png"
                      alt="User Avatar"
                      class="w-10 h-10 rounded-full"
                    />
                    <div class="flex-1">
                      <div class="flex justify-between items-start">
                        <p class="font-semibold">{{ comment.user_name }}</p>
                        <p class="text-gray-700">{{ dayjs(comment.created_at).fromNow() }}</p>
                      </div>
                      <p class="text-gray-700">{{ comment.message }}</p>
                    </div>
                  </div>
                </div>

                <div class="flex items-start gap-3 mb-2 mt-2">
                  <img
                    src="@/assets/profile.png"
                    alt="User Avatar"
                    class="w-10 h-10 rounded-full"
                  />
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
              <TabsContent
                v-if="activeTab === 'feedbacks'"
                value="feedbacks"
                class="shadow-md border border-gray-200 rounded-lg p-4 flex flex-col max-h-[500px]"
              >
                <div class="flex-1 overflow-y-auto pr-2">
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
                        <p class="font-semibold">{{ feedback.user_name }}</p>
                        <p class="text-gray-700">{{ dayjs(feedback.created_at).fromNow() }}</p>
                      </div>
                      <p class="text-gray-700">{{ feedback.message }}</p>
                    </div>
                  </div>
                </div>
                <div class="flex items-start gap-3 mb-2 mt-2">
                  <img
                    src="@/assets/profile.png"
                    alt="User Avatar"
                    class="w-10 h-10 rounded-full"
                  />
                  <div class="flex items-center space-x-2 w-full">
                    <input
                      v-model="newFeedback"
                      placeholder="Write a feedback..."
                      class="flex-1 p-2 border rounded-md focus:outline-none focus:ring focus:ring-secondary"
                      @keydown.enter="addFeedback"
                    />
                    <Button
                      @click="addFeedback"
                      class="flex items-center justify-center px-4 py-5 bg-primary text-white rounded-lg hover:bg-secondary focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition"
                    >
                      <Send class="w-5 h-5" />
                      <span>Send</span>
                    </Button>
                  </div>
                </div>
              </TabsContent>
            </div>
          </Tabs>
        </div>
      </div>
      <div v-else>
        <div class="flex flex-col items-center justify-center">
          <p>Something went wrong, please</p>
          <Button variant="outline" size="sm" class="ml-2" @click="fetchArticleDetails(articleId)">
            Try Again
          </Button>
        </div>
      </div>
    </template>
  </Layout>
</template>
