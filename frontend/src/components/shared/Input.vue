<script setup lang="ts">
import { Eye, EyeOff } from 'lucide-vue-next'
import { ErrorMessage, Field } from 'vee-validate'
import { ref, useAttrs } from 'vue'

import { cn } from '@/lib/utils'

interface Props {
  type?: string
  name: string
  class?: string
  errors?: Record<string, string | string[] | null | undefined>
}

const props = withDefaults(defineProps<Props>(), {
  class: '',
  type: 'text',
})

const attrs = useAttrs() as Record<string, unknown> // Get rest props

const isPassword = props.type === 'password'
const type = ref(props.type)
</script>

<template>
  <div :class="['w-full', { relative: isPassword }]">
    <Field
      v-bind="attrs"
      :name="props.name"
      :type="isPassword ? type : props.type"
      :class="
        cn(
          'flex h-10 w-full rounded-md border border-input bg-background px-3 py-2 text-sm ring-offset-background file:border-0 file:bg-transparent file:text-sm file:font-medium placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-primary focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50',
          props.class,
          { 'border-red-500': props.errors && props.errors[props.name] }, // Apply error styling if there are errors
          { 'pr-10': isPassword }, // Add padding right if it's a password field
        )
      "
    />
    <div v-if="isPassword" class="absolute h-full top-0 right-0 flex items-center pr-3">
      <button
        type="button"
        class="text-sm text-muted-foreground"
        @click="() => (type = type === 'password' ? 'text' : 'password')"
      >
        <span v-if="type === 'password'">
          <EyeOff class="h-5 w-5" />
        </span>
        <span v-else>
          <Eye class="h-5 w-5" />
        </span>
      </button>
    </div>
  </div>

  <ErrorMessage
    v-if="props.errors && props.errors[props.name]"
    :name="props.name"
    class="text-sm text-red-500"
  />
</template>

<style lang="css" scoped>
input::-ms-reveal,
input::-ms-clear {
  display: none;
}
</style>
