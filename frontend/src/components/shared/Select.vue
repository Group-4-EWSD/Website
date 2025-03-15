<script setup lang="ts">
import { ErrorMessage, Field, useField } from 'vee-validate'
import { useAttrs, watchEffect } from 'vue'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import { cn } from '@/lib/utils'

interface Props {
  name: string
  class?: string
  placeholder?: string
  options: { label: string; value: string | number }[]
  errors?: Record<string, string | string[] | null | undefined>
  modelValue?: string | number
}

const props = withDefaults(defineProps<Props>(), {
  class: '',
  placeholder: 'Select an option',
})

const attrs = useAttrs() as Record<string, unknown> // Get rest props

// Vee-validate field
const { value, setValue } = useField(() => props.name)

// Watch for external modelValue updates and sync with vee-validate
watchEffect(() => {
  console.log('props.modelValue', props.modelValue, props.options)
  if (props.modelValue !== undefined) {
    setValue(props.modelValue)
  }
})
</script>

<template>
  <div class="w-full">
    <Field v-bind="attrs" :name="props.name">
      <Select :model-value="value" @update:model-value="setValue">
        <SelectTrigger
          :class="
            cn(
              'flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50',
              props.class,
              { 'border-red-500': props.errors && props.errors[props.name] }
            )
          "
        >
          <SelectValue :placeholder="props.placeholder" />
        </SelectTrigger>
        <SelectContent>
          <SelectItem
            v-for="option in props.options"
            :key="option.value"
            :value="option.value"
          >
            {{ option.label }}
          </SelectItem>
        </SelectContent>
      </Select>
    </Field>
    <ErrorMessage
      v-if="props.errors && props.errors[props.name]"
      :name="props.name"
      class="text-sm text-red-500"
    />
  </div>
</template>
