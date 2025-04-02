<script setup lang="ts">
import { Loader2 } from 'lucide-vue-next'
import { computed, ref } from 'vue'

import { Button } from '@/components/ui/button'
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog'

interface Props {
  title?: string
  cancelButtonText?: string
  confirmButtonText?: string
  confirmAction?: () => Promise<void> | void
  modelValue?: boolean
}

const props = withDefaults(defineProps<Props>(), {
  title: 'Confirm Action',
  cancelButtonText: 'Cancel',
  confirmButtonText: 'Confirm',
  confirmAction: undefined,
  modelValue: false,
})

const emit = defineEmits<{
  'update:modelValue': [value: boolean]
  cancel: []
  confirm: []
}>()

const isLoading = ref(false)

const isOpen = computed({
  get: () => props.modelValue,
  set: (value: boolean) => emit('update:modelValue', value),
})

const handleCancel = (): void => {
  emit('cancel')
  isOpen.value = false
}

const handleConfirm = async (): Promise<void> => {
  if (props.confirmAction) {
    isLoading.value = true
    try {
      await props.confirmAction()
      isOpen.value = false
    } catch (error) {
      console.error('Confirmation action failed:', error)
    } finally {
      isLoading.value = false
    }
  } else {
    emit('confirm')
    isOpen.value = false
  }
}
</script>

<template>
  <Dialog v-model:open="isOpen">
    <DialogContent class="sm:max-w-md">
      <DialogHeader>
        <DialogTitle>{{ title }}</DialogTitle>
        <DialogDescription>
          <slot>Are you sure you want to continue?</slot>
        </DialogDescription>
      </DialogHeader>

      <DialogFooter class="flex flex-row gap-2 justify-end">
        <Button @click="handleCancel" :disabled="isLoading" variant="outline">
          {{ cancelButtonText }}
        </Button>
        <Button @click="handleConfirm" :disabled="isLoading" variant="default">
          <Loader2 v-if="isLoading" class="mr-2 h-4 w-4 animate-spin" />
          {{ confirmButtonText }}
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
