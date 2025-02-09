<script setup lang="ts">
import { useForm } from 'vee-validate'
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import * as yup from 'yup'

import { register } from '@/api/auth'
import Logo from '@/assets/logo.png'
import AuthBaseLayout from '@/components/shared/AuthBaseLayout.vue'
import FormElement from '@/components/shared/FormElement.vue'
import Input from '@/components/shared/Input.vue'
import Button from '@/components/ui/button/Button.vue'
import { Label } from '@/components/ui/label'
import type { RegisterData } from '@/types/auth'

const router = useRouter()
const loading = ref(false)

const schema = yup.object({
  name: yup.string().required('Name is required'),
  email: yup.string().email('Invalid email').required('Email is required'),
  password: yup
    .string()
    .min(6, 'Password must be at least 6 characters')
    .required('Password is required'),
  confirmPassword: yup
    .string()
    .oneOf([yup.ref('password')], 'Passwords must match')
    .required('Confirm password is required'),
})

const { handleSubmit, errors } = useForm<RegisterData>({
  validationSchema: schema,
})

const onSubmit = handleSubmit(async (values: RegisterData) => {
  loading.value = true
  try {
    await register(values)
    router.push('/login')
  } catch (error) {
    console.error('Registration failed:', error)
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <AuthBaseLayout>
    <div class="h-40 w-40 mx-auto md:mx-0">
      <img :src="Logo" alt="Aurora University'g Logo" class="object-cover h-full w-full" />
    </div>
    <h1 class="text-3xl text-center md:text-left">Welcome to AURORA University</h1>
    <form @submit="onSubmit" class="space-y-4">
      <FormElement :errors="errors">
        <template #label>
          <Label form="email">Email</Label>
        </template>
        <template #field>
          <Input name="email" id="email" placeholder="Enter your email" :errors="errors" />
        </template>
      </FormElement>
      <FormElement :errors="errors">
        <template #label>
          <Label form="password">Password</Label>
        </template>
        <template #field>
          <Input
            name="password"
            id="password"
            placeholder="Enter your password"
            type="password"
            :errors="errors"
          />
        </template>
      </FormElement>

      <FormElement :errors="errors">
        <template #label>
          <Label form="confirmPassword">Confirm Password</Label>
        </template>
        <template #field>
          <Input
            name="confirmPassword"
            id="confirmPassword"
            placeholder="Retype your password"
            type="password"
            :errors="errors"
          />
        </template>
      </FormElement>

      <Button type="submit" :disabled="loading" class="w-full">
        {{ loading ? 'Loading...' : 'Register' }}
      </Button>
    </form>
    <div class="flex justify-center text-sm">
      Alreay have an account?
      <router-link to="/auth/login" class="text-blue-700 ml-2">Log in?</router-link>
    </div>
  </AuthBaseLayout>
</template>
