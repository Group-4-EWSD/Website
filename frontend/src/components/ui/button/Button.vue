<script setup lang="ts">
import { Loader } from 'lucide-vue-next'
import { Primitive, type PrimitiveProps } from 'radix-vue'
import type { HTMLAttributes } from 'vue'

import { cn } from '@/lib/utils'

import { type ButtonVariants, buttonVariants } from '.'

interface Props extends PrimitiveProps {
  variant?: ButtonVariants['variant']
  size?: ButtonVariants['size']
  class?: HTMLAttributes['class']
  processing?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  as: 'button',
})
</script>

<template>
  <Primitive
    :as="as"
    :as-child="asChild"
    :class="cn(buttonVariants({ variant, size }), props.class)"
  >
    <Loader v-if="processing" name="fa-spinner" class="animate-spin h-5 w-5" /> <slot />
  </Primitive>
</template>
