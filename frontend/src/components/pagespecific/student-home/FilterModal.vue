<script setup lang="ts">
import { FilterX } from 'lucide-vue-next'
import { ref } from 'vue'

import Select from '@/components/shared/Select.vue'
import TooltipWrapper from '@/components/shared/TooltipWrapper.vue'
import { Button } from '@/components/ui/button'
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog'

const selectedCategory = ref('all')
const selectedYear = ref('all')

const applyFilter = () => {
  console.log('Applying filter:', { category: selectedCategory.value, date: selectedYear.value })
}

const resetFilters = () => {
  selectedCategory.value = 'all'
  selectedYear.value = 'all'
}
</script>

<template>
  <div>
    <!-- Trigger Button -->
    <TooltipWrapper text="Filter">
      <Dialog>
        <DialogTrigger as-child>
          <FilterX class="w-5 h-5 cursor-pointer hover:text-black" />
        </DialogTrigger>

        <!-- Modal Content -->
        <DialogContent class="sm:max-w-md">
          <DialogHeader>
            <DialogTitle>Filter Articles</DialogTitle>
            <DialogDescription>Select filters to refine your results.</DialogDescription>
          </DialogHeader>

          <div class="space-y-4">
            <div>
              <label for="category" class="block text-sm font-medium text-gray-700 mb-2"
                >Category</label
              >
              <Select
                name="category"
                id="category"
                v-model="selectedCategory"
                class="w-full p-2 border rounded-md"
                :options="[
                  { label: 'All', value: 'all' },
                  { label: 'Mathematics', value: 'mathematics' },
                  { label: 'Physics', value: 'physics' },
                  { label: 'Chemistry', value: 'chemistry' },
                ]"
              >
              </Select>
            </div>

            <div>
              <label for="year" class="block text-sm font-medium text-gray-700 mb-2">Year</label>
              <Select
                name="year"
                id="year"
                v-model="selectedYear"
                class="w-full p-2 border rounded-md"
                :options="[
                  { label: 'All', value: 'all' },
                  { label: '2025', value: '2025' },
                  { label: '2024', value: '2024' },
                  { label: '2023', value: '2023' },
                  { label: '2022', value: '2022' },
                ]"
              >
              </Select>
            </div>
          </div>

          <!-- Footer with Buttons -->
          <DialogFooter>
            <Button variant="outline" @click="resetFilters">Reset</Button>
            <Button @click="applyFilter">Apply</Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </TooltipWrapper>
  </div>
</template>
