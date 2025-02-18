<script setup lang="ts">
import { ref } from 'vue'
import { useDropzone } from 'vue3-dropzone'

const files = ref([])

const onDrop = (acceptedFiles: any) => {
  files.value = acceptedFiles
  console.log('Files uploaded:', files.value)
}

const { getRootProps, getInputProps, isDragActive } = useDropzone({ onDrop })
</script>

<template>
  <div
    v-bind="getRootProps()"
    class="border-2 border-dashed border-gray-300 p-6 rounded-lg text-center cursor-pointer"
  >
    <input v-bind="getInputProps()" />
    <svg
      class="w-10 h-10 text-gray-500 mx-auto"
      xmlns="http://www.w3.org/2000/svg"
      fill="none"
      viewBox="0 0 24 24"
      stroke="currentColor"
    >
      <path
        stroke-linecap="round"
        stroke-linejoin="round"
        stroke-width="2"
        d="M7 16v2a4 4 0 004 4h2a4 4 0 004-4v-2M5 12l7-7 7 7M12 5v13"
      ></path>
    </svg>
    <p v-if="isDragActive" class="text-gray-700">Drop the files here...</p>
    <p v-else class="text-gray-500">Drag and drop files here, or click to select files</p>
  </div>

  <ul v-if="files.length" class="mt-4">
    <li v-for="(file, index) in files" :key="index" class="text-gray-700">
      <!-- File name - {{ file.name }} ({{ (file.size / 1024).toFixed(2) }} KB) -->
    </li>
  </ul>
</template>
