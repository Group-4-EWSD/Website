<script setup lang="ts">
import { PencilIcon, PlusIcon, TrashIcon } from 'lucide-vue-next'
import { ref } from 'vue'
import { ErrorMessage, Field, useForm } from 'vee-validate'
import * as yup from 'yup'
import { Button } from '@/components/ui/button'
import { Card, CardContent } from '@/components/ui/card'
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog'
import Input from '@/components/shared/Input.vue'
import { Label } from '@/components/ui/label'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import type { Faculty, FacultyParams, FacultyUpdateParams } from '@/types/faculty'
import FormElement from '@/components/shared/FormElement.vue'
import { createFaculty, updateFaculty } from '@/api/faculties'
import { toast } from 'vue-sonner'

// Props and emits
const props = defineProps<{
  faculties: Faculty[]
}>()

const emit = defineEmits<{
  (e: 'refresh'): void
}>()

// Local state
const isModalOpen = ref<boolean>(false)
const editing = ref<Faculty | null>(null)
const formId = ref<string | null>(null)
const isSubmitting = ref<boolean>(false)

// Form validation schema
const schema = yup.object({
  faculty_name: yup
    .string()
    .required('Faculty name is required')
    .min(2, 'Faculty name must be at least 2 characters')
    .max(100, 'Faculty name must be less than 100 characters'),
  remark: yup.string().max(500, 'Remark must be less than 500 characters'),
})

const { handleSubmit, resetForm, setValues, errors } = useForm({
  validationSchema: schema,
  initialValues: {
    faculty_name: '',
    remark: '',
  },
})

// Methods
const openModal = (faculty: Faculty | null): void => {
  if (faculty) {
    editing.value = faculty
    formId.value = faculty.faculty_id

    // Use setValues to populate the form fields
    setValues({
      faculty_name: faculty.faculty_name,
      remark: faculty.remark,
    })
  } else {
    editing.value = null
    formId.value = null
    resetForm()
  }
  isModalOpen.value = true
}

const saveFaculty = handleSubmit(async (values) => {
  if (editing.value && formId.value) {
    isSubmitting.value = true
    try {
      const params: FacultyUpdateParams = {
        faculty_id: formId.value,
        faculty_name: values.faculty_name,
        remark: values.remark,
      }
      await updateFaculty(params)
      emit('refresh')
      toast.success('Faculty updated successfully')
      isModalOpen.value = false
    } catch (error) {
      toast.error('Failed to update faculty')
    } finally {
      isSubmitting.value = false
    }
  } else {
    isSubmitting.value = true
    try {
      const params: FacultyParams = {
        faculty_name: values.faculty_name,
        remark: values.remark,
      }
      await createFaculty(params)
      emit('refresh')
      toast.success('Faculty created successfully')
      isModalOpen.value = false
    } catch (error) {
      toast.error('Failed to update faculty')
    } finally {
      isSubmitting.value = false
    }
  }
})
</script>
<template>
  <Card>
    <CardContent class="pt-6">
      <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4">
        <h3 class="text-lg font-medium hidden md:block">Current Faculties</h3>
        <Button @click="openModal(null)" class="w-full sm:w-auto">
          <PlusIcon class="mr-2 h-4 w-4" /> Add Faculty
        </Button>
      </div>

      <!-- Faculties Table -->
      <div class="rounded-md border overflow-x-auto">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead class="w-16">No.</TableHead>
              <TableHead>Name</TableHead>
              <TableHead>Remark</TableHead>
              <TableHead class="w-24 text-right">Actions</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow v-for="(faculty, index) in faculties" :key="faculty.faculty_id">
              <TableCell class="font-medium">{{ index + 1 }}</TableCell>
              <TableCell>{{ faculty.faculty_name }}</TableCell>
              <TableCell>{{ faculty.remark }}</TableCell>
              <TableCell class="text-right">
                <Button variant="ghost" size="icon" @click="openModal(faculty)" class="h-8 w-8">
                  <PencilIcon class="h-4 w-4" />
                </Button>
              </TableCell>
            </TableRow>
            <TableRow v-if="faculties.length === 0">
              <TableCell colspan="4" class="text-center py-4 md:py-6 text-gray-500">
                No faculties found. Add one to get started.
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>
    </CardContent>
  </Card>

  <!-- Faculty Modal -->
  <Dialog :open="isModalOpen" @update:open="isModalOpen = $event">
    <DialogContent class="w-[90vw] max-w-[425px]">
      <DialogHeader>
        <DialogTitle>{{ editing ? 'Edit' : 'Add' }} Faculty</DialogTitle>
        <DialogDescription>Enter the details for the faculty</DialogDescription>
      </DialogHeader>

      <form @submit.prevent="saveFaculty" class="grid gap-4 pt-2">
        <FormElement>
          <template #label>
            <Label for="faculty_name">Faculty Name</Label>
          </template>
          <template #field>
            <Input
              name="faculty_name"
              id="faculty_name"
              placeholder="Enter faculty name"
              :errors="errors"
            />
          </template>
        </FormElement>

        <FormElement>
          <template #label>
            <Label for="remark">Remark</Label>
          </template>
          <template #field>
            <Input
              name="remark"
              id="remark"
              as="textarea"
              placeholder="Enter remark"
              :errors="errors"
              class="min-h-[120px]"
            />
          </template>
        </FormElement>

        <DialogFooter class="flex flex-col sm:flex-row gap-2 mt-4">
          <Button
            type="button"
            variant="outline"
            @click="isModalOpen = false"
            class="w-full sm:w-auto"
            >Cancel</Button
          >
          <Button type="submit" class="w-full sm:w-auto" :processing="isSubmitting">
            {{ isSubmitting ? 'Saving...' : 'Save' }}
          </Button>
        </DialogFooter>
      </form>
    </DialogContent>
  </Dialog>
</template>
