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
  categoryOptions: { label: string; value: string }[]
  yearOptions: { label: string; value: string }[]
}>()

const emit = defineEmits<{
  (e: 'update:selectedCategory', value: string): void
  (e: 'update:selectedYear', value: string): void
}>()

const localCategory = ref(props.selectedCategory)
const localYear = ref(props.selectedYear)
const isDialogOpen = ref(false)

const applyFilter = () => {
  emit('update:selectedCategory', localCategory.value === 'all' ? '' : localCategory.value)
  emit('update:selectedYear', localYear.value === 'all' ? '' : localYear.value)
  isDialogOpen.value = false
}

const resetFilters = () => {
  localCategory.value = 'all'
  localYear.value = 'all'
}

watch(
  () => props.selectedCategory,
  (newVal) => {
    localCategory.value = newVal || 'all'
  },
)

watch(
  () => props.selectedYear,
  (newVal) => {
    localYear.value = newVal || 'all'
  },
)
</script>

<template>
  <div>
    <!-- Trigger Button -->
    <TooltipWrapper text="Filter">
      <Dialog v-model:open="isDialogOpen">
        <DialogTrigger as-child>
          <FilterX class="w-5 h-5 cursor-pointer hover:text-black mt-2" />
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
                >Faculty</label
              >
              <Select v-model="localCategory">
                <SelectTrigger class="w-full p-2 border rounded-md">
                  {{
                    categoryOptions.find((o) => o.value === localCategory)?.label ||
                    'Select a faculty'
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
                  {{ yearOptions.find((o) => o.value === localYear)?.label || 'Select a year' }}
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
