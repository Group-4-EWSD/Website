<script setup lang="ts">
import { ErrorMessage, Field, useForm } from 'vee-validate'
import { computed, onMounted, ref, watch } from 'vue'
import { toast } from 'vue-sonner'
import * as yup from 'yup'

import {
  ArticleStatus,
  getArticleDetails,
  getCategories,
  updateArticle,
  uploadArticle,
} from '@/api/articles'
import DropZone from '@/components/shared/DropZone.vue'
import FormElement from '@/components/shared/FormElement.vue'
import Input from '@/components/shared/Input.vue'
import Select from '@/components/shared/Select.vue'
import { Button } from '@/components/ui/button'
import Checkbox from '@/components/ui/checkbox/Checkbox.vue'
import {
  Dialog,
  DialogContent,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog'
import { Label } from '@/components/ui/label'
import { useMyArticlesStore } from '@/stores/my-articles'
import type { Article, ArticleResponse, Category, UpdateArcitleData } from '@/types/article'

interface UploadArticleSchema {
  title: string
  description: string
  category: string
  agreeToterm: boolean
  files: File[]
}

const props = defineProps<{
  article_id?: string
  modelValue?: boolean
}>()

const emit = defineEmits<{
  (e: 'update:modelValue', value: boolean): void
}>()

// Create refs to control the dialog and loading states
const isOpen = ref(props.modelValue || false)
const isSubmitting = ref(false)
const categories = ref<{ label: string; value: string }[]>([])
const isEditMode = ref(props.article_id !== undefined)
const isEditableState = ref(false)
const isAlreadySubmitted = ref(false)
const existingFiles = ref<string[]>([])
const isLoading = ref(false)

// Computed property to check if article is already submitted
// const isAlreadySubmitted = computed((article: Article) => {
//   return props.article_id &&  !== undefined && props.article.status !== ArticleStatus.DRAFT;
// })

// Watch for changes in isOpen and emit events
// watch(isOpen, (value) => {
//   emit('update:modelValue', value)
// })

// Watch for changes in props.modelValue
// watch(
//   () => props.modelValue,
//   (value) => {
//     isOpen.value = value || false
//   },
// )

// Watch for changes in props.article
watch(
  () => props.article_id,
  async (article_id) => {
    console.log(article_id)

    if (article_id) {
      const articleDetails = await getArticleDetails(article_id)
      console.log(articleDetails)
    }

    // isEditMode.value = !!article
    // if (article) {
    //   setFieldValue('title', article.article_title || '')
    //   setFieldValue('description', article.article_description || '')
    //   setFieldValue('category', article.article_type_id || '')
    //   setFieldValue('agreeToterm', true)
    //   // Note: Existing files handling depends on your API structure
    //   // This is a placeholder - you might need to adjust based on your actual data structure
    //   // if (article.article_details && Array.isArray(article.article_details)) {
    //   //   setFieldValue('files', article.article_details)
    //   // }
    // }
  },
)

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
    .test('required', 'At least one file is required', (files) => {
      // Skip validation if in edit mode and there are existing files
      if (isEditMode.value && existingFiles.value.length > 0) return true
      return files && files.length > 0
    })
    .test('fileSize', 'File size is too large', (files) => {
      if (!files) return true
      return files.every((file) => file && file.size <= 5 * 1024 * 1024) // 5MB limit
    }),
})

const { handleSubmit, errors, values, resetForm, setFieldValue } = useForm<UploadArticleSchema>({
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
  if (isSubmitting.value) return

  try {
    isSubmitting.value = true

    // Map form values to API data structure
    const articleData = {
      article_id: props.article_id || '',
      article_title: formValues.title,
      article_description: formValues.description,
      article_type_id: formValues.category,
      status: ArticleStatus.PENDING,
      article_details: formValues.files || [],
    }

    if (isEditMode.value) {
      const data = {
        ...articleData,
        article_remaining_files: [...existingFiles.value],
      } as UpdateArcitleData
      await updateArticle(data)
    } else {
      await uploadArticle(articleData)
    }

    toast.success(
      isEditMode.value ? 'Article updated successfully' : 'Article submitted successfully',
    )

    const myArticlesStore = useMyArticlesStore()
    void myArticlesStore.fetchArticles(true)

    // Close the dialog after successful submission
    isOpen.value = false

    // Reset form
    resetForm()
  } catch (error) {
    console.error('Error submitting article:', error)
    toast.error(`Failed to ${isEditMode.value ? 'update' : 'submit'} article. Please try again.`)
  } finally {
    isSubmitting.value = false
  }
})

const saveAsDraft = async () => {
  if (isSubmitting.value) return

  try {
    isSubmitting.value = true

    const articleData = {
      article_id: props.article_id,
      article_title: values.title,
      article_description: values.description,
      article_type_id: values.category,
      status: ArticleStatus.DRAFT,
      article_details: values.files || [],
    }

    if (isEditMode.value) {
      const data = {
        ...articleData,
        article_remaining_files: [...existingFiles.value],
      } as UpdateArcitleData
      await updateArticle(data)
    } else {
      await uploadArticle(articleData)
    }

    toast.success(isEditMode.value ? 'Draft updated successfully' : 'Article saved as draft')

    const myArticlesStore = useMyArticlesStore()
    void myArticlesStore.fetchArticles(true)

    // Close the dialog after saving
    isOpen.value = false

    // Reset form
    resetForm()
  } catch (error) {
    console.error('Error saving draft:', error)
    toast.error('Failed to save draft. Please try again.')
  } finally {
    isSubmitting.value = false
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

onMounted(async () => {
  try {
    isLoading.value = true
    // True parallel fetching with Promise.all
    const promises: Promise<any>[] = [getCategories()]

    // Only add article fetch if article_id exists
    if (props.article_id) {
      promises.push(getArticleDetails(props.article_id))
    }

    // Wait for all promises to resolve simultaneously
    const results = await Promise.all(promises)

    // Process results
    const rawCategories = results[0]
    categories.value = rawCategories.map((category: Category) => ({
      label: category.article_type_name,
      value: category.article_type_id,
    }))

    // Process article details if it was fetched
    if (props.article_id) {
      const resp = results[1].data as ArticleResponse
      // const articleDetails = resp.data as ArticleResponse

      setFieldValue('title', resp.articleDetail?.article_title || '')
      setFieldValue('description', resp.articleDetail?.article_description || '')
      setFieldValue('category', resp.articleDetail?.article_type_id || '')

      const photoKeys = Object.keys(resp.articlePhotos)
      const contentKeys = Object.keys(resp.articleContent)
      existingFiles.value = [...photoKeys, ...contentKeys]

      isEditableState.value =
        resp.articleDetail?.article_status === ArticleStatus.DRAFT ||
        resp.articleDetail?.article_status === ArticleStatus.PENDING
      isAlreadySubmitted.value = resp.articleDetail?.article_status !== ArticleStatus.DRAFT
    }
  } catch (error) {
    console.error('Error in fetching operations:', error)
    toast.error('Failed to load data')
  } finally {
    isLoading.value = false
  }
})
</script>

<template>
  <Dialog v-model:open="isOpen">
    <slot name="trigger">
      <Button class="uppercase w-full" @click="isOpen = true">{{
        isEditMode ? 'Edit' : 'Upload'
      }}</Button>
    </slot>
    <DialogContent class="sm:max-w-[700px]">
      <DialogHeader>
        <DialogTitle class="uppercase">{{
          isEditMode ? 'Edit your article' : 'Upload your article'
        }}</DialogTitle>
      </DialogHeader>

      <div v-if="isLoading" class="flex items-center justify-center h-40">Loading . . .</div>

      <form @submit.prevent="onSubmit" class="space-y-4 pt-5" v-else>
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
              :options="categories"
              :modelValue="values.category"
            ></Select>
          </template>
        </FormElement>
        <DropZone
          @files-added="handleFilesAdded"
          :acceptedTypes="acceptedFileTypes"
          :value="values.files"
          :errors="errors"
          :existingFiles="existingFiles"
          name="files"
        />

        <FormElement>
          <template #label>
            <Field name="agreeToterm" v-slot="{ field, handleChange }">
              <div class="flex items-center gap-2">
                <Checkbox
                  :id="field.name"
                  :name="field.name"
                  :checked="values.agreeToterm"
                  @update:modelValue="
                    (value) => {
                      setFieldValue('agreeToterm', Boolean(value), true)
                      handleChange(Boolean(value))
                    }
                  "
                />
                <Label :for="field.name" class="text-sm cursor-pointer">
                  I agree to the <a href="/terms-and-conditions" target="_blank" class="text-blue-500">terms and conditions</a>
                </Label>
              </div>
            </Field>
            <ErrorMessage name="agreeToterm" class="text-sm text-red-500" />
          </template>
        </FormElement>
      </form>
      <DialogFooter class="gap-2">
        <Button type="button" variant="ghost" @click="closeModal" :disabled="isSubmitting"
          >Cancel</Button
        >

        <!-- Only show Save as Draft button if the article is not already submitted -->
        <Button
          v-if="!isAlreadySubmitted"
          type="button"
          variant="outline"
          @click="saveAsDraft"
          :disabled="isSubmitting"
        >
          {{ isSubmitting ? 'Saving...' : isEditMode ? 'Update draft' : 'Save as draft' }}
        </Button>

        <Button type="submit" @click="onSubmit" :disabled="isSubmitting">
          {{ isSubmitting ? 'Submitting...' : 'Submit' }}
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
