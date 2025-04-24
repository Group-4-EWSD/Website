<script setup lang="ts">
import { computed, onMounted, ref, watch } from 'vue'

import StatusIndicator from '@/components/shared/StatusIndicator.vue'
import TooltipWrapper from '@/components/shared/TooltipWrapper.vue'
import Button from '@/components/ui/button/Button.vue'
import Card from '@/components/ui/card/Card.vue'
import Layout from '@/components/ui/Layout.vue'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import { useCoordinatorStore } from '@/stores/coordinator'
import { publishArticles } from '@/api/coordinator'
import router from '@/router'

interface Article {
  article_id: string
  article_title: string
  submission_date: string
  submission_deadline: string
  status: number
  article_type_name: string
  user_name: string
}

const coordinatorStore = useCoordinatorStore()
const articles = ref<Article[]>([])

// Static mock data
// const articles = ref([
//   {
//     article_id: '1',
//     article_title: 'Quantum Entanglement Explained',
//     submission_date: '2025-03-01',
//     submission_deadline: '2025-03-10',
//     status: 2,
//     article_type_name: 'Physics',
//     user_name: 'Alice',
//   },
//   {
//     article_id: '2',
//     article_title: 'Algebraic Structures',
//     submission_date: '2025-03-02',
//     submission_deadline: '2025-03-12',
//     status: 2,
//     article_type_name: 'Mathematics',
//     user_name: 'Bob',
//   },
//   {
//     article_id: '3',
//     article_title: 'Organic Chemistry Basics',
//     submission_date: '2025-03-03',
//     submission_deadline: '2025-03-15',
//     status: 2,
//     article_type_name: 'Chemistry',
//     user_name: 'Carol',
//   },
// ])

onMounted(async () => {
  if (!coordinatorStore.approvedArticles.articles.length) {
    await coordinatorStore.fetchApprovedArticles()
  }

  articles.value = [...coordinatorStore.approvedArticles.articles]
})

const selectedIds = ref<string[]>([])

const isAllSelected = computed(
  () =>
    articles.value.length > 0 &&
    selectedIds.value.length === articles.value.length &&
    articles.value.every((article) => selectedIds.value.includes(article.article_id)),
)

const toggleSelectAll = () => {
  if (isAllSelected.value) {
    selectedIds.value = []
  } else {
    selectedIds.value = articles.value.map((a) => a.article_id)
  }
}

const toggleSelectRow = (id: string) => {
  if (selectedIds.value.includes(id)) {
    selectedIds.value = selectedIds.value.filter((i) => i !== id)
  } else {
    selectedIds.value.push(id)
  }
}

const publishSelected = async () => {
  try {
    await publishArticles(selectedIds.value)

    alert('Selected articles have been published!')

    await coordinatorStore.fetchApprovedArticles()
    articles.value = [...coordinatorStore.approvedArticles.articles]
    selectedIds.value = []
  } catch (error) {
    alert('Something went wrong while publishing.')
    console.error(error)
  }
}

watch(articles, (newArticles) => {
  selectedIds.value = selectedIds.value.filter((id) =>
    newArticles.some((article) => article.article_id === id),
  )
})

const goToArticleDetails = (articleId: string) => {
  if (!articleId) return
  router.push({ name: 'getArticleDetails', params: { id: articleId } })
}
</script>

<template>
  <Layout>
    <h2 class="text-xl font-bold mb-4 uppercase">Publish Articles</h2>
    <Card class="p-4">
      <div class="flex justify-between items-center mb-4">
        <div class="flex items-center gap-2">
          <input
            type="checkbox"
            :checked="isAllSelected"
            class="cursor-pointer"
            @change="toggleSelectAll"
          />
          <span>Select All</span>
        </div>
        <Button variant="default" @click="publishSelected" :disabled="selectedIds.length === 0">
          Publish Selected ({{ selectedIds.length }})
        </Button>
      </div>

      <div class="max-h-[480px] overflow-auto">
        <Table>
          <TableHeader class="bg-gray-100 text-gray-700 sticky top-0 z-10">
            <TableRow>
              <TableHead></TableHead>
              <TableHead>Title</TableHead>
              <TableHead>Submission Datetime</TableHead>
              <TableHead>Submission Deadline</TableHead>
              <TableHead>Status</TableHead>
              <TableHead>Category</TableHead>
              <TableHead>Submission By</TableHead>
              <TableHead>Actions</TableHead>
            </TableRow>
          </TableHeader>

          <TableBody>
            <template v-if="coordinatorStore.isLoading">
              <TableRow v-for="n in 5" :key="n">
                <TableCell colspan="8">
                  <div class="animate-pulse h-8 bg-gray-200 rounded w-full"></div>
                </TableCell>
              </TableRow>
            </template>

            <template v-else-if="!coordinatorStore.approvedArticles.articles.length">
              <TableRow>
                <TableCell colspan="8" class="text-center text-gray-500 py-6">
                  No articles to display.
                </TableCell>
              </TableRow>
            </template>

            <TableRow
              v-else
              v-for="article in coordinatorStore.approvedArticles.articles"
              :key="article.article_id"
            >
              <TableCell>
                <input
                  type="checkbox"
                  :checked="selectedIds.includes(article.article_id)"
                  @change="toggleSelectRow(article.article_id)"
                  class="cursor-pointer"
                />
              </TableCell>
              <TableCell>{{ article.article_title }}</TableCell>
              <TableCell>{{ article.submission_date }}</TableCell>
              <TableCell>{{ article.submission_deadline }}</TableCell>
              <TableCell>
                <StatusIndicator :status="article.status" />
              </TableCell>
              <TableCell>{{ article.article_type_name }}</TableCell>
              <TableCell>{{ article.user_name }}</TableCell>
              <TableCell>
                <TooltipWrapper text="View the article">
                  <Button
                    variant="outline"
                    size="sm"
                    @click="goToArticleDetails(article.article_id || '')"
                  >
                    View
                  </Button>
                </TooltipWrapper>
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>
    </Card>
  </Layout>
</template>
