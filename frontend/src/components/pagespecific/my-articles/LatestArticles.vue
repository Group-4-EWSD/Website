<script setup lang="ts">
import { PencilIcon } from 'lucide-vue-next'
import { Card } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
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
import StatusIndicator from '@/components/shared/StatusIndicator.vue'
import UploadArticle from './UploadArticle.vue'
import { ref } from 'vue'

const myArticlesStore = useMyArticlesStore()
const showDialog = ref(false)
const selectedArticle = ref(null)

// Function to handle edit button click
const handleEditClick = (article) => {
  selectedArticle.value = article
  showDialog.value = true
}
</script>

<template>
  <Card class="p-4">
    <Skeleton v-if="myArticlesStore.isLoading && !myArticlesStore.hasLoaded" class="w-full h-14" />
    <div v-else-if="myArticlesStore.hasLoaded && myArticlesStore.latestArticles.length === 0" class="flex items-center justify-center h-12">
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
              <Button variant="ghost" size="sm" @click="handleEditClick(article)">
                <PencilIcon class="h-4 w-4" />
              </Button>
            </div>
          </TableCell>
        </TableRow>
      </TableBody>
    </Table>
  </Card>

  <!-- Single UploadArticle component outside the table -->
  <UploadArticle 
    :article="selectedArticle" 
    v-model="showDialog"
  >
    <template #trigger>
      <span></span>
    </template>
  </UploadArticle>
</template>