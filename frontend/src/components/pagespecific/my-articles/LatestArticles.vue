<script setup lang="ts">
import { PencilIcon } from 'lucide-vue-next'

import { Card } from '@/components/ui/card'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import { useMyArticlesStore } from '@/stores/my-articles'
import Skeleton from '@/components/ui/skeleton/Skeleton.vue'

const myArticlesStore = useMyArticlesStore()

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
          <TableHead> Tile </TableHead>
          <TableHead> Submission Date </TableHead>
          <TableHead> Status </TableHead>
          <TableHead> Category </TableHead>
          <TableHead> Comments </TableHead>
          <TableHead> Actions </TableHead>
        </TableRow>
      </TableHeader>
      <TableBody>
        <TableRow v-for="article in myArticlesStore.latestArticles" :key="article.article_id">
          <TableCell> {{ article.article_title }} </TableCell>
          <TableCell> {{ article.created_at }} </TableCell>
          <TableCell> {{ myArticlesStore.statusText(article.status) }} </TableCell>
          <TableCell> - </TableCell>
          <TableCell> - </TableCell>
          <TableCell>
            <div class="flex gap-2">
              <PencilIcon class="h-4 w-4" />
            </div>
          </TableCell>
        </TableRow>
      </TableBody>
    </Table>

  </Card>
</template>
