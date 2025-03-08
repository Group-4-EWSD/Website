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

// Import the API function
import { ArticleStatus, uploadArticle } from '@/api/article'
import { toast } from 'vue-sonner'
import * as yup from 'yup'

interface UploadArticleSchema {
  title: string
  description: string
  category: string
  agreeToterm: boolean
  files: File[]
}

// Create refs to control the dialog and loading states
const isOpen = ref(false)
const isLoading = ref(false)

const acceptedFileTypes = ['image/jpeg', 'image/png', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document']

const validationSchema = yup.object({
    title: yup.string()
      .required('Title is required')
      .min(3, 'Title must be at least 3 characters')
      .max(100, 'Title must be less than 100 characters'),
    description: yup.string()
      .required('Description is required')
      .min(10, 'Description must be at least 10 characters')
      .max(500, 'Description must be less than 500 characters'),
    category: yup.string()
      .required('Category is required')
      .oneOf(['coffee', 'tea', 'coke'], 'Please select a valid category'),
    type: yup.string()
      .required('Type is required')
      .oneOf(['docx', 'image'], 'Please select a valid type'),
    files: yup.array()
      .of(yup.mixed<File>())
      .test('required', 'At least one file is required', (files) => files && files.length > 0)
      .test('fileSize', 'File size is too large', (files) => {
        if (!files) return true
        return files.every(file => file && file.size <= 5 * 1024 * 1024) // 5MB limit
      }),
    agreeToterm: yup.boolean()
      .required('You must agree to the terms')
      .oneOf([true], 'You must agree to the terms')
})


const { handleSubmit, errors, values, setValues, resetForm } = useForm<UploadArticleSchema>({
  initialValues: {
    title: '',
    description: '',
    category: '',
    agreeToterm: false,
    files: []
  },
})

const onSubmit = handleSubmit(async (formValues: UploadArticleSchema) => {

  if (isLoading.value) return

  try {
    isLoading.value = true
    
    // Map form values to API data structure
    const articleData = {
      article_title: formValues.title,
      article_description: formValues.description,
      article_type_id: formValues.category,
      status: ArticleStatus.SUBMITTED,
      article_details: formValues.files
    }
    
    // Submit the article using the API
    await uploadArticle(articleData)

    toast.success('Article submitted successfully')
    
    // Close the dialog after successful submission
    isOpen.value = false
    
    // Reset form
    resetForm()
  } catch (error) {
    console.error('Error submitting article:', error)
    toast.error('Failed to submit article. Please try again.')
  } finally {
    isLoading.value = false
  }
})

const saveAsDraft = async () => {
  if (isLoading.value) return

  try {
    isLoading.value = true

    const articleData = {
      article_title: values.title,
      article_description: values.description,
      article_type_id: values.category,
      status: ArticleStatus.DRAFT,
      article_details: values.files
    }
    
    await uploadArticle(articleData)

    toast.success('Article saved as draft')
    
    // Close the dialog after saving
    isOpen.value = false
    
    // Reset form
    resetForm()
  } catch (error) {
    console.error('Error saving draft:', error)
    toast.error('Failed to save draft. Please try again.')
  } finally {
    isLoading.value = false
  }
}

// Function to close the modal
const closeModal = () => {
  isOpen.value = false
  resetForm()
}

// Handle files from DropZone
const handleFilesAdded = (files: File[]) => {
  setValues({
    ...values,
    files
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
            <Label for="title">Title</Label>
          </template>
          <template #field>
            <Input name="title" id="title" placeholder="Enter article title" :errors="errors" />
          </template>
        </FormElement>
        <FormElement :errors="errors" layout="row">
          <template #label>
            <Label for="description">Description</Label>
          </template>
          <template #field>
            <Input
              name="description"
              id="description"
              placeholder="Enter article description"
              :errors="errors"
              as="textarea"
              class="min-h-[120px]"
            />
          </template>
        </FormElement>
        <FormElement :errors="errors" layout="row">
          <template #label>
            <Label for="category">Category</Label>
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
            <Label for="type">Type</Label>
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
        <DropZone 
            @files-added="handleFilesAdded" 
            :acceptedTypes="acceptedFileTypes" 
            :value="values.files"
          />
      </form>
      <DialogFooter>
        <Button type="button" variant="ghost" @click="closeModal" :disabled="isLoading">Cancel</Button>
        <Button type="button" variant="outline" @click="saveAsDraft" :disabled="isLoading">
          {{ isLoading ? 'Saving...' : 'Save as draft' }}
        </Button>
        <Button type="submit" @click="onSubmit" :disabled="isLoading">
          {{ isLoading ? 'Submitting...' : 'Submit' }}
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>