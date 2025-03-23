<script setup lang="ts">
import { PencilIcon } from 'lucide-vue-next'
import { ref } from 'vue'

import { ArticleStatus } from '@/api/articles'
import StatusIndicator from '@/components/shared/StatusIndicator.vue'
import TooltipWrapper from '@/components/shared/TooltipWrapper.vue'
import { Button } from '@/components/ui/button'
import { Card } from '@/components/ui/card'
import Skeleton from '@/components/ui/skeleton/Skeleton.vue'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import { useMyArticlesStore } from '@/stores/my-articles'

import UploadArticle from './UploadArticle.vue'




const myArticlesStore = useMyArticlesStore()
const showDialog = ref(false)
const selectedArticleId = ref<string>('')

// Function to handle edit button click
const handleEditClick = (id: string) => {
  selectedArticleId.value = id
  showDialog.value = true
}
</script>

<template>
  <Card class="p-4">
    <Skeleton v-if="myArticlesStore.isLoading && !myArticlesStore.hasLoaded" class="w-full h-14" />
    <div
      v-else-if="myArticlesStore.hasLoaded && myArticlesStore.latestArticles.length === 0"
      class="flex items-center justify-center h-12"
    >
      <p>No articles found</p>
    </div>
    <Table v-if="myArticlesStore.latestArticles.length > 0">
      <TableHeader>
        <TableRow>
          <TableHead>Title</TableHead>
          <TableHead>Submission Date</TableHead>
          <TableHead>Status</TableHead>
          <TableHead>Category</TableHead>
          <TableHead>Coordinator Feedback</TableHead>
          <TableHead>Actions</TableHead>
        </TableRow>
      </TableHeader>
      <TableBody>
        <TableRow v-for="article in myArticlesStore.latestArticles" :key="article.article_id">
          <TableCell>{{ article.article_title }}</TableCell>
          <TableCell>{{ article.created_at }}</TableCell>
          <TableCell>
            <StatusIndicator :status="article.status" />
          </TableCell>
          <TableCell>{{ article.article_type_name }}</TableCell>
          <TableCell>{{ article.last_feedback }}</TableCell>
          <TableCell>
            <div class="flex gap-2">
              <TooltipWrapper text="Edit">
                <Button variant="ghost" size="sm" @click="handleEditClick(article.article_id)" :disabled="![ArticleStatus.DRAFT, ArticleStatus.PENDING].includes(article.status)">
                  <PencilIcon class="h-4 w-4" />
                </Button>
              </TooltipWrapper>
            </div>
          </TableCell>
        </TableRow>
      </TableBody>
    </Table>
  </Card>

  <UploadArticle v-if="selectedArticleId" :article_id="selectedArticleId" v-model="showDialog">
    <template #trigger>
      <span></span>
    </template>
  </UploadArticle>
</template>
