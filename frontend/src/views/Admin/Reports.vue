<script setup lang="ts">
import { onMounted, ref } from 'vue'

import { getArticles } from '@/api/articles'
import ActiveUsersReport from '@/components/pagespecific/admin-reports/ActiveUsersReport.vue'
import BrowsersReport from '@/components/pagespecific/admin-reports/BrowsersReport.vue'
import PageViewsReport from '@/components/pagespecific/admin-reports/PageViewsReport.vue'
import Layout from '@/components/ui/Layout.vue'

interface ResponseData {
  mostViewedPages: MostViewedPage[];
  activeUserList: User[];
  browserList: Browser[];
}

// Most Viewed Pages
interface MostViewedPage {
  page_id: string;
  app_page_name: string;
  view_count: number;
}

// User
interface User {
  id: string;
  user_name: string;
  nickname: string;
  user_email: string;
  gender: number;
  user_type_id: string;
  user_type_name: string;
  faculty_id: string;
  faculty_name: string;
  phone_number: string;
  user_photo_path: string | null;
  date_of_birth: string | null;
  article_count: number;
  action_count: number;
  comment_count: number;
  total_score: number;
}

// Browser
interface Browser {
  browser_id: number;
  browser_name: string;
  login_count: number;
}

// Enums for fixed values

// User Type enum (based on user_type_id values)
enum UserType {
  Guest = "0",
  Student = "1",
  MarketingCoordinator = "2",
  MarketingManager = "3",
  Admin = "4"
}

// Gender enum
enum Gender {
  Male = 1,
  Female = 2
  // Other values if they exist in your data
}

// Faculty type (optional, for reference when looking up by ID)
interface Faculty {
  id: string;
  name: string;
}

const data = ref<ResponseData>({
  mostViewedPages: [],
  activeUserList: [],
  browserList: []
})

const isLoading = ref<boolean>(false)

onMounted(async () => {
  isLoading.value = true
  data.value = await getArticles({}) as unknown as ResponseData
  isLoading.value = false
  console.log('Articles:', data)
})

</script>

<template>
  <Layout>
    <div class="space-y-6">
      <header class="mb-8 flex flex-col gap-2">
        <h1 class="text-2xl font-bold tracking-tight">Analytics Dashboard</h1>
        <p class="text-muted-foreground">Track the website performance and user engagement</p>
      </header>

      <div class="grid gap-6 lg:grid-cols-2">
        <PageViewsReport :data="data.mostViewedPages" :isLoading="isLoading"/>
        <ActiveUsersReport :data="data.activeUserList.splice(0, 8)" :isLoading="isLoading"/>
      </div>

      <BrowsersReport :data="data.browserList" :isLoading="isLoading"/>
      
    </div>
  </Layout>
</template>
