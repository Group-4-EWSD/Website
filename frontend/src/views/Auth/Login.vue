<script setup lang="ts">
import { ref } from 'vue'
import { useForm } from 'vee-validate'
import * as yup from 'yup'
import FormElement from '@/components/shared/FormElement.vue'
import Button from '@/components/ui/button/Button.vue'
import SideImage from '@/assets/login-side-image.webp'

const loading = ref(false)

const schema = yup.object({
  email: yup.string().email('Invalid email').required('Please enter your email'),
  password: yup
    .string()
    .required('Please enter your password')
    .min(8, 'Password must be at least 8 characters.'),
})

interface loginForm {
  email: string
  password: string
}

const { handleSubmit, errors } = useForm<loginForm>({
  validationSchema: schema,
})

const onSubmit = handleSubmit(async (values: loginForm) => {
  loading.value = true
  try {
    console.log('Login data:', values)
  } catch (error) {
    console.error('Login failed:', error)
  } finally {
    loading.value = false
  }
})
</script>

<template>
  <div class="grid grid-cols-1 md:grid-cols-2 h-screen">
    <div class="w-full h-full hidden md:block">
      <img :src="SideImage" alt="Aurora University" class="object-cover h-full w-full" />
    </div>
    <div class="flex items-center justify-center">
      <div class="w-full max-w-lg px-4 flex flex-col gap-4">
        <h1 class="text-3xl">Login</h1>
        <form @submit="onSubmit" class="space-y-4">
          <FormElement
            name="email"
            label="Email"
            id="email"
            placeholder="Enter your email"
            :errors="errors"
          />
          <FormElement
            name="password"
            label="Password"
            id="password"
            placeholder="Enter your password"
            :errors="errors"
          />

          <Button type="submit" :disabled="loading" class="w-full">
            {{ loading ? 'Loading...' : 'Login' }}
          </Button>
        </form>
        <p>
          Forgot password? <router-link to="/auth/forgot-password" class="text-blue-700">Reset password</router-link>
        </p>
      </div>
    </div>
  </div>
</template>