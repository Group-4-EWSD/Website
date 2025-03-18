<script setup lang="ts">
defineProps<{
  label: string
  value: string
  extraClass?: string
}>()
</script>
<template>
  <div class="grid grid-cols-12 h-14 mb-4 md:mb-0">
    <div
      :class="[
        'col-span-12 md:col-span-3 font-medium text-muted-foreground flex items-center text-sm',
        extraClass,
      ]"
    >
      {{ label }}
    </div>
    <div class="col-span-12 md:col-span-8 flex items-center text-sm">
      {{ value || '-' }}
    </div>
  </div>
</template>
