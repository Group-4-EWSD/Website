<script setup lang="ts">
import type { HTMLAttributes } from 'vue'

import { cn } from '@/lib/utils'

const props = defineProps<{
  class?: HTMLAttributes['class']
}>()
</script>

<template>
  <li
    data-sidebar="menu-item"
    :class="
      cn('group/menu-item relative hover:bg-primary hover:text-white hover:rounded-lg', props.class)
    "
  >
    <slot />
  </li>
</template>
