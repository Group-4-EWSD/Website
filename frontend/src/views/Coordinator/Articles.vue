<script setup lang="ts">
import { ArchiveX, CircleCheckBig, FilePenLine, FileText, SlidersHorizontal } from 'lucide-vue-next'
import { computed, onMounted, ref, watch } from 'vue'

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
import Button from '@/components/ui/button/Button.vue'
import Skeleton from '@/components/ui/skeleton/Skeleton.vue'
import { useCoordinatorStore } from '@/stores/coordinator'
import { getFilterItems } from '@/api/articles'
import TooltipWrapper from '@/components/shared/TooltipWrapper.vue'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import router from '@/router'

const coordinatorStore = useCoordinatorStore()

const selectedStatusFilter = ref('all')
const selectedFeedbackFilter = ref('all')
const currentSort = ref('')
const selectedYear = ref<string>('all')
const yearOptions = ref<{ label: string; value: string }[]>([])

const filteredArticles = computed(() => {
  if (selectedStatusFilter.value === 'all') return coordinatorStore.coordinatorArticles.articles
  return coordinatorStore.coordinatorArticles.articles.filter((article) => {
    if (selectedStatusFilter.value === 'pending') return article.status === 1
    if (selectedStatusFilter.value === 'approved') return article.status === 2
    if (selectedStatusFilter.value === 'rejected') return article.status === 3
    if (selectedStatusFilter.value === 'published') return article.status === 4
    return true
  })
})

const sortOptions = [
  { label: 'Recently Submitted', value: '1' },
  { label: 'Oldest Submission', value: '2' },
  { label: 'Title A-Z', value: '3' },
  { label: 'Title Z-A', value: '4' },
]

const sortBy = (value: string) => {
  currentSort.value = value
}

onMounted(async () => {
  if (!coordinatorStore.coordinatorArticles.articles.length) {
    coordinatorStore.fetchCoordinatorArticles()
  }

  const [academicYears] = await Promise.all([getFilterItems(4)])

  yearOptions.value = academicYears.map((item: any) => ({
    label: item.academic_year_description,
    value: item.academic_year_id,
  }))
})

const updateArticles = () => {
  const params: any = {
    sorting: currentSort.value || undefined,
  }

  if (selectedFeedbackFilter.value !== 'all') {
    params.feedback = Number(selectedFeedbackFilter.value)
  }

  if (selectedYear.value !== 'all') {
    params.academicYearId = selectedYear.value
  }

  coordinatorStore.fetchCoordinatorArticles(params)
}

watch([selectedFeedbackFilter, selectedYear], updateArticles, { immediate: true })

watch(currentSort, updateArticles)

const goToPublishPage = () => {
  router.push({ name: 'Coordinator Publish' })
}
</script>

<template>
  <Layout>
    <h2 class="text-xl font-bold mb-4 uppercase">Articles (2025-2026)</h2>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <Card class="p-5 flex flex-row gap-5 items-center">
        <FileText class="h-14 w-14 text-blue-500" />
        <div class="flex flex-col gap-3">
          <h2 class="font-semibold">Total Submission</h2>

          <Skeleton v-if="false" class="w-20 h-5" />
          <p v-if="true">{{ coordinatorStore.coordinatorArticles.totalSubmissions }}</p>
        </div>
      </Card>
      <Card class="p-5 flex flex-row gap-5 items-center">
        <FilePenLine class="h-14 w-14 text-yellow-500" />
        <div class="flex flex-col gap-3">
          <h2 class="font-semibold">Pending Review</h2>

          <Skeleton v-if="false" class="w-20 h-5" />
          <p v-if="true">{{ coordinatorStore.coordinatorArticles.pendingReview }}</p>
        </div>
      </Card>
      <Card class="p-5 flex flex-row gap-5 items-center">
        <CircleCheckBig class="h-14 w-14 text-green-500" />
        <div class="flex flex-col gap-3">
          <h2 class="font-semibold">Approved Articles</h2>

          <Skeleton v-if="false" class="w-20 h-5" />
          <p v-if="true">{{ coordinatorStore.coordinatorArticles.approvedArticles }}</p>
        </div>
      </Card>
      <Card class="p-5 flex flex-row gap-5 items-center">
        <ArchiveX class="h-14 w-14 text-red-500" />
        <div class="flex flex-col gap-3">
          <h2 class="font-semibold">Rejected Articles</h2>

          <Skeleton v-if="false" class="w-20 h-5" />
          <p v-if="true">{{ coordinatorStore.coordinatorArticles.rejectedArticles }}</p>
        </div>
      </Card>
    </div>
    <div class="flex flex-col gap-3">
      <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between mt-4 mb-2">
        <h2 class="text-xl font-bold uppercase">
          List of Articles <br />
          (TOTAL UP TO 2025–2026)
        </h2>

        <!-- Controls -->
        <div class="flex flex-wrap items-center gap-2 sm:gap-4">
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

          <!-- Status Filter -->
          <Select v-model="selectedStatusFilter">
            <SelectTrigger class="w-[160px]">
              <SelectValue placeholder="Filter by status" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="all">All Status</SelectItem>
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
              <SelectItem value="all">All Feedback</SelectItem>
              <SelectItem value="1">Given Within 14 days</SelectItem>
              <SelectItem value="2">Given After 14 days</SelectItem>
              <SelectItem value="0">Not Given</SelectItem>
            </SelectContent>
          </Select>

          <!-- Year Filter -->
          <Select v-model="selectedYear">
            <SelectTrigger class="w-[160px]">
              <SelectValue placeholder="Filter by year" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="all">All Years</SelectItem>
              <SelectItem v-for="option in yearOptions" :key="option.value" :value="option.value">
                {{ option.label }}
              </SelectItem>
            </SelectContent>
          </Select>

          <!-- Publish Action -->
          <Button @click="goToPublishPage">Publish Articles</Button>
        </div>
      </div>

      <ArticleTable :articles="filteredArticles" :isLoading="coordinatorStore.isLoading" />
    </div>
  </Layout>
</template>
