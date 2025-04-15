<script setup lang="ts">
import ContactUs from '@/assets/contact-us.png'
import { Button } from '@/components/ui/button'
import PublicNavBar from '@/components/shared/PublicNavBar.vue'
import PublicFooter from '@/components/shared/PublicFooter.vue'
import Input from '@/components/shared/Input.vue'
import { Label } from '@/components/ui/label'
import FormElement from '@/components/shared/FormElement.vue'
import { useForm } from 'vee-validate'
import * as yup from 'yup'
import { ref } from 'vue'

const schema = yup.object({
  visitor_name: yup.string().required('Name is required'),
  visitor_email: yup.string().email('Please enter a valid email').required('Email is required'),
  title: yup.string().required('Title is required'),
  description: yup.string().required('Description is required'),
})

const { handleSubmit, errors } = useForm({
  validationSchema: schema,
})

const isSubmitting = ref(false)
const isSubmitted = ref(false)

const onSubmit = handleSubmit((values) => {
  isSubmitting.value = true

  // Simulate form submission
  setTimeout(() => {
    console.log('Form submitted:', values)
    isSubmitting.value = false
    isSubmitted.value = true
  }, 1000)
})
</script>

<template>
  <div class="min-h-screen bg-white">
    <PublicNavBar />
    <!-- Hero Section -->
    <div class="2xl:container 2xl:px-4 mx-auto grid grid-cols-1 lg:grid-cols-2 gap-10">
      <img
        :src="ContactUs"
        alt="University campus"
        class="w-full h-[300px] lg:h-screen object-cover"
      />
      <div class="flex flex-col justify-center mx-10 mb-10 lg:mb-0 lg:mx-0 lg:mr-10 gap-10">
        <h3 class="text-3xl font-bold text-center">Contact Us</h3>

        <div
          v-if="isSubmitted"
          class="bg-green-50 border border-green-200 rounded-md p-6 text-center"
        >
          <h4 class="text-xl font-semibold text-green-700 mb-2">Thank You!</h4>
          <p class="text-green-600">
            Your message has been received. We'll get back to you shortly.
          </p>
        </div>

        <form @submit.prevent="onSubmit" class="space-y-4 pt-3" v-else>
          <FormElement layout="row">
            <template #label>
              <Label for="visitor_name">Name</Label>
            </template>
            <template #field>
              <Input
                name="visitor_name"
                id="visitor_name"
                placeholder="Enter your name"
                :errors="errors"
              />
            </template>
          </FormElement>

          <FormElement layout="row">
            <template #label>
              <Label for="visitor_email">Email</Label>
            </template>
            <template #field>
              <Input
                name="visitor_email"
                id="visitor_email"
                placeholder="Enter your email"
                :errors="errors"
              />
            </template>
          </FormElement>

          <FormElement layout="row">
            <template #label>
              <Label for="title">Title</Label>
            </template>
            <template #field>
              <Input name="title" id="title" placeholder="Enter message title" :errors="errors" />
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
                placeholder="Enter your message"
                :errors="errors"
                as="textarea"
                class="min-h-[120px]"
              />
            </template>
          </FormElement>

          <FormElement layout="row">
            <template #label> </template>
            <template #field>
              <Button type="submit" :disabled="isSubmitting" class="w-full">
                {{ isSubmitting ? 'Submitting...' : 'Submit' }}
              </Button>
            </template>
          </FormElement>
        </form>
      </div>
    </div>
    <PublicFooter />
  </div>
</template>
