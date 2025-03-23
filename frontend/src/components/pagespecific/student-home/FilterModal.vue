<script setup lang="ts">
import { FilterX } from 'lucide-vue-next'
import { ref, watch } from 'vue'

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
import { Select, SelectContent, SelectItem, SelectTrigger } from '@/components/ui/select'

const props = defineProps<{
  selectedCategory: string
  selectedYear: string
}>()

const emit = defineEmits<{
  (e: 'update:selectedCategory', value: string): void
  (e: 'update:selectedYear', value: string): void
}>()

const localCategory = ref(props.selectedCategory)
const localYear = ref(props.selectedYear)

// Watch for local changes and emit events to the parent
watch(localCategory, (newVal) => {
  emit('update:selectedCategory', newVal)
})

watch(localYear, (newVal) => {
  emit('update:selectedYear', newVal)
})

const categoryOptions = [
  { label: 'All', value: 'all' },
  { label: 'Mathematics', value: 'mathematics' },
  { label: 'Physics', value: 'physics' },
  { label: 'Chemistry', value: 'chemistry' },
]

const yearOptions = [
  { label: 'All', value: 'all' },
  { label: '2025', value: '2025' },
  { label: '2024', value: '2024' },
  { label: '2023', value: '2023' },
  { label: '2022', value: '2022' },
]

const getLabel = (value: string) => {
  const found = categoryOptions.find((option) => option.value === value)
  return found ? found.label : ''
}

const applyFilter = () => {
  console.log('Applying filter:', { category: localCategory.value, date: localYear.value })
}

const resetFilters = () => {
  localCategory.value = 'all'
  localYear.value = 'all'
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
              <Select v-model="localCategory">
                <SelectTrigger class="w-full p-2 border rounded-md">
                  {{
                    selectedCategory !== 'all' ? getLabel(selectedCategory) : 'Select a category'
                  }}
                </SelectTrigger>
                <SelectContent>
                  <SelectItem
                    v-for="option in categoryOptions"
                    :key="option.value"
                    :value="option.value"
                  >
                    {{ option.label }}
                  </SelectItem>
                </SelectContent>
              </Select>
            </div>

            <div>
              <label for="year" class="block text-sm font-medium text-gray-700 mb-2">Year</label>
              <Select v-model="localYear">
                <SelectTrigger class="w-full p-2 border rounded-md">
                  {{ selectedYear !== 'all' ? selectedYear : 'Select a year' }}
                </SelectTrigger>
                <SelectContent>
                  <SelectItem
                    v-for="option in yearOptions"
                    :key="option.value"
                    :value="option.value"
                  >
                    {{ option.label }}
                  </SelectItem>
                </SelectContent>
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
