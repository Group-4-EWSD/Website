<script setup lang="ts">
import { useForm } from 'vee-validate'
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import { toast } from 'vue-sonner'
import * as yup from 'yup'

import { login } from '@/api/auth'
import Logo from '@/assets/logo.png'
import AuthBaseLayout from '@/components/shared/AuthBaseLayout.vue'
import FormElement from '@/components/shared/FormElement.vue'
import Input from '@/components/shared/Input.vue'
import { Button } from '@/components/ui/button'
import { Label } from '@/components/ui/label'
import { setCookie } from '@/lib/utils'
import { useUserStore } from '@/stores/user'
import type { Credentials } from '@/types/auth'
import type { User } from '@/types/user'

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
        setCookie('token', response.data.token)

        const userStore = useUserStore()

        const user = response.data.user as User;
        userStore.setUser(user)

        const userRole = user.user_type_name

        // Role-based redirection
        switch (userRole) {
          case 'Student':
            router.push('/student/home')
            break
          case 'Guest':
            router.push('/guest/home')
            break
          case 'Marketing Coordinator':
            router.push('/coordinator/dashboard')
            break
          case 'Admin':
            router.push('/admin/management')
            break
          case 'Marketing Manager':
            router.push('/manager/dashboard')
            break
          default:
            router.push('/')
        }
      }
      loading.value = false
    })
    .catch((error) => {
      loading.value = false
      toast.error(error.response.data.message)
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
      <router-link to="/auth/forgot-password" class="text-primary hover:underline"
        >Forgot password?</router-link
      >
    </div>
  </AuthBaseLayout>
</template>
