<script setup lang="ts">
import { MenuIcon } from 'lucide-vue-next'
import type { HTMLAttributes } from 'vue'

import { Button } from '@/components/ui/button'
import { cn } from '@/lib/utils'

import { useSidebar } from './utils'

const props = defineProps<{
  class?: HTMLAttributes['class']
}>()

const { toggleSidebar } = useSidebar()
</script>

<template>
  <Button
    data-sidebar="trigger"
    size="icon"
    :class="cn('h-8 w-8', props.class)"
    @click="toggleSidebar"
  >
    <MenuIcon class="menu-icon" />
    <span class="sr-only">Toggle Sidebar</span>
  </Button>
</template>

<style scoped>
.menu-icon {
  width: 2rem;
  height: 2rem;
}
</style>
