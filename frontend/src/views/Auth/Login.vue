<script setup lang="ts">
import { useForm } from 'vee-validate'
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { useCookies } from 'vue3-cookies'
import * as yup from 'yup'

import Logo from '@/assets/logo.png'
import AuthBaseLayout from '@/components/shared/AuthBaseLayout.vue'
import FormElement from '@/components/shared/FormElement.vue'
import Input from '@/components/shared/Input.vue'
import { Button } from '@/components/ui/button'
import { Label } from '@/components/ui/label'

const router = useRouter()
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
    const { cookies } = useCookies()
    const token = cookies.set('token', 'token1234')

    // go to home
    if (token) {
      router.replace('/')
    }
  } catch (error) {
    console.error('Login failed:', error)
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
          <Input name="email" id="email" placeholder="Enter your email" :errors="errors"/>
        </template>
      </FormElement>
      <FormElement>
        <template #label>
          <Label form="password">Password</Label>
        </template>
        <template #field>
          <Input name="password" id="password" placeholder="Enter your password" type="password" :errors="errors"/>
        </template>
      </FormElement>

      <Button type="submit" :disabled="loading" class="w-full">
        {{ loading ? 'Loading...' : 'Login' }}
      </Button>
    </form>
    <div class="flex justify-between text-sm">
      <router-link to="/auth/forgot-password" class="text-blue-700">Forgot password?</router-link>
      <router-link to="/auth/register" class="text-blue-700">Register</router-link>
    </div>
  </AuthBaseLayout>
</template>
