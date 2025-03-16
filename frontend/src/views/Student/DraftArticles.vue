<script setup lang="ts">
import { PencilIcon, TrashIcon } from 'lucide-vue-next'
import { onMounted, ref } from 'vue'
import { toast } from 'vue-sonner'

import { getDraftArticles } from '@/api/articles'
import UploadArticle from '@/components/pagespecific/my-articles/UploadArticle.vue'
import TooltipWrapper from '@/components/shared/TooltipWrapper.vue'
import { Card } from '@/components/ui/card'
import Layout from '@/components/ui/Layout.vue'
import Skeleton from '@/components/ui/skeleton/Skeleton.vue'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import type { DraftArticle } from '@/types/article'

const isLoading = ref(false)
const articles = ref<DraftArticle[]>([])

const fetchDraftArticles = async () => {
  isLoading.value = true
  try {
    articles.value = await getDraftArticles()
  } catch (error) {
    console.error(error)
    toast.error('Failed to fetch draft articles')
  } finally {
    isLoading.value = false
  }
}

onMounted(() => {
  fetchDraftArticles()
})
</script>

<template>
  <Layout>
    <h2 class="text-xl font-bold mb-4 uppercase">Draft Articles</h2>

    <Card class="p-4">
      <div class="flex flex-col gap-4" v-if="isLoading">
        <Skeleton class="h-10 space-y-2" />
        <Skeleton class="h-10 space-y-2" />
        <Skeleton class="h-10 space-y-2" />
      </div>

      <div class="flex flex-col justify-center items-center gap-4 h-40" v-if="!isLoading && !articles.length">
        <p>No draft articles found. Start by uploading a new article.</p>
        <div>
          <UploadArticle />
        </div>
      </div>

      <Table v-else-if="articles.length && !isLoading">
        <TableHeader>
          <TableRow>
            <TableHead> Tile </TableHead>
            <TableHead> Description </TableHead>
            <TableHead> Category </TableHead>
            <TableHead> Last Edited Date </TableHead>
            <TableHead> Actions </TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow v-for="article in articles" :key="article.article_title">
            <TableCell> {{ article.article_title }} </TableCell>
            <TableCell> {{ article.article_description }} </TableCell>
            <!-- <TableCell> {{ article.category }} </TableCell>
            <TableCell> {{ article.lastEditedDate }} </TableCell> -->
            <TableCell> </TableCell>
            <TableCell> </TableCell>
            <TableCell>
              <div class="flex gap-3">
                <TooltipWrapper text="Edit">
                  <PencilIcon class="h-4 w-4 cursor-pointer" />
                </TooltipWrapper>
                <TooltipWrapper text="Delete">
                  <TrashIcon class="h-4 w-4 text-destructive cursor-pointer" />
                </TooltipWrapper>
              </div>
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </Card>
  </Layout>
</template>
