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
import type { CoordinatorArticle } from '@/types/article'

const articles: CoordinatorArticle[] = [
  {
    article_id: '0184fe10-0c13-41ac-9e33-6176329afa7e',
    article_title: 'A Game of Inches',
    submission_date: '21 Mar 2025',
    submission_deadline: '21 Mar 2025',
    status: 1,
    category: 'Art & Science',
    submission_by: 'Zar li',
  },
  {
    article_id: '2',
    article_title: 'A Game of Inches',
    submission_date: '21 Mar 2025',
    submission_deadline: '21 Mar 2025',
    status: 1,
    category: 'Art & Science',
    submission_by: 'Zar li',
  },
  {
    article_id: '3',
    article_title: 'A Game of Inches',
    submission_date: '21 Mar 2025',
    submission_deadline: '21 Mar 2025',
    status: 1,
    category: 'Art & Science',
    submission_by: 'Zar li',
  },
  {
    article_id: '4',
    article_title: 'A Game of Inches',
    submission_date: '21 Mar 2025',
    submission_deadline: '21 Mar 2025',
    status: 1,
    category: 'Art & Science',
    submission_by: 'Zar li',
  },
  {
    article_id: '5',
    article_title: 'A Game of Inches',
    submission_date: '21 Mar 2025',
    submission_deadline: '21 Mar 2025',
    status: 1,
    category: 'Art & Science',
    submission_by: 'Zar li',
  },
  {
    article_id: '6',
    article_title: 'A Game of Inches',
    submission_date: '21 Mar 2025',
    submission_deadline: '21 Mar 2025',
    status: 1,
    category: 'Art & Science',
    submission_by: 'Zar li',
  },
  {
    article_id: '7',
    article_title: 'A Game of Inches',
    submission_date: '21 Mar 2025',
    submission_deadline: '21 Mar 2025',
    status: 1,
    category: 'Art & Science',
    submission_by: 'Zar li',
  },
]

const goToArticleDetails = (articleId: string) => {
  router.push({ name: 'getArticleDetails', params: { id: articleId } })
}
</script>

<template>
  <Card class="p-4">
    <Table>
      <TableHeader>
        <TableRow>
          <TableHead>Title</TableHead>
          <TableHead>Submission Date</TableHead>
          <TableHead>Submission Deadline</TableHead>
          <TableHead>Status</TableHead>
          <TableHead>Category</TableHead>
          <TableHead>Submission By</TableHead>
          <TableHead>Actions</TableHead>
        </TableRow>
      </TableHeader>
      <TableBody>
        <TableRow
          v-for="article in articles"
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
          <TableCell>{{ article.category }}</TableCell>
          <TableCell>{{ article.submission_by }}</TableCell>
          <TableCell>
            <div class="flex gap-2">
              <TooltipWrapper text="Give a feedback">
                <Button variant="outline" size="sm" @click=""> View </Button>
              </TooltipWrapper>
            </div>
          </TableCell>
        </TableRow>
      </TableBody>
    </Table>
  </Card>
</template>
