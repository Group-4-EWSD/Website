<script setup lang="ts">
import { CalendarDays } from 'lucide-vue-next'

import ArticlePost from '@/components/pagespecific/my-articles/ArticlePost.vue'
import LatestArticles from '@/components/pagespecific/my-articles/LatestArticles.vue'
import UploadArticle from '@/components/pagespecific/my-articles/UploadArticle.vue'
import { Button } from '@/components/ui/button'
import { Card } from '@/components/ui/card'
import Layout from '@/components/ui/Layout.vue'

const preUploadDate = new Intl.DateTimeFormat('en', {
  day: 'numeric',
  month: 'short',
  year: 'numeric',
}).format(new Date())

const actualDeadlineDate = new Intl.DateTimeFormat('en', {
  day: 'numeric',
  month: 'short',
  year: 'numeric',
}).format(new Date())

const sampleArticles = [
  {
    title: 'Article 1',
    description:
      'loren ipsum dolor sit amet consectetur adipisicing elit sed do eiusmod tempor incididunt ut labore et dolore magna aliqua',
    category: 'Category 1',
    totalLikes: 10,
    totalViews: 100,
    date: '12/12/2025',
    status: 'Approved',
  },
  {
    title: 'Article 2',
    description:
      'loren ipsum dolor sit amet consectetur adipisicing elit sed do eiusmod tempor incididunt ut labore et dolore magna aliqua',
    category: 'Category 2',
    totalLikes: 20,
    totalViews: 200,
    date: '12/12/2025',
    status: 'Pending',
  },
  {
    title: 'Article 3',
    description:
      'loren ipsum dolor sit amet consectetur adipisicing elit sed do eiusmod tempor incididunt ut labore et dolore magna aliqua',
    category: 'Category 3',
    totalLikes: 30,
    totalViews: 300,
    date: '12/12/2025',
    status: 'Rejected',
  },
]
</script>

<template>
  <Layout>
    <h2 class="text-xl font-bold mb-4 uppercase">My Articles</h2>

    <div class="flex flex-col gap-5">
      <div class="grid md:grid-cols-3 grid-cols-1 gap-5">
        <Card class="p-5 flex flex-col gap-5">
          <h2 class="font-semibold uppercase">Upload Articles</h2>
          <div class="grid md:grid-cols-2 grid-cols-1 gap-3">
            <UploadArticle />
            <RouterLink to="/student/my-articles/draft">
              <Button variant="secondary" class="uppercase w-full">View draft</Button>
            </RouterLink>
          </div>
        </Card>
        <Card class="p-5 flex flex-row gap-5 items-center">
          <CalendarDays class="h-14 w-14 text-yellow-500" />
          <div class="flex flex-col gap-3">
            <h2 class="font-semibold">Pre Upload Deadline</h2>
            <p>{{ preUploadDate }}</p>
          </div>
        </Card>
        <Card class="p-5 flex flex-row gap-5 items-center">
          <CalendarDays class="h-14 w-14 text-destructive" />
          <div class="flex flex-col gap-3">
            <h2 class="font-semibold">Actual Deadline</h2>
            <p>{{ actualDeadlineDate }}</p>
          </div>
        </Card>
      </div>

      <div class="flex flex-col gap-3">
        <h3 class="font-semibold uppercase">Latest articles</h3>
        <LatestArticles />
      </div>

      <div class="flex flex-col gap-3">
        <h3 class="font-semibold uppercase">My Articles</h3>
        <ArticlePost v-for="article in sampleArticles" :key="article.title" :article="article" />
      </div>
    </div>
  </Layout>
</template>
