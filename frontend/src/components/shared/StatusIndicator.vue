<script setup lang="ts">
import { computed } from 'vue'

const props = defineProps<{
  status: number
}>()

const statusText = computed(() => {
  switch (props.status) {
    case 0:
      return 'Draft'
    case 1:
      return 'Pending'
    case 2:
      return 'Approved'
    case 3:
      return 'Rejected'
    case 4:
      return 'Published'
    default:
      return 'Unknown'
  }
})
</script>
<template>
  <div class="flex items-center space-x-2">
    <div class="inline-flex items-center">
      <span
        class="px-2.5 py-1 text-xs font-medium rounded-md"
        :class="{
          'bg-accent text-accent-foreground': status === 0,
          'bg-yellow-100 text-yellow-800': status === 1,
          'bg-green-100 text-green-800': status === 2,
          'bg-red-100 text-red-800': status === 3,
          'bg-primary text-primary-foreground': status === 4,
          'bg-gray-100 text-gray-600': ![0, 1, 2, 3, 4].includes(status),
        }"
      >
        {{ statusText }}
      </span>
    </div>
  </div>
</template>
