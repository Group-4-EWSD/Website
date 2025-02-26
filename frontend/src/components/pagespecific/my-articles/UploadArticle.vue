<script setup lang="ts">
import { useForm } from 'vee-validate'
import { ref } from 'vue'
import DropZone from '@/components/shared/DropZone.vue'
import FormElement from '@/components/shared/FormElement.vue'
import Input from '@/components/shared/Input.vue'
import Select from '@/components/shared/Select.vue'
import { Button } from '@/components/ui/button'
import {
  Dialog,
  DialogContent,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog'
import { Label } from '@/components/ui/label'

interface UploadArticleSchema {
  title: string
  description: string
  category: string
  type: string
  files: File[]
}

// Create a ref to control the dialog open state
const isOpen = ref(false)
const isLoading = ref(false)

const { handleSubmit, errors, values, setValues } = useForm<UploadArticleSchema>({
  initialValues: {
    title: '',
    description: '',
    category: '',
    type: '',
    files: []
  }
})

// Function to submit the article
const onSubmit = handleSubmit(async (formValues: UploadArticleSchema) => {
  try {
    isLoading.value = true
    console.log('Submitting article:', formValues)
    
    // Here you would typically make an API call to submit the article
    // Example:
    // await articleApi.submitArticle(formValues)
    
    // Simulate API call with timeout
    await new Promise(resolve => setTimeout(resolve, 1000))
    
    // Close the dialog after successful submission
    isOpen.value = false
    
    // Reset form after submission
    resetForm()
    
    // You might want to show a success notification here
  } catch (error) {
    console.error('Error submitting article:', error)
    // Handle error (show error notification, etc.)
  } finally {
    isLoading.value = false
  }
})

// Function to save article as draft
const saveAsDraft = async () => {
  try {
    isLoading.value = true
    const formValues = values
    
    console.log('Saving article as draft:', formValues)
    
    // Here you would typically make an API call to save the draft
    // Example:
    // await articleApi.saveAsDraft(formValues)
    
    // Simulate API call with timeout
    await new Promise(resolve => setTimeout(resolve, 1000))
    
    // Close the dialog after saving
    isOpen.value = false
    
    // Reset form after saving
    resetForm()
    
    // You might want to show a success notification here
  } catch (error) {
    console.error('Error saving draft:', error)
    // Handle error (show error notification, etc.)
  } finally {
    isLoading.value = false
  }
}

// Function to close the modal
const closeModal = () => {
  isOpen.value = false
  resetForm()
}

// Function to reset the form
const resetForm = () => {
  setValues({
    title: '',
    description: '',
    category: '',
    type: '',
    files: []
  })
}
</script>

<template>
  <Dialog v-model:open="isOpen">
    <DialogTrigger as-child>
      <Button class="uppercase w-full" @click="isOpen = true">Upload</Button>
    </DialogTrigger>
    <DialogContent class="sm:max-w-[700px]">
      <DialogHeader>
        <DialogTitle class="uppercase">Upload your article</DialogTitle>
      </DialogHeader>
      <form @submit.prevent="onSubmit" class="space-y-4 pt-5">
        <FormElement :errors="errors" layout="row">
          <template #label>
            <Label form="title">Title</Label>
          </template>
          <template #field>
            <Input name="title" id="title" placeholder="" :errors="errors" />
          </template>
        </FormElement>
        <FormElement :errors="errors" layout="row">
          <template #label>
            <Label form="description">Description</Label>
          </template>
          <template #field>
            <Input
              name="description"
              id="description"
              placeholder=""
              :errors="errors"
              as="textarea"
              class="min-h-[120px]"
            />
          </template>
        </FormElement>
        <FormElement :errors="errors" layout="row">
          <template #label>
            <Label form="category">Category</Label>
          </template>
          <template #field>
            <Select
              name="category"
              id="category"
              :errors="errors"
              :options="[
                { label: 'Coffee', value: 'coffee' },
                { label: 'Tea', value: 'tea' },
                { label: 'Coke', value: 'coke' },
              ]"
            ></Select>
          </template>
        </FormElement>
        <FormElement :errors="errors" layout="row">
          <template #label>
            <Label form="type">Type</Label>
          </template>
          <template #field>
            <Select
              name="type"
              id="type"
              :errors="errors"
              :options="[
                { label: 'Docx', value: 'docx' },
                { label: 'Image', value: 'image' },
              ]"
            >
            </Select>
          </template>
        </FormElement>
        <DropZone />
      </form>
      <DialogFooter>
        <Button type="button" variant="ghost" @click="closeModal" :disabled="isLoading">Cancel</Button>
        <Button type="button" variant="outline" @click="saveAsDraft" :processing="isLoading">
          {{ isLoading ? 'Saving...' : 'Save as draft' }}
        </Button>
        <Button type="submit" @click="onSubmit" :processing="isLoading">
          {{ isLoading ? 'Submitting...' : 'Submit' }}
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>