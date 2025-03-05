<script setup lang="ts">
import { Eye, SlidersHorizontal, ThumbsUp } from 'lucide-vue-next'
import { ref } from 'vue'

import FilterModal from '@/components/pagespecific/student-home/FilterModal.vue'
import { Card } from '@/components/ui/card'
import {
  DropdownMenu,
  DropdownMenuContent,
  DropdownMenuItem,
  DropdownMenuLabel,
  DropdownMenuSeparator,
  DropdownMenuTrigger,
} from '@/components/ui/dropdown-menu'
import Layout from '@/components/ui/Layout.vue'
import { Table, TableBody, TableCell, TableRow } from '@/components/ui/table'

const sortOption = ref<string>('')

const sortOptions = ref([
  { value: 'created asc', label: 'Newest First' },
  { value: 'created desc', label: 'Oldest First' },
  { value: 'title asc', label: 'Name (A → Z)' },
  { value: 'title-desc', label: 'Name (Z → A)' },
])

const sortBy = (option: string) => {
  sortOption.value = option
  console.log('Sorting by:', option)
  // Implement sorting logic in your computed properties or API call
}

const articles = [
  {
    id: 1,
    title: 'ARTICLES 1.1 - The Power of Picture by Zar Li',
    description:
      'Eight months after the Civil War began, in December 1861, Frederick Douglass spoke at Boston’s Tremont...',
    author: 'Zar Li',
    date: '15 Jan 2025',
    avatar: 'https://randomuser.me/api/portraits/women/1.jpg',
  },
  {
    id: 2,
    title: 'ARTICLES 2.2 - Will AI replace the Arts? by Swe Thu Htet',
    description:
      'The rise of artificial intelligence has sparked numerous debates across various fields...',
    author: 'Swe Thu Htet',
    date: '05 Jan 2025',
    avatar: 'https://randomuser.me/api/portraits/men/2.jpg',
  },
  {
    id: 3,
    title: 'ARTICLES 3.1 - Will AI replace the Arts? by Swe Thu Htet',
    description:
      'The rise of artificial intelligence has sparked numerous debates across various fields...',
    author: 'Swe Thu Htet',
    date: '05 Jan 2025',
    avatar: 'https://randomuser.me/api/portraits/men/2.jpg',
  },
  {
    id: 4,
    title: 'ARTICLES 4.1 - Will AI replace the Arts? by Swe Thu Htet',
    description:
      'The rise of artificial intelligence has sparked numerous debates across various fields...',
    author: 'Swe Thu Htet',
    date: '05 Jan 2025',
    avatar: 'https://randomuser.me/api/portraits/men/2.jpg',
  },
  {
    id: 5,
    title: 'ARTICLES 5.1 - Will AI replace the Arts? by Swe Thu Htet',
    description:
      'The rise of artificial intelligence has sparked numerous debates across various fields...',
    author: 'Swe Thu Htet',
    date: '05 Jan 2025',
    avatar: 'https://randomuser.me/api/portraits/men/2.jpg',
  },
  {
    id: 6,
    title: 'ARTICLES 6.1 - Will AI replace the Arts? by Swe Thu Htet',
    description:
      'The rise of artificial intelligence has sparked numerous debates across various fields...',
    author: 'Swe Thu Htet',
    date: '05 Jan 2025',
    avatar: 'https://randomuser.me/api/portraits/men/2.jpg',
  },
  {
    id: 7,
    title: 'ARTICLES 2.2 - Will AI replace the Arts? by Swe Thu Htet',
    description:
      'The rise of artificial intelligence has sparked numerous debates across various fields...',
    author: 'Swe Thu Htet',
    date: '05 Jan 2025',
    avatar: 'https://randomuser.me/api/portraits/men/2.jpg',
  },
]
</script>

<template>
  <Layout>
    <h2 class="text-xl font-bold mb-4 uppercase">Dashboard</h2>

    <div class="flex flex-col gap-3">
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <Card class="p-4">
          <div class="flex items-center justify-between p-0">
            <h2 class="text-lg uppercase">Total Likes</h2>
            <component :is="ThumbsUp" />
          </div>
          <div>
            <p class="text-4xl font-bold text-blue-500 py-2">
              45 <span class="text-primary text-3xl"> Likes</span>
            </p>
            <p class="text-sm text-muted-foreground">Up to 10% from Last Week</p>
          </div>
        </Card>
        <Card class="p-4">
          <div class="flex items-center justify-between">
            <h2 class="text-lg uppercase">Uploaded Articles</h2>
          </div>
          <div>
            <p class="text-4xl font-bold py-2">21</p>
            <p class="text-sm text-muted-foreground">Articles for this year</p>
          </div>
        </Card>
        <Card class="p-4">
          <div class="flex items-center justify-between">
            <h2 class="text-lg uppercase">Total Views</h2>
            <component :is="Eye" />
          </div>
          <div>
            <p class="text-4xl font-bold py-2">35</p>
            <p class="text-sm text-muted-foreground">75% total numbers of views</p>
          </div>
        </Card>
      </div>

      <div class="flex justify-between items-center pb-2 relative">
        <h3 class="font-semibold uppercase">AURORA's magazine articles</h3>
        <div class="flex gap-3 text-gray-600 pr-[10px] relative">
          <!-- Filter -->
          <FilterModal />

          <!-- Sorting -->
          <DropdownMenu>
            <DropdownMenuTrigger as-child>
              <SlidersHorizontal
                class="w-5 h-5 cursor-pointer hover:text-black transition-all"
                aria-label="Sort items"
              />
            </DropdownMenuTrigger>

            <DropdownMenuContent align="end" class="w-48 z-50">
              <DropdownMenuLabel>Sort By</DropdownMenuLabel>
              <DropdownMenuSeparator />
              <DropdownMenuItem
                v-for="option in sortOptions"
                :key="option.value"
                @click="sortBy(option.value)"
              >
                {{ option.label }}
              </DropdownMenuItem>
            </DropdownMenuContent>
          </DropdownMenu>
        </div>
      </div>
      <div class="w-full border rounded-lg shadow-sm bg-white p-4 relative">
        <!-- <div class="flex flex-col gap-3"> -->
        <div class="max-h-[400px] overflow-y-auto">
          <Table class="w-full">
            <TableBody>
              <TableRow
                v-for="article in articles.slice(0, 6)"
                :key="article.id"
                class="border-b hover:bg-gray-50 transition-all"
              >
                <TableCell>
                  <div class="flex items-center gap-4">
                    <img
                      src="@/assets/profile.png"
                      class="w-10 h-10 rounded-full border border-white hidden sm:flex"
                    />
                    <div class="flex-1">
                      <a
                        :href="`/articles/${article.id}`"
                        class="text-blue-600 font-semibold hover:underline py-1"
                      >
                        {{ article.title }}
                      </a>
                      <p class="text-sm text-gray-500 py-1">
                        {{ article.description }}
                      </p>
                    </div>

                    <span class="text-gray-600 text-sm">{{ article.date }}</span>
                  </div>
                </TableCell>
              </TableRow>
            </TableBody>
          </Table>
        </div>
      </div>
    </div>
  </Layout>
</template>
