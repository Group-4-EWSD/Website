<script setup lang="ts">
import Layout from '@/components/ui/Layout.vue'
import ArticleChart from '@/components/shared/ArticleChart.vue'
import MagazineArticles from '@/components/shared/MagazineArticles.vue'
import { useGuestStore } from '@/stores/guest'
import { computed, onMounted, ref } from 'vue'

const guestStore = useGuestStore()
const selectedYear = ref<string>('2025')

const selectYear = (year: string) => {
  selectedYear.value = year
}

onMounted(() => {
  if (!guestStore.articles.length) {
    guestStore.fetchDashboardData()
  }
})

const articles = computed(() => {
  return guestStore.articles.filter((article) => {
    const year = new Date(article.final_submission_deadline).getFullYear().toString()
    return year === selectedYear.value
  })
})

const sortedPublishedYears = computed(() => {
  const targetYear = '2025'
  const years = [...guestStore.publishedYear]

  years.sort((a, b) => {
    if (a.academic_year_start === targetYear) return -1
    if (b.academic_year_start === targetYear) return 1
    return 0
  })

  return years
})
</script>

<template>
  <Layout>
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 p-6">
      <div class="space-y-2">
        <h2 class="text-xl font-semibold">Welcome to AURORA’s Dashboard</h2>
        <p class="text-sm text-gray-500">Previous Login {{ guestStore.prevLogin }}</p>

        <div class="h-100">
          <ArticleChart :data="guestStore.chartData" />
        </div>

        <div>
          <h3 class="text-lg font-medium mb-3">View Reports</h3>
          <div class="flex flex-wrap gap-4">
            <div
              v-for="year in sortedPublishedYears"
              :key="year.academic_year_start"
              class="flex flex-col items-center"
            >
              <img
                src="@/assets/Magazine.png"
                alt="Campus Report"
                class="w-24 h-32 rounded shadow cursor-pointer"
                @click="selectYear(year.academic_year_start)"
              />
              <a
                href="#"
                class="mt-2 text-blue-600 text-sm hover:underline"
                @click.prevent="selectYear(year.academic_year_start)"
                >Year {{ year.academic_year_start }}</a
              >
            </div>
          </div>
        </div>

        <div>
          <h3 class="text-lg font-medium mb-3">View Articles By Faculty</h3>
          <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
            <div
              v-for="faculty in guestStore.facultyList"
              :key="faculty.faculty_id"
              class="border rounded-xl px-4 py-3 text-center shadow-sm cursor-pointer"
            >
              <p class="text-blue-600 font-medium">{{ faculty.faculty_name }}</p>
              <p class="text-sm text-gray-600">{{ faculty.articleCount }} Articles</p>
            </div>
          </div>
        </div>
      </div>

      <MagazineArticles
        :articles="articles"
        :isLoading="guestStore.isLoading"
        :year="selectedYear"
      />
    </div>
  </Layout>
</template>
