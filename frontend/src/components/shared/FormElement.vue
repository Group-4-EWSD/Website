<script setup lang="ts">
import { Field, ErrorMessage } from 'vee-validate'
import { cn } from '@/lib/utils'

interface Props {
  id: string
  name: string
  placeholder?: string
  label?: string
  errors?: Record<string, string | string[] | null>
  class?: string
}

const props = withDefaults(defineProps<Props>(), {
  placeholder: '',
  label: '',
  class: '',
})
</script>
<template>
  <div class="flex flex-col gap-3">
    <label v-if="props.label" :for="props.name" class="text-sm">{{ props.label }}</label>
    <Field
      :id="props.id"
      :name="props.name"
      :placeholder="props.placeholder"
      :class="
        cn(
          'flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50',
          props.class,
          { 'border-red-500': props.errors && props.errors[props.name] },
        )
      "
    />
    <ErrorMessage
      v-if="errors && errors[props.name]"
      :name="props.name"
      class="text-sm text-red-500"
    />
  </div>
</template>
