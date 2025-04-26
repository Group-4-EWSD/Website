<script setup lang="ts">
import { useForm } from 'vee-validate'
import { ref } from 'vue'
import { toast } from 'vue-sonner'
import * as yup from 'yup'

import { updateUserPassword } from '@/api/user'
import FormElement from '@/components/shared/FormElement.vue'
import Input from '@/components/shared/Input.vue'
import { Button } from '@/components/ui/button'
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
  DialogTrigger,
} from '@/components/ui/dialog'
import { Label } from '@/components/ui/label'
import { useUserStore } from '@/stores/user'

// Create a ref to control the dialog open state
const dialogOpen = ref(false)
const isLoading = ref(false)

// Get user store
const userStore = useUserStore()

// Define the form schema with password validation
const schema = yup.object({
  user_password: yup
    .string()
    .required('Password is required')
    .min(8, 'Password must be at least 8 characters')
    .matches(
      /^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]/,
      'Password must contain at least one uppercase letter, one lowercase letter, one number, and one special character',
    ),
  user_password_confirmation: yup
    .string()
    .required('Confirm password is required')
    .oneOf([yup.ref('user_password')], 'Passwords must match'),
})

// Interface for password form values
interface PasswordForm {
  user_password: string
  user_password_confirmation: string
}

// Use vee-validate for form handling
const { handleSubmit, errors, resetForm } = useForm<PasswordForm>({
  validationSchema: schema,
})

// Function to submit the form
const onSubmit = handleSubmit(async (formValues: PasswordForm) => {
  try {
    isLoading.value = true

    // Only send the new password to the API
    await updateUserPassword({
      user_password: formValues.user_password,
      user_password_confirmation: formValues.user_password_confirmation
    })

    toast.success('Password updated successfully')

    // Close the dialog after successful submission
    closeModal()
    resetForm()
  } catch (error) {
    console.error('Error updating password:', error)
    toast.error('Failed to update password. Please try again.')
  } finally {
    isLoading.value = false
  }
})

// Function to open the modal
const openModal = () => {
  resetForm()
  dialogOpen.value = true
}

// Function to close the modal
const closeModal = () => {
  dialogOpen.value = false
}
</script>

<template>
  <Dialog v-model:open="dialogOpen">
    <DialogTrigger as="div" class="cursor-pointer" @click="openModal">
      <slot name="trigger"> </slot>
    </DialogTrigger>
    <DialogContent class="sm:max-w-[650px]">
      <DialogHeader>
        <DialogTitle class="uppercase">Change Password</DialogTitle>
        <DialogDescription>
          Enter a new password below. Your password should be at least 8 characters and include
          uppercase, lowercase, number, and special characters.
        </DialogDescription>
      </DialogHeader>
      <form @submit.prevent="onSubmit" class="space-y-4 pt-5">
        <FormElement :errors="errors" layout="row">
          <template #label>
            <Label for="user_password">New Password</Label>
          </template>
          <template #field>
            <Input
              name="user_password"
              id="user_password"
              type="password"
              placeholder="Enter your new password"
              :errors="errors"
            />
          </template>
        </FormElement>

        <FormElement :errors="errors" layout="row">
          <template #label>
            <Label for="user_password_confirmation">Confirm Password</Label>
          </template>
          <template #field>
            <Input
              name="user_password_confirmation"
              id="user_password_confirmation"
              type="password"
              placeholder="Confirm your new password"
              :errors="errors"
            />
          </template>
        </FormElement>
      </form>
      <DialogFooter>
        <Button type="button" variant="ghost" @click="closeModal" :disabled="isLoading"
          >Cancel</Button
        >
        <Button type="submit" @click="onSubmit" :processing="isLoading">
          {{ isLoading ? 'Updating...' : 'Update Password' }}
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
