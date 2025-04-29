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

const selectedYear = ref(guestStore.selectedYear || '')
const yearOptions = ref<{ label: string; value: string }[]>([])

onMounted(async () => {
  if (!guestStore.guestArticles.length) {
    guestStore.fetchArticles()
  }

  if (!guestStore.yearOptions.length) {
    const [academicYears] = await Promise.all([getFilterItems(4)])

    yearOptions.value = academicYears.map((item: any) => ({
      label: item.academic_year_description,
      value: item.academic_year_id,
    }))

    guestStore.setYearOptions(yearOptions.value)
  }
})

watch([selectedYear], ([newYear]) => {
  guestStore.setSelectedYear(newYear)

  guestStore.fetchArticles({
    ...(newYear && { academicYearId: newYear }),
  })
})

const resetFilters = () => {
  selectedYear.value = ''
}
</script>

<template>
  <Layout>
    <div class="flex flex-col gap-3">
      <div class="flex justify-between items-center mt-4 mb-2">
        <h2 class="text-xl font-bold uppercase">Articles</h2>

        <div class="flex items-center gap-4">
          <Select v-model="selectedYear">
            <SelectTrigger class="w-[180px]">
              <SelectValue placeholder="Filter by Year" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem
                v-for="option in guestStore.yearOptions"
                :key="option.value"
                :value="option.value"
              >
                {{ option.label }}
              </SelectItem>
            </SelectContent>
          </Select>

          <Button class="btn btn-outline" @click="resetFilters"> Reset Filters </Button>
        </div>
      </div>

      <ArticleTable :articles="guestStore.guestArticles" :isLoading="guestStore.isLoading" />
    </div>
  </Layout>
</template>
