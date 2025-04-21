<script setup lang="ts">
import { onMounted, ref, watch } from 'vue'

import { getFilterItems } from '@/api/articles'
import ArticleTable from '@/components/pagespecific/coordinator-articles/ArticleTable.vue'
import Button from '@/components/ui/button/Button.vue'
import Layout from '@/components/ui/Layout.vue'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import { useGuestStore } from '@/stores/guest'



const guestStore = useGuestStore()

const selectedCategory = ref<string | null>(null)
const selectedYear = ref<string | null>(null)
const categoryOptions = ref<{ label: string; value: string }[]>([])
const yearOptions = ref<{ label: string; value: string }[]>([])

onMounted(async () => {
  const [articleTypes, academicYears] = await Promise.all([getFilterItems(1), getFilterItems(4)])

  categoryOptions.value = articleTypes.map((item: any) => ({
    label: item.article_type_name,
    value: item.article_type_id,
  }))

  yearOptions.value = academicYears.map((item: any) => ({
    label: item.academic_year_description,
    value: item.academic_year_id,
  }))

  if (!guestStore.articles.length) {
    guestStore.fetchDashboardData()
  }
})

watch([selectedCategory, selectedYear], ([newCategory, newYear]) => {
  console.log(newCategory, newYear)
  guestStore.fetchDashboardData({
    ...(newCategory && { facultyId: newCategory }),
    ...(newYear && { academicYearId: newYear }),
  })
})

const resetFilters = () => {
  selectedCategory.value = ''
  selectedYear.value = ''
}
</script>

<template>
  <Layout>
    <div class="flex flex-col gap-3">
      <div class="flex justify-between items-center mt-4 mb-2">
        <h2 class="text-xl font-bold uppercase">Articles</h2>

        <div class="flex items-center gap-4">
          <!-- Feedback Filter -->
          <Select v-model="selectedCategory">
            <SelectTrigger class="w-[180px]">
              <SelectValue placeholder="Filter by Faculty" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem
                v-for="option in categoryOptions"
                :key="option.value"
                :value="option.value"
              >
                {{ option.label }}
              </SelectItem>
            </SelectContent>
          </Select>

          <Select v-model="selectedYear">
            <SelectTrigger class="w-[180px]">
              <SelectValue placeholder="Filter by Year" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem v-for="option in yearOptions" :key="option.value" :value="option.value">
                {{ option.label }}
              </SelectItem>
            </SelectContent>
          </Select>

          <Button class="btn btn-outline" @click="resetFilters"> Reset Filters </Button>
        </div>
      </div>

      <ArticleTable :articles="guestStore.articles" :isLoading="guestStore.isLoading" />
    </div>
  </Layout>
</template>
