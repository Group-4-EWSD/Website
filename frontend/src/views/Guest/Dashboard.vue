<script setup lang="ts">
import { computed, onMounted, ref } from 'vue'

import ArticleChart from '@/components/shared/ArticleChart.vue'
import MagazineArticles from '@/components/shared/MagazineArticles.vue'
import Layout from '@/components/ui/Layout.vue'
import { useGuestStore } from '@/stores/guest'

const guestStore = useGuestStore()
const selectedYear = ref<string>('2025')

const selectYear = (year: string) => {
  selectedYear.value = year
}

onMounted(() => {
  if (!guestStore.articles.length) {
    guestStore.fetchDashboardData()
  }
  console.log(guestStore.articles.length)
})

const articles = computed(() => {
  return guestStore.articles.filter((article) => {
    const year = new Date(article.submission_deadline).getFullYear().toString()
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
        <!-- <p class="text-sm text-gray-500">Previous Login {{ guestStore.prevLogin }}</p> -->
        <div class="text-sm text-gray-500">
          <template v-if="guestStore.isLoading">
            <span class="animate-pulse text-gray-400">Loading...</span>
          </template>
          <template v-else> Previous Login {{ guestStore.prevLogin || '-' }} </template>
        </div>

        <div class="h-100">
          <ArticleChart :data="guestStore.chartData" />
        </div>

        <div>
          <h3 class="text-lg font-medium mb-3">View Reports</h3>

          <div v-if="guestStore.isLoading" class="flex flex-wrap gap-4">
            <div v-for="n in 4" :key="n" class="flex flex-col items-center animate-pulse">
              <div class="w-24 h-32 bg-gray-300 rounded shadow"></div>
              <div class="mt-2 w-16 h-4 bg-gray-300 rounded"></div>
            </div>
          </div>

          <div v-else class="flex flex-wrap gap-4">
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
              >
                Year {{ year.academic_year_start }}
              </a>
            </div>
          </div>
        </div>

        <div>
          <h3 class="text-lg font-medium mb-3">View Articles By Faculty</h3>

          <div v-if="guestStore.isLoading" class="grid grid-cols-2 sm:grid-cols-3 gap-4">
            <div
              v-for="n in 6"
              :key="n"
              class="border rounded-xl px-4 py-3 text-center shadow-sm animate-pulse"
            >
              <div class="w-20 h-4 bg-gray-300 rounded mx-auto mb-2"></div>
              <div class="w-16 h-3 bg-gray-300 rounded mx-auto"></div>
            </div>
          </div>

          <div v-else class="grid grid-cols-2 sm:grid-cols-3 gap-4">
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
