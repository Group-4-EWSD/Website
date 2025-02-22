<script setup lang="ts">
import { useForm } from 'vee-validate'
// import { ref } from 'vue'

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

// const loading = ref(false)

// const schema = yup.object({
//   email: yup.string().email('Invalid email').required('Please enter your email'),
//   password: yup
//     .string()
//     .required('Please enter your password')
//     .min(8, 'Password must be at least 8 characters.'),
// })

interface UploadArticleSchema {
  title: string
  description: string
  category: string
  type: string
  files: File[]
}

const { handleSubmit, errors } = useForm<UploadArticleSchema>({
  // validationSchema: schema,
})

const onSubmit = handleSubmit(async (values: UploadArticleSchema) => {
  console.log(values)
})
</script>

<template>
  <Dialog>
    <DialogTrigger as-child>
      <Button class="uppercase w-full">Upload</Button>
    </DialogTrigger>
    <DialogContent class="sm:max-w-[700px]">
      <DialogHeader>
        <DialogTitle class="uppercase">Upload your article</DialogTitle>
      </DialogHeader>
      <form @submit="onSubmit" class="space-y-4 pt-5">
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
        <Button type="submit" variant="ghost"> Cancel </Button>
        <Button type="submit" variant="outline"> Save as draft </Button>
        <Button type="submit"> Submit </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
