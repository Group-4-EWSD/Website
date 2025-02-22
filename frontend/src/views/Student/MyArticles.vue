<script setup>
import Layout from '@/components/ui/Layout.vue'
import { Card } from '@/components/ui/card'
import { Button } from '@/components/ui/button'
import { Calendar } from 'lucide-vue-next'
import LatestArticles from '@/components/pagespecific/my-articles/LatestArticles.vue'


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
</script>

<template>
  <Layout>
    <h2 class="text-2xl font-bold mb-4 uppercase">My Articles</h2>

    <div class="flex flex-col gap-5">
      <div class="grid md:grid-cols-3 grid-cols-1 gap-5">
        <Card class="p-5 flex flex-col gap-5">
          <h2 class="font-semibold uppercase">Upload Articles</h2>
          <div class="grid md:grid-cols-2 grid-cols-1 gap-3">
            <Button class="uppercase w-full">Upload</Button>
            <Button variant="secondary" class="uppercase w-full">View draft</Button>
          </div>
        </Card>
        <Card class="p-5 flex flex-row gap-5 items-center">
          <Calendar class="h-14 w-14" />
          <div class="flex flex-col gap-3">
            <h2 class="font-semibold">Pre Upload Deadline</h2>
            <p>{{ preUploadDate }}</p>
          </div>
        </Card>
        <Card class="p-5 flex flex-row gap-5 items-center">
          <Calendar class="h-14 w-14" />
          <div class="flex flex-col gap-3">
            <h2 class="font-semibold">Pre Upload Deadline</h2>
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
      </div>
    </div>
  </Layout>
</template>
