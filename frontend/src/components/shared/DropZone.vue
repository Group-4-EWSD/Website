<script setup lang="ts">
import { FileText, Image as ImageIcon, XIcon } from 'lucide-vue-next'
import { ErrorMessage } from 'vee-validate'
import { defineEmits, ref, watch } from 'vue'

import Input from '@/components/shared/Input.vue'
import { Button } from '@/components/ui/button'

interface Props {
  value?: File[]
  acceptedTypes?: string[]
  maxFileSize?: number
  errors?: Record<string, string | string[] | null | undefined>
  name?: string
}

const props = withDefaults(defineProps<Props>(), {
  value: () => [],
  acceptedTypes: () => [
    'image/jpeg',
    'image/png',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
  ],
  maxFileSize: 5 * 1024 * 1024, // 5MB
  name: 'files',
})

const emit = defineEmits(['files-added', 'update:modelValue'])

const files = ref<File[]>(props.value || [])
const isDragging = ref(false)
const fileInput = ref<HTMLInputElement | null>(null)

// Watch for changes in files and emit events
watch(
  files,
  (newFiles) => {
    emit('files-added', newFiles)
    emit('update:modelValue', newFiles)
  },
  { deep: true },
)

const onDragOver = () => {
  isDragging.value = true
}

const onDragLeave = () => {
  isDragging.value = false
}

const onDrop = (event: DragEvent) => {
  event.preventDefault()
  isDragging.value = false
  if (event.dataTransfer?.files) {
    handleFiles(Array.from(event.dataTransfer.files))
  }
}

const selectFile = () => {
  fileInput.value?.click()
}

const onFileSelect = (event: Event) => {
  const target = event.target as HTMLInputElement
  if (target.files) {
    handleFiles(Array.from(target.files))
  }
}

const handleFiles = (selectedFiles: File[]) => {
  const validFiles = selectedFiles.filter(
    (file) => props.acceptedTypes.includes(file.type) && file.size <= props.maxFileSize,
  )

  if (validFiles.length !== selectedFiles.length) {
    // Could add notification here for invalid files
    console.warn('Some files were rejected due to invalid type or size')
  }

  files.value = [...files.value, ...validFiles]
}

const removeFile = (index: number) => {
  files.value.splice(index, 1)
}

const getFileIcon = (file: File) => {
  return file.type.includes('image') ? ImageIcon : FileText
}

const getFileTypeLabel = (file: File) => {
  if (file.type.includes('image')) {
    return 'Image'
  } else if (file.type.includes('wordprocessingml') || file.type.includes('msword')) {
    return 'Document'
  }
  return 'File'
}

// Format file size to human-readable format
const formatFileSize = (bytes: number) => {
  if (bytes < 1024) return bytes + ' B'
  else if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB'
  else return (bytes / 1048576).toFixed(1) + ' MB'
}
</script>

<template>
  <div
    class="border-[1.5px] border-dashed border-input rounded-md p-5 transition-all min-h-[160px] flex flex-col items-center justify-center"
    @dragover.prevent="onDragOver"
    @dragleave="onDragLeave"
    @drop.prevent="onDrop"
    :class="{ 'bg-muted': isDragging, 'border-red-500': props.errors && props.errors[props.name] }"
  >
    <div class="flex flex-col items-center gap-2" v-if="!files.length">
      <span class="text-muted-foreground">Drag & drop files here</span>
      <span class="text-muted-foreground">or</span>
      <Button variant="outline" size="sm" @click="selectFile">Browse</Button>
      <p class="text-xs text-muted-foreground mt-2 text-center">
        <!-- Accepted file types: {{ props.acceptedTypes.join(', ') }}<br> -->
        Max size: {{ formatFileSize(props.maxFileSize) }}
      </p>
    </div>

    <div v-if="files.length" class="w-full">
      <div class="flex justify-between items-center mb-4">
        <h4 class="text-sm font-medium">Uploaded Files ({{ files.length }})</h4>
        <Button variant="ghost" size="sm" @click="selectFile">Add More</Button>
      </div>

      <div class="flex flex-row gap-4 flex-wrap">
        <div
          v-for="(file, index) in files"
          :key="index"
          class="p-3 border rounded-md relative flex flex-col items-center w-[120px] hover:bg-muted transition-colors"
        >
          <component :is="getFileIcon(file)" class="w-10 h-10 text-primary mb-2" />
          <span class="block truncate text-sm w-full text-center">{{ file.name }}</span>
          <span class="text-xs text-muted-foreground mt-1">{{ getFileTypeLabel(file) }}</span>
          <span class="text-xs text-muted-foreground">{{ formatFileSize(file.size) }}</span>
          <button
            type="button"
            @click="removeFile(index)"
            class="absolute -top-2 -right-2 bg-destructive text-destructive-foreground rounded-full w-6 h-6 flex items-center justify-center hover:bg-destructive/90 transition-colors"
            aria-label="Remove file"
          >
            <XIcon class="w-4 h-4" />
          </button>
        </div>
      </div>
    </div>

    <Input
      :name="props.name"
      type="file"
      @change="onFileSelect"
      :accept="props.acceptedTypes.join(',')"
      class="hidden"
    />
    <input
      type="file"
      ref="fileInput"
      @change="onFileSelect"
      multiple
      hidden
      :accept="props.acceptedTypes.join(',')"
    />
  </div>
  <ErrorMessage
    v-if="props.errors && props.errors[props.name]"
    :name="props.name"
    class="text-sm text-red-500"
  />
</template>
