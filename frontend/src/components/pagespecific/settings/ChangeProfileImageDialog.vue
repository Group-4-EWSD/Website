<script setup lang="ts">
import type Cropper from 'cropperjs'
import { Upload } from 'lucide-vue-next'
import { onMounted, ref, type Ref } from 'vue'
import { toast } from 'vue-sonner'

import { updateProfilePhoto } from '@/api/user'
import { Button } from '@/components/ui/button'
import {
  Dialog,
  DialogContent,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog'
import { useUserStore } from '@/stores/user'

interface FileReaderEvent extends ProgressEvent {
  target: FileReader
}

interface CropperEventData {
  detail: {
    x: number
    y: number
    width: number
    height: number
    rotate: number
    scaleX: number
    scaleY: number
  }
}

const userStore = useUserStore()
const isOpen = ref<boolean>(false)
const isDragging = ref<boolean>(false)
const isLoading = ref<boolean>(false)
const selectedFile = ref<File | null>(null)
const cropperInstance: Ref<Cropper | null> = ref(null)
const cropperContainer: Ref<HTMLImageElement | null> = ref(null)

// Initial cropper setup
onMounted(() => {
  if (typeof window !== 'undefined') {
    // Dynamically import Cropper.js when component mounts
    import('cropperjs').then((module) => {
      // Cropper initialization happens later when a file is selected
    })
  }
})

const onFileSelected = (event: Event): void => {
  const input = event.target as HTMLInputElement
  const file = input.files?.[0]
  if (file) {
    handleFile(file)
  }
}

const handleFile = (file: File): void => {
  // Validate file type
  if (!file.type.match('image.*')) {
    toast.error('Please select an image file')
    return
  }

  // Validate file size (max 5MB)
  if (file.size > 5 * 1024 * 1024) {
    toast.error('Image size should be less than 5MB')
    return
  }

  selectedFile.value = file

  // Create URL for the image
  const reader = new FileReader()
  reader.onload = (e: ProgressEvent<FileReader>) => {
    // Use proper type checking for the target result
    if (e.target && e.target.result) {
      initCropper(e.target.result as string)
    }
  }
  reader.readAsDataURL(file)
}

const initCropper = (imageUrl: string): void => {
  if (cropperInstance.value) {
    cropperInstance.value.destroy()
  }

  // Set the image source
  if (cropperContainer.value) {
    cropperContainer.value.src = imageUrl

    // Initialize cropper
    import('cropperjs').then((module) => {
      const Cropper = module.default
      if (cropperContainer.value) {
        cropperInstance.value = new Cropper(cropperContainer.value, {
          aspectRatio: 1,
          viewMode: 1,
          dragMode: 'move',
          autoCropArea: 0.8,
          responsive: true,
          crop(event: CropperEventData) {
            // Access cropped area data via event.detail if needed
          },
        })
      }
    })
  }
}

const handleDragOver = (event: DragEvent): void => {
  event.preventDefault()
  isDragging.value = true
}

const handleDragLeave = (): void => {
  isDragging.value = false
}

const handleDrop = (event: DragEvent): void => {
  event.preventDefault()
  isDragging.value = false

  const file = event.dataTransfer?.files[0]
  if (file) {
    handleFile(file)
  }
}

const saveChanges = async (): Promise<void> => {
  if (!cropperInstance.value) return

  try {
    isLoading.value = true

    // Get the cropped canvas
    const canvas = cropperInstance.value.getCroppedCanvas({
      width: 200,
      height: 200,
    })

    // Convert canvas to blob
    const blob = await new Promise<Blob>((resolve) => {
      canvas.toBlob((blob) => resolve(blob as Blob), 'image/jpeg', 0.8)
    })

    // Create a File object from the blob
    const file = new File([blob], 'profile-image.jpg', { type: 'image/jpeg' })

    // Call your API function directly with the file
    const { photo_path } = await updateProfilePhoto({
      user_photo: file,
    })

    if (photo_path) {
      userStore.setProfilePhoto(photo_path)
    }

    toast.success('Profile image updated successfully')

    setTimeout(() => {
      isOpen.value = false
      resetCropper()
    }, 1500)
  } catch (err: unknown) {
    const error = err as Error
    toast.error(error.message || 'An error occurred while updating profile image')
  } finally {
    isLoading.value = false
  }
}

const resetCropper = (): void => {
  if (cropperInstance.value) {
    cropperInstance.value.destroy()
    cropperInstance.value = null
  }
  selectedFile.value = null
}

const cancelChanges = (): void => {
  resetCropper()
  isOpen.value = false
}
</script>

<template>
  <Dialog v-model:open="isOpen">
    <DialogTrigger asChild>
      <Button variant="secondary" @click="isOpen = true">Change</Button>
    </DialogTrigger>

    <DialogContent class="sm:max-w-md">
      <DialogHeader>
        <DialogTitle>Change Profile Image</DialogTitle>
      </DialogHeader>

      <!-- File upload area -->
      <div
        v-if="!selectedFile"
        class="border-2 border-dashed border-gray-300 rounded-lg p-6 text-center"
        :class="{ 'border-primary bg-primary/5': isDragging }"
        @dragover="handleDragOver"
        @dragleave="handleDragLeave"
        @drop="handleDrop"
      >
        <Upload class="mx-auto h-12 w-12 text-gray-400" />
        <div class="mt-4 text-sm text-gray-600">
          <p>Drag and drop your image here, or</p>
          <label class="cursor-pointer text-primary hover:underline">
            browse
            <input type="file" class="hidden" accept="image/*" @change="onFileSelected" />
          </label>
        </div>
        <p class="mt-2 text-xs text-gray-500">PNG, JPG or GIF up to 5MB</p>
      </div>

      <!-- Image cropper -->
      <div v-if="selectedFile" class="mt-4">
        <div class="relative w-full h-64 overflow-hidden border rounded-lg">
          <img ref="cropperContainer" class="max-w-full" />
        </div>
        <div class="mt-4 flex justify-end space-x-2">
          <Button variant="outline" @click="cancelChanges"> Cancel </Button>
          <Button
            @click="saveChanges"
            :disabled="isLoading"
            :processing="isLoading"
            variant="default"
          >
            Submit
          </Button>
        </div>
      </div>
    </DialogContent>
  </Dialog>
</template>

<style>
@import 'cropperjs/dist/cropper.css';
</style>
