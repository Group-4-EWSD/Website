<script setup lang="ts">
import { computed, ref } from 'vue'
import { Dialog, DialogContent, DialogHeader, DialogTitle } from '@/components/ui/dialog'
import { Input } from '@/components/ui/input'
import { Button } from '@/components/ui/button'

const props = defineProps<{
  modelValue: boolean
  title?: string
}>()

const emit = defineEmits(['update:modelValue', 'submit', 'quick-approve'])

const feedback = ref('')
const error = ref('')

const updateOpen = (value: boolean) => {
  emit('update:modelValue', value)
  if (!value) {
    feedback.value = ''
    error.value = ''
  }
}

const submitFeedback = () => {
  if (!feedback.value.trim()) {
    error.value = 'Feedback is required.'
    return
  }
  emit('submit', feedback.value.trim())
  updateOpen(false)
}

const isApproved = computed(() => {
  return props.title === 'Approve'
})

const quickApprove = () => {
  emit('quick-approve')
  updateOpen(false)
}
</script>

<template>
  <Dialog :open="modelValue" @update:open="updateOpen">
    <DialogContent class="sm:max-w-md">
      <DialogHeader>
        <DialogTitle>Provide Feedback to {{ title }}</DialogTitle>
      </DialogHeader>
      <div class="py-2">
        <Input v-model="feedback" placeholder="Enter your feedback..." class="w-full" required />
        <p v-if="error" class="text-sm text-red-500 ml-2 mt-2">{{ error }}</p>
      </div>

      <div class="flex justify-end gap-2 pt-2">
        <Button variant="outline" @click="updateOpen(false)">Cancel</Button>
        <!-- approve and quick approve -->
        <Button v-if="isApproved" variant="outline" @click="quickApprove">Quick Approve</Button>
        <Button @click="submitFeedback">{{ title }}</Button>
      </div>
    </DialogContent>
  </Dialog>
</template>
