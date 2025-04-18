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
import { useRouter } from 'vue-router'
import type { CoordinatorArticle } from '@/types/coordinator'
import type { GuestArticle } from '@/types/guest'

const router = useRouter()

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
type Article = CoordinatorArticle | GuestArticle

const props = defineProps<{
  articles: Article[]
  isLoading: boolean
}>()

const goToArticleDetails = (articleId: string) => {
  if (!articleId) return
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
          <template v-if="!isLoading">
            <template v-if="props.articles.length">
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
                      <Button variant="outline" size="sm" @click.stop> View </Button>
                    </TooltipWrapper>
                  </div>
                </TableCell>
              </TableRow>
            </template>
            <template v-else>
              <TableRow>
                <TableCell colspan="7" class="text-center text-gray-500 py-6">
                  No articles match your filter.
                </TableCell>
              </TableRow>
            </template>
          </template>

          <!-- Loading skeleton rows -->
          <template v-else>
            <TableRow v-for="n in 6" :key="n">
              <TableCell colspan="7" class="py-4">
                <div class="h-4 w-full bg-gray-200 animate-pulse rounded"></div>
              </TableCell>
            </TableRow>
          </template>
        </TableBody>
      </Table>
    </div>
  </Card>
</template>
