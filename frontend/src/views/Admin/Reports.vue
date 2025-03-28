<script setup lang="ts">
import ActiveUsersReport from '@/components/pagespecific/admin-reports/ActiveUsersReport.vue'
import BrowsersReport from '@/components/pagespecific/admin-reports/BrowsersReport.vue'
import PageViewsReport from '@/components/pagespecific/admin-reports/PageViewsReport.vue'
import Layout from '@/components/ui/Layout.vue'
</script>

<template>
  <Layout>
    <div class="space-y-6">
      <header class="mb-8 flex flex-col gap-2">
        <h1 class="text-2xl font-bold tracking-tight">Analytics Dashboard</h1>
        <p class="text-muted-foreground">Track the website performance and user engagement</p>
      </header>

      <div class="grid gap-6 lg:grid-cols-2">
        <PageViewsReport />
        <ActiveUsersReport />
      </div>

      
      <BrowsersReport />
      
    </div>
  </Layout>
</template>
