<script setup lang="ts">
interface Props {
  layout?: 'column' | 'row'
}

const props = withDefaults(defineProps<Props>(), {
  layout: 'column',
})
</script>
<template>
  <div :class="[props.layout === 'row' ? 'grid grid-cols-12 gap-3' : 'flex flex-col gap-3']">
    <div :class="[props.layout === 'row' ? 'col-span-3' : '']">
      <slot name="label"></slot>
    </div>
    <div :class="[props.layout === 'row' ? 'col-span-9' : '']">
      <slot name="field"></slot>
    </div>
  </div>
</template>
