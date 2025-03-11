<script setup lang="ts">
import { ErrorMessage, useForm, Field } from 'vee-validate'
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
import { ArticleStatus, uploadArticle } from '@/api/articles'
import { toast } from 'vue-sonner'
import * as yup from 'yup'
import Checkbox from '@/components/ui/checkbox/Checkbox.vue'

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
const isOnSubmit = ref(false)

const acceptedFileTypes = [
  'image/jpeg',
  'image/png',
  'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
]

const validationSchema = yup.object({
  title: yup
    .string()
    .required('Title is required')
    .min(3, 'Title must be at least 3 characters')
    .max(100, 'Title must be less than 100 characters'),
  description: yup.string().max(500, 'Description must be less than 500 characters'),
  category: yup.string().required('Category is required'),
  agreeToterm: yup.boolean().oneOf([true], 'Please agree to the terms and conditions'),
  files: yup
    .array()
    .of(yup.mixed<File>())
    .test('required', 'At least one file is required', (files) => files && files.length > 0)
    .test('fileSize', 'File size is too large', (files) => {
      if (!files) return true
      return files.every((file) => file && file.size <= 5 * 1024 * 1024) // 5MB limit
    }),
})

const { handleSubmit, errors, values, setValues, resetForm, setFieldValue } =
  useForm<UploadArticleSchema>({
    validationSchema,
    initialValues: {
      title: '',
      description: '',
      category: '',
      agreeToterm: false,
      files: [],
    },
  })

const onSubmit = handleSubmit(async (formValues: UploadArticleSchema) => {
  if (isLoading.value) return

  console.log('Form values:', formValues)

  // if (!formValues.agreeToterm) {
  //   toast.error('Please agree to the terms and conditions')
  //   return
  // }

  try {
    isLoading.value = true

    // Map form values to API data structure
    const articleData = {
      article_title: formValues.title,
      article_description: formValues.description,
      article_type_id: formValues.category,
      status: ArticleStatus.SUBMITTED,
      article_details: formValues.files,
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
      article_details: values.files,
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
  setFieldValue('files', files)
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
        <FormElement layout="row">
          <template #label>
            <Label for="title">Title</Label>
          </template>
          <template #field>
            <Input name="title" id="title" placeholder="Enter article title" :errors="errors" />
          </template>
        </FormElement>
        <FormElement layout="row">
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
        <FormElement layout="row">
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
        <DropZone
          @files-added="handleFilesAdded"
          :acceptedTypes="acceptedFileTypes"
          :value="values.files"
          :errors="errors"
          name="files"
        />

        <FormElement>
          <template #label>
            <Field name="agreeToterm" v-slot="{ field, handleChange }">
              <div class="flex items-center gap-2">
                <Checkbox
                  :id="field.name"
                  :name="field.name"
                  @update:modelValue="
                    (value) => {
                      setFieldValue('agreeToterm', Boolean(value), true)
                      handleChange(Boolean(value))
                    }
                  "
                />
                <Label :for="field.name" class="text-sm cursor-pointer">
                  I agree to the terms and conditions
                </Label>
              </div>
            </Field>
            <ErrorMessage name="agreeToterm" class="text-sm text-red-500" />
          </template>
        </FormElement>
      </form>
      <DialogFooter>
        <Button type="button" variant="ghost" @click="closeModal" :disabled="isLoading"
          >Cancel</Button
        >
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
