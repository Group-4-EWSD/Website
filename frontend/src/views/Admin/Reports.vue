<script setup lang="ts">
import Layout from '@/components/ui/Layout.vue'
import PageViewsReport from '@/components/pagespecific/admin-reports/PageViewsReport.vue'
import ActiveUsersReport from '@/components/pagespecific/admin-reports/ActiveUsersReport.vue'
import BrowsersReport from '@/components/pagespecific/admin-reports/BrowsersReport.vue'
</script>

<template>
  <Layout>
    <div class="p-6 space-y-6">
      <header class="mb-8">
        <h1 class="text-3xl font-bold tracking-tight">Analytics Dashboard</h1>
        <p class="text-muted-foreground">Track your website performance and user engagement</p>
      </header>

      <div class="grid gap-6 md:grid-cols-2 lg:grid-cols-3">
        <div class="bg-card rounded-lg shadow p-4 border">
          <div class="text-2xl font-bold">52,432</div>
          <div class="text-sm text-muted-foreground">Total Page Views</div>
          <div class="text-xs text-green-500 mt-1">↑ 12.5% from last week</div>
        </div>

        <div class="bg-card rounded-lg shadow p-4 border">
          <div class="text-2xl font-bold">8,721</div>
          <div class="text-sm text-muted-foreground">Unique Visitors</div>
          <div class="text-xs text-green-500 mt-1">↑ 5.3% from last week</div>
        </div>

        <div class="bg-card rounded-lg shadow p-4 border">
          <div class="text-2xl font-bold">3m 42s</div>
          <div class="text-sm text-muted-foreground">Avg. Session Duration</div>
          <div class="text-xs text-red-500 mt-1">↓ 1.2% from last week</div>
        </div>
      </div>

      <div class="grid gap-6 lg:grid-cols-2">
        <PageViewsReport />
        <ActiveUsersReport />
      </div>

      <div class="grid gap-6 lg:grid-cols-3">
        <BrowsersReport class="lg:col-span-2" />
        <div class="bg-card rounded-lg shadow p-4 border">
          <h3 class="text-lg font-medium mb-4">Quick Analytics</h3>
          <div class="space-y-4">
            <div>
              <div class="text-sm text-muted-foreground">Bounce Rate</div>
              <div class="text-xl font-semibold">32.4%</div>
              <div class="text-xs text-green-500">↓ 3.2% (better)</div>
            </div>
            <div>
              <div class="text-sm text-muted-foreground">Pages per Session</div>
              <div class="text-xl font-semibold">4.3</div>
              <div class="text-xs text-green-500">↑ 0.5</div>
            </div>
            <div>
              <div class="text-sm text-muted-foreground">New Users</div>
              <div class="text-xl font-semibold">24.6%</div>
              <div class="text-xs text-red-500">↓ 1.8%</div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Layout>
</template>
