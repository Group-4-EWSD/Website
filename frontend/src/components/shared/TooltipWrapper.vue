<script setup lang="ts">
import { Tooltip, TooltipTrigger, TooltipContent } from "@/components/ui/tooltip"

// props
// const props = defineProps({
//   text: 'Default text',
// })

const props = defineProps({
  text: {
    type: String,
    default: 'Default text',
  },
})

</script>
<template>
  <Tooltip>
    <TooltipTrigger>
      <slot />
    </TooltipTrigger>
    <TooltipContent>
      {{ props.text }}
    </TooltipContent>
  </Tooltip>
</template>
