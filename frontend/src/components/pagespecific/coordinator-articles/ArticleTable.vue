<script setup lang="ts">
import StatusIndicator from '@/components/shared/StatusIndicator.vue'
import TooltipWrapper from '@/components/shared/TooltipWrapper.vue'
import Button from '@/components/ui/button/Button.vue'
import Card from '@/components/ui/card/Card.vue'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import router from '@/router'
import type { CoordinatorArticle } from '@/types/coordinator'

interface Column {
  key: string
  label: string
}

const columns: Column[] = [
  { key: 'title', label: 'Title' },
  { key: 'submission_date', label: 'Submission Datetime' },
  { key: 'submission_deadline', label: 'Submission Deadline' },
  { key: 'status', label: 'Status' },
  { key: 'category', label: 'Category' },
  { key: 'submission_by', label: 'Submission By' },
]

const props = defineProps<{
  articles: CoordinatorArticle[]
  isLoading: boolean
}>()

const goToArticleDetails = (articleId: string) => {
  router.push({ name: 'getArticleDetails', params: { id: articleId } })
}
</script>

<template>
  <Card class="p-4">
    <div class="max-h-[480px] overflow-auto">
      <Table>
        <TableHeader class="bg-gray-100 text-gray-700 sticky top-0">
          <TableRow>
            <TableHead v-for="column in columns">
              {{ column.label }}
            </TableHead>
            <TableHead>Actions</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow
            v-for="article in props.articles"
            :key="article.article_id"
            class="cursor-pointer"
            @click="goToArticleDetails(article.article_id || '')"
          >
            <TableCell>{{ article.article_title }}</TableCell>
            <TableCell>{{ article.submission_date }}</TableCell>
            <TableCell>{{ article.submission_deadline }}</TableCell>
            <TableCell>
              <StatusIndicator :status="article.status" />
            </TableCell>
            <TableCell>{{ article.article_type_name }}</TableCell>
            <TableCell>{{ article.user_name }}</TableCell>
            <TableCell>
              <div class="flex gap-2">
                <TooltipWrapper text="View the article">
                  <Button variant="outline" size="sm" @click=""> View </Button>
                </TooltipWrapper>
              </div>
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </div>
  </Card>
</template>
