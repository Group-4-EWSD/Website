<script setup lang="ts">
import { ArchiveX, CircleCheckBig, FilePenLine, FileText, SlidersHorizontal } from 'lucide-vue-next'

import ArticleTable from '@/components/pagespecific/coordinator-articles/ArticleTable.vue'
import {
  Select,
  SelectItem,
  SelectValue,
  SelectTrigger,
  SelectContent,
} from '@/components/ui/select'
import Card from '@/components/ui/card/Card.vue'
import Layout from '@/components/ui/Layout.vue'
import Skeleton from '@/components/ui/skeleton/Skeleton.vue'
import { computed, onMounted, ref } from 'vue'
import { useCoordinatorStore } from '@/stores/coordinator'
import TooltipWrapper from '@/components/shared/TooltipWrapper.vue'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'

const coordinatorStore = useCoordinatorStore()

const selectedStatusFilter = ref('all')
const selectedFeedbackFilter = ref('all')

const filteredArticles = computed(() => {
  if (selectedStatusFilter.value === 'all') return coordinatorStore.magazineArticles.articles
  return coordinatorStore.magazineArticles.articles.filter((article) => {
    if (selectedStatusFilter.value === 'pending') return article.status === 1
    if (selectedStatusFilter.value === 'approved') return article.status === 2
    if (selectedStatusFilter.value === 'rejected') return article.status === 3
    if (selectedStatusFilter.value === 'published') return article.status === 4
    return true
  })
})

const sortOptions = [
  { label: 'Date Created', value: 'created_at' },
  { label: 'Title A-Z', value: 'title_asc' },
  { label: 'Title Z-A', value: 'title_desc' },
]

const sortBy = (value: string) => {
  console.log('Sort by:', value)
}

onMounted(() => {
  if (!coordinatorStore.magazineArticles.articles.length) {
    coordinatorStore.fetchCoordinatorArticles()
  }
})
</script>

<template>
  <Layout>
    <h2 class="text-xl font-bold mb-4 uppercase">Articles</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <Card class="p-5 flex flex-row gap-5 items-center">
        <FileText class="h-14 w-14 text-blue-500" />
        <div class="flex flex-col gap-3">
          <h2 class="font-semibold">Total Submission</h2>

          <Skeleton v-if="false" class="w-20 h-5" />
          <p v-if="true">{{ coordinatorStore.magazineArticles.totalSubmissions }}</p>
        </div>
      </Card>
      <Card class="p-5 flex flex-row gap-5 items-center">
        <FilePenLine class="h-14 w-14 text-yellow-500" />
        <div class="flex flex-col gap-3">
          <h2 class="font-semibold">Pending Review</h2>

          <Skeleton v-if="false" class="w-20 h-5" />
          <p v-if="true">{{ coordinatorStore.magazineArticles.pendingReview }}</p>
        </div>
      </Card>
      <Card class="p-5 flex flex-row gap-5 items-center">
        <CircleCheckBig class="h-14 w-14 text-green-500" />
        <div class="flex flex-col gap-3">
          <h2 class="font-semibold">Approved Articles</h2>

          <Skeleton v-if="false" class="w-20 h-5" />
          <p v-if="true">{{ coordinatorStore.magazineArticles.approvedArticles }}</p>
        </div>
      </Card>
      <Card class="p-5 flex flex-row gap-5 items-center">
        <ArchiveX class="h-14 w-14 text-red-500" />
        <div class="flex flex-col gap-3">
          <h2 class="font-semibold">Rejected Articles</h2>

          <Skeleton v-if="false" class="w-20 h-5" />
          <p v-if="true">{{ coordinatorStore.magazineArticles.rejectedArticles }}</p>
        </div>
      </Card>
    </div>
    <div class="flex flex-col gap-3">
      <div class="flex justify-between items-center mt-4 mb-2">
        <h2 class="text-xl font-bold uppercase">List of Articles</h2>

        <div class="flex items-center gap-4">
          <!-- Sort Dropdown Menu -->
          <DropdownMenu>
            <TooltipWrapper text="Sort">
              <DropdownMenuTrigger as-child>
                <SlidersHorizontal
                  class="w-5 h-5 cursor-pointer hover:text-black transition-all"
                  aria-label="Sort items"
                />
              </DropdownMenuTrigger>
            </TooltipWrapper>

            <DropdownMenuContent align="end" class="w-48 z-50">
              <DropdownMenuLabel>Sort By</DropdownMenuLabel>
              <DropdownMenuSeparator />
              <DropdownMenuItem
                v-for="option in sortOptions"
                :key="option.value"
                @click="sortBy(option.value)"
              >
                {{ option.label }}
              </DropdownMenuItem>
            </DropdownMenuContent>
          </DropdownMenu>

          <!-- Filter Dropdown -->
          <Select v-model="selectedStatusFilter">
            <SelectTrigger class="w-[160px]">
              <SelectValue placeholder="Filter by status" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="all">All</SelectItem>
              <SelectItem value="pending">Pending</SelectItem>
              <SelectItem value="approved">Approved</SelectItem>
              <SelectItem value="rejected">Rejected</SelectItem>
              <SelectItem value="published">Published</SelectItem>
            </SelectContent>
          </Select>

          <!-- Feedback Filter -->
          <Select v-model="selectedFeedbackFilter">
            <SelectTrigger class="w-[180px]">
              <SelectValue placeholder="Filter by feedback" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="all">All</SelectItem>
              <SelectItem value="given">Feedback Given</SelectItem>
              <SelectItem value="not_given">Not Given</SelectItem>
              <SelectItem value="within_14_days">Given Within 14 Days</SelectItem>
            </SelectContent>
          </Select>
        </div>
      </div>

      <ArticleTable :articles="filteredArticles" :isLoading="coordinatorStore.isLoading" />
    </div>
  </Layout>
</template>
