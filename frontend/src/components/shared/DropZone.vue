<script setup lang="ts">
import { ref } from 'vue';
import { Button } from '@/components/ui/button';
import { FileText, Image as ImageIcon } from 'lucide-vue-next';

const files = ref<File[]>([]);
const isDragging = ref(false);
const fileInput = ref<HTMLInputElement | null>(null);

const allowedTypes = ['image/jpeg', 'image/png', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
const maxSize = 5 * 1024 * 1024; // 5MB

const onDragOver = () => {
  isDragging.value = true;
};

const onDragLeave = () => {
  isDragging.value = false;
};

const onDrop = (event: DragEvent) => {
  event.preventDefault();
  isDragging.value = false;
  if (event.dataTransfer?.files) {
    handleFiles(Array.from(event.dataTransfer.files));
  }
};

const selectFile = () => {
  fileInput.value?.click();
};

const onFileSelect = (event: Event) => {
  const target = event.target as HTMLInputElement;
  if (target.files) {
    handleFiles(Array.from(target.files));
  }
};

const handleFiles = (selectedFiles: File[]) => {
  const validFiles = selectedFiles.filter(file => allowedTypes.includes(file.type) && file.size <= maxSize);
  files.value = [...files.value, ...validFiles];
};

const removeFile = (index: number) => {
  files.value.splice(index, 1);
};

const getFileIcon = (file: File) => {
  return file.type.includes('image') ? ImageIcon : FileText;
};
</script>

<template>
  <div
    class="border-[1.5px] border-dashed border-input rounded-md p-5 transition-all min-h-[160px] flex flex-col items-center justify-center"
    @dragover.prevent="onDragOver"
    @dragleave="onDragLeave"
    @drop.prevent="onDrop"
    :class="{ 'bg-input': isDragging }"
  >
    <div class="flex flex-col items-center gap-2" v-if="!files.length">
      <span>Drag & drop files here</span>
      <span>or</span>
      <Button variant="link" @click="selectFile">Upload</Button>
    </div>
    <div v-if="files.length" class="flex flex-row gap-4 flex-wrap">
      <div v-for="(file, index) in files" :key="index" class="p-2 border rounded-md text-center relative flex flex-col items-center w-[100px]">
        <component :is="getFileIcon(file)" class="w-10 h-10 text-gray-500 mb-2" />
        <span class="block truncate text-sm w-[100px]">{{ file.name }}</span>
        <button @click="removeFile(index)" class="absolute top-1 right-1 text-destructive rounded-full w-6 h-6 flex items-center justify-center">×</button>
      </div>
    </div>
    <input type="file" ref="fileInput" @change="onFileSelect" multiple hidden />
  </div>
</template>
