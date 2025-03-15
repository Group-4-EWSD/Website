<script setup lang="ts">
import { useForm } from 'vee-validate'
import { ref, onMounted } from 'vue'
import { useUserStore } from '@/stores/user'

import FormElement from '@/components/shared/FormElement.vue'
import Input from '@/components/shared/Input.vue'
import Select from '@/components/shared/Select.vue'
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
import type { User, UserDetailsParams } from '@/types/user'
import * as yup from 'yup'
import { updateUserDetail } from '@/api/user'
import { toast } from 'vue-sonner'

// Create a ref to control the dialog open state
const dialogOpen = ref(false)
const isLoading = ref(false)

// Get user store
const userStore = useUserStore()

// Gender options mapping
const genderOptions = [
  { label: 'Male', value: 1 },
  { label: 'Female', value: 2 },
  { label: 'Prefer not to say', value: 0 },
]

// Helper function to calculate age from date of birth
const calculateAge = (birthDate: string): number => {
  const today = new Date()
  const dob = new Date(birthDate)
  let age = today.getFullYear() - dob.getFullYear()
  const monthDiff = today.getMonth() - dob.getMonth()

  // If birth month is later in the year or same month but birth day is later, subtract one year
  if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < dob.getDate())) {
    age--
  }

  return age
}

// Define the form schema
const schema = yup.object({
  user_name: yup.string().required('Name is required'),
  nickname: yup.string().required('Nickname is required'),
  date_of_birth: yup
    .string()
    .required('Date of birth is required')
    .matches(/^\d{4}-\d{2}-\d{2}$/, 'Date of birth must be in the format YYYY-MM-DD')
    .test('valid-date', 'Invalid date', (value) => {
      if (!value) return false
      const date = new Date(value)
      return date instanceof Date && !isNaN(date.getTime())
    })
    .test('age-min', 'You must be at least 16 years old', (value) => {
      if (!value) return false
      const age = calculateAge(value)
      return age >= 16
    })
    .test('age-max', 'Age cannot be greater than 100 years', (value) => {
      if (!value) return false
      const age = calculateAge(value)
      return age <= 100
    }),
  gender: yup
    .string()
    .oneOf(['0', '1', '2'], 'Please select a valid gender')
    .required('Gender is required'),
  phone_number: yup
    .string()
    .matches(/^\+?[0-9\s\-()]+$/, 'Invalid phone number format')
    .required('Phone number is required'),
  user_email: yup.string().email('Invalid email format').required('Email is required'),
  faculty_name: yup.string(),
})

// Use vee-validate for form handling
const { handleSubmit, errors, setValues, resetForm, values } = useForm<User>({
  validationSchema: schema,
})

// Initialize form with user data
onMounted(() => {
  if (userStore.currentUser) {
    initializeForm()
  }
})

const initializeForm = () => {
  if (userStore.currentUser) {
    // Set all form values
    setValues({
      ...userStore.currentUser,
      gender: userStore.currentUser.gender ?? 0,
    })
  }

  console.log('Form initialized with:', values.gender)
}

// Function to submit the form
const onSubmit = handleSubmit(async (formValues: User) => {
  try {
    isLoading.value = true

    // Ensure we don't modify the email and faculty_id
    const userData: UserDetailsParams = {
      user_name: formValues.user_name,
      nickname: formValues.nickname,
      date_of_birth: formValues.date_of_birth,
      phone_number: formValues.phone_number,
    }

    const resp: User = await updateUserDetail(userData)

    // Update the store with the new data
    userStore.setUser({
      ...userStore.currentUser,
      user_name: resp.user_name,
      nickname: resp.nickname,
      date_of_birth: resp.date_of_birth,
      phone_number: resp.phone_number,
    } as User)

    toast.success('Information updated successfully')

    // Close the dialog after successful submission
    closeModal()
  } catch (error) {
    console.error('Error updating information:', error)
    toast.error('Failed to update information. Please try again.')
  } finally {
    isLoading.value = false
  }
})

// Open modal and initialize form with current user data
const openModal = () => {
  initializeForm()
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
        <DialogTitle class="uppercase">Edit Personal Information</DialogTitle>
        <DialogDescription>
          Make changes to your personal information here. Click save when you're done.
        </DialogDescription>
      </DialogHeader>
      <form @submit.prevent="onSubmit" class="space-y-4 pt-5">
        <FormElement :errors="errors" layout="row">
          <template #label>
            <Label for="user_name">Name</Label>
          </template>
          <template #field>
            <Input
              name="user_name"
              id="user_name"
              placeholder="Enter your full name"
              :errors="errors"
            />
          </template>
        </FormElement>

        <FormElement :errors="errors" layout="row">
          <template #label>
            <Label for="nickname">Nick Name</Label>
          </template>
          <template #field>
            <Input
              name="nickname"
              id="nickname"
              placeholder="Enter your nickname"
              :errors="errors"
            />
          </template>
        </FormElement>

        <FormElement :errors="errors" layout="row">
          <template #label>
            <Label for="user_email">Email</Label>
          </template>
          <template #field>
            <Input
              name="user_email"
              id="user_email"
              placeholder="Enter your email"
              :errors="errors"
              disabled
              :modelValue="userStore.currentUser?.user_email || ''"
            />
          </template>
        </FormElement>

        <FormElement :errors="errors" layout="row">
          <template #label>
            <Label for="faculty_name">Faculty</Label>
          </template>
          <template #field>
            <Input
              name="faculty_name"
              id="faculty_name"
              placeholder="Your faculty"
              :errors="errors"
              disabled
              :modelValue="userStore.currentUser?.faculty_name || ''"
            />
          </template>
        </FormElement>

        <FormElement :errors="errors" layout="row">
          <template #label>
            <Label for="date_of_birth">Date of Birth</Label>
          </template>
          <template #field>
            <Input name="date_of_birth" id="date_of_birth" type="date" :errors="errors" />
          </template>
        </FormElement>

        <FormElement :errors="errors" layout="row">
          <template #label>
            <Label for="gender">Gender</Label>
          </template>
          <template #field>
            <Select
              name="gender"
              id="gender"
              :errors="errors"
              :options="genderOptions"
              :modelValue="values.gender"
            ></Select>
          </template>
        </FormElement>

        <FormElement :errors="errors" layout="row">
          <template #label>
            <Label for="phone_number">Phone Number</Label>
          </template>
          <template #field>
            <Input
              name="phone_number"
              id="phone_number"
              placeholder="Enter your phone number"
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
          {{ isLoading ? 'Saving...' : 'Save' }}
        </Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
</template>
