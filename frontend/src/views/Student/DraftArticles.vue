<script setup lang="ts">
import { PencilIcon, TrashIcon } from 'lucide-vue-next'

import TooltipWrapper from '@/components/shared/TooltipWrapper.vue'
import { Card } from '@/components/ui/card'
import Layout from '@/components/ui/Layout.vue'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'

const sampleData = [
  {
    title: 'Article 1',
    description: 'Description 1',
    category: 'Category 1',
    lastEditedDate: '12/12/2025',
  },
  {
    title: 'Article 2',
    description: 'Description 2',
    category: 'Category 2',
    lastEditedDate: '12/12/2025',
  },
  {
    title: 'Article 3',
    description: 'Description 3',
    category: 'Category 3',
    lastEditedDate: '12/12/2025',
  },
]
</script>

<template>
  <Layout>
    <h2 class="text-xl font-bold mb-4 uppercase">Draft Articles</h2>

    <Card class="p-4">
      <Table>
        <TableHeader>
          <TableRow>
            <TableHead> Tile </TableHead>
            <TableHead> Description </TableHead>
            <TableHead> Category </TableHead>
            <TableHead> Last Edited Date </TableHead>
            <TableHead> Actions </TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <TableRow v-for="article in sampleData" :key="article.title">
            <TableCell> {{ article.title }} </TableCell>
            <TableCell> {{ article.description }} </TableCell>
            <TableCell> {{ article.category }} </TableCell>
            <TableCell> {{ article.lastEditedDate }} </TableCell>
            <TableCell>
              <div class="flex gap-3">
                <TooltipWrapper text="Edit">
                  <PencilIcon class="h-4 w-4 cursor-pointer" />
                </TooltipWrapper>
                <TooltipWrapper text="Delete">
                  <TrashIcon class="h-4 w-4 text-destructive cursor-pointer" />
                </TooltipWrapper>
              </div>
            </TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </Card>
  </Layout>
</template>
