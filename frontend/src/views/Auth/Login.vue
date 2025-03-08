<script setup lang="ts">
import { useForm } from 'vee-validate'
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { toast } from 'vue-sonner'
import { useCookies } from 'vue3-cookies'
import * as yup from 'yup'

import { login } from '@/api/auth'
import Logo from '@/assets/logo.png'
import AuthBaseLayout from '@/components/shared/AuthBaseLayout.vue'
import FormElement from '@/components/shared/FormElement.vue'
import Input from '@/components/shared/Input.vue'
import { Button } from '@/components/ui/button'
import { Label } from '@/components/ui/label'
import type { Credentials } from '@/types/auth'

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

  login(values as Credentials)
    .then((response) => {

      console.log(response)

      if (!response.data) {
        toast.error('Invalid credentials')
      } else {
        const { cookies } = useCookies()
        cookies.set('token', response.data.token)

        // Redirect to the home page base on role
        router.push('/student/home')
      }
      loading.value = false
    })
    .catch((error) => {
      loading.value = false
      toast.error(`An error occurred: ${error.message}`)
    })
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
      <FormElement>
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

      <Button type="submit" :disabled="loading" :processing="loading" class="w-full">
        {{ loading ? 'Logging in...' : 'Login' }}
      </Button>
    </form>
    <div class="flex justify-end text-sm">
      <router-link to="/auth/forgot-password" class="text-primary hover:underline">Forgot password?</router-link>
    </div>
  </AuthBaseLayout>
</template>
