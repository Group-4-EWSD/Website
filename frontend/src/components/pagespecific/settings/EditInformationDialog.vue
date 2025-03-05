<script setup lang="ts">
import { useForm } from 'vee-validate'
import { defineEmits, defineProps, ref, watch } from 'vue'


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
import type { User } from '@/types/user'

const props = defineProps({
  isOpen: {
    type: Boolean,
    default: false,
  },
  initialData: {
    type: Object as () => User,
  },
})

const emit = defineEmits(['update:isOpen', 'save'])

// Create a ref to control the dialog open state
const dialogOpen = ref(props.isOpen)
const isLoading = ref(false)

// Watch for changes in the isOpen prop and update the local state
watch(
  () => props.isOpen,
  (newValue) => {
    console.log('Dialog open:', props)
    dialogOpen.value = newValue

    // When dialog opens, reset form with current initialData
    if (newValue) {
      setValues({ ...props.initialData })
    }
  },
)

// Use vee-validate for form handling
const { handleSubmit, errors, setValues } = useForm<User>({
  initialValues: { ...props.initialData },
})

// Function to submit the form
const onSubmit = handleSubmit(async (formValues: User) => {
  try {
    isLoading.value = true

    // Here you would typically make an API call to update user data
    // Simulate API call with timeout
    await new Promise((resolve) => setTimeout(resolve, 1000))

    // Emit the save event with updated values
    emit('save', formValues)

    // Close the dialog after successful submission
    closeModal()
  } catch (error) {
    console.error('Error updating information:', error)
    // Handle error (show error notification, etc.)
  } finally {
    isLoading.value = false
  }
})

// Function to close the modal
const closeModal = () => {
  dialogOpen.value = false
  emit('update:isOpen', false)
  resetForm()
}

// Function to reset the form
const resetForm = () => {
  setValues({ ...props.initialData })
}
</script>

<template>
  <Dialog v-model:open="dialogOpen">
    <DialogTrigger as="div" class="cursor-pointer">
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
            <Label for="name">Name</Label>
          </template>
          <template #field>
            <Input name="name" id="name" placeholder="Enter your full name" :errors="errors" />
          </template>
        </FormElement>

        <FormElement :errors="errors" layout="row">
          <template #label>
            <Label for="nickName">Nick Name</Label>
          </template>
          <template #field>
            <Input
              name="nickName"
              id="nickName"
              placeholder="Enter your nickname"
              :errors="errors"
            />
          </template>
        </FormElement>

        <FormElement :errors="errors" layout="row">
          <template #label>
            <Label for="email">Email</Label>
          </template>
          <template #field>
            <Input name="email" id="email" placeholder="Enter your email" :errors="errors" />
          </template>
        </FormElement>

        <FormElement :errors="errors" layout="row">
          <template #label>
            <Label for="faculty">Faculty</Label>
          </template>
          <template #field>
            <Input name="faculty" id="faculty" placeholder="Enter your faculty" :errors="errors" />
          </template>
        </FormElement>

        <FormElement :errors="errors" layout="row">
          <template #label>
            <Label for="dateOfBirth">Date of Birth</Label>
          </template>
          <template #field>
            <Input name="dateOfBirth" id="dateOfBirth" type="date" :errors="errors" />
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
              :options="[
                { label: 'Female', value: 'Female' },
                { label: 'Male', value: 'Male' },
                { label: 'Other', value: 'Other' },
                { label: 'Prefer not to say', value: 'Prefer not to say' },
              ]"
            />
          </template>
        </FormElement>

        <FormElement :errors="errors" layout="row">
          <template #label>
            <Label for="phoneNumber">Phone Number</Label>
          </template>
          <template #field>
            <Input
              name="phoneNumber"
              id="phoneNumber"
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
