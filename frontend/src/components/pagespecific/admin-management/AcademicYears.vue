<script setup lang="ts">
import { PencilIcon, PlusIcon } from 'lucide-vue-next'
import { ref } from 'vue'
import { useForm } from 'vee-validate'
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
import type {
  AcademicYear,
  AcademicYearParams,
  AcademicYearUpdateParams,
} from '@/types/academic-years'
import type { Update } from 'vite/types/hmrPayload.js'
import { toast } from 'vue-sonner'
import { createAcademicYear, updateAcademicYear } from '@/api/academic-years'

// Props and emits
const props = defineProps<{
  academicYears: AcademicYear[]
}>()

const emit = defineEmits<{
  (e: 'refresh'): void
}>()

// Local state
const isModalOpen = ref<boolean>(false)
const editing = ref<AcademicYear | null>(null)
const isSubmitting = ref<boolean>(false)

// Form validation schema
const schema = yup.object({
  academic_year_start: yup
    .string()
    .required('Start year is required')
    .matches(/^\d{4}$/, 'Must be a 4-digit year')
    .test('is-valid-year', 'Must be a valid year', (value) => {
      const year = parseInt(value)
      return year >= 2000 && year <= 2100
    }),
  academic_year_end: yup
    .string()
    .required('End year is required')
    .matches(/^\d{4}$/, 'Must be a 4-digit year')
    .test('is-valid-year', 'Must be a valid year', (value) => {
      const year = parseInt(value)
      return year >= 2000 && year <= 2100
    })
    .test('is-sequential', 'End year must follow start year', function (value) {
      const startYear = parseInt(this.parent.academic_year_start)
      const endYear = parseInt(value)
      return !isNaN(startYear) && !isNaN(endYear) && endYear === startYear + 1
    }),
})

// Methods
const openModal = (year: AcademicYear | null): void => {
  if (year) {
    editing.value = year
    const parts = year.academic_year_description.split('-')

    setFieldValue('id', year.academic_year_id)
    setFieldValue('academic_year_start', parts[0])
    setFieldValue('academic_year_end', parts[1])

    // resetForm({
    //   id: year.academic_year_id,
    //   academic_year_start: parts[0],
    //   academic_year_end: parts[1],
    // })
  } else {
    editing.value = null
    resetForm({
      id: null,
      academic_year_start: '',
      academic_year_end: '',
    })
  }
  isModalOpen.value = true
}

const { handleSubmit, resetForm, values, setFieldValue, errors } = useForm({
  validationSchema: schema,
  initialValues: {
    id: null as string | null,
    academic_year_start: '',
    academic_year_end: '',
  },
})

const saveAcademicYear = handleSubmit(async (values) => {
  if (editing.value && values.id !== null) {
    isSubmitting.value = true
    try {
      const params: AcademicYearUpdateParams = {
        academic_year_id: values.id,
        academic_year_start: values.academic_year_start,
        academic_year_end: values.academic_year_end,
      }
      await updateAcademicYear(params)
      emit('refresh')
      toast.success('Academic year updated successfully')
      isModalOpen.value = false
    } catch (error) {
      toast.error('Failed to update academic year')
    } finally {
      isSubmitting.value = false
    }
  } else {
    isSubmitting.value = true
    const params: AcademicYearParams = {
      academic_year_start: values.academic_year_start,
      academic_year_end: values.academic_year_end,
    }
    try {
      await createAcademicYear(params)
      emit('refresh')
      toast.success('Academic year added successfully')
      isModalOpen.value = false
    } catch (error) {
      toast.error('Failed to add academic year')
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
        <h3 class="text-lg font-medium hidden md:block">Current Academic Years</h3>
        <Button @click="openModal(null)" class="w-full sm:w-auto">
          <PlusIcon class="mr-2 h-4 w-4" /> Add Academic Year
        </Button>
      </div>

      <!-- Academic Years Table -->
      <div class="rounded-md border overflow-x-auto">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead class="w-16">No.</TableHead>
              <TableHead>Name</TableHead>
              <TableHead class="w-24 text-right">Actions</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow v-for="(year, index) in academicYears" :key="year.academic_year_id">
              <TableCell class="font-medium">{{ index + 1 }}</TableCell>
              <TableCell>{{ year.academic_year_description }}</TableCell>
              <TableCell class="text-right">
                <Button variant="ghost" size="icon" @click="openModal(year)" class="h-8 w-8">
                  <PencilIcon class="h-4 w-4" />
                </Button>
                <!-- <Button variant="ghost" size="icon" @click="confirmDelete(year)" class="h-8 w-8">
                    <TrashIcon class="h-4 w-4" />
                  </Button> -->
              </TableCell>
            </TableRow>
            <TableRow v-if="academicYears.length === 0">
              <TableCell colspan="3" class="text-center py-4 md:py-6 text-gray-500">
                No academic years found. Add one to get started.
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>
    </CardContent>
  </Card>

  <!-- Academic Year Modal -->
  <Dialog :open="isModalOpen" @update:open="isModalOpen = $event">
    <DialogContent class="w-[90vw] max-w-[425px]">
      <DialogHeader>
        <DialogTitle>{{ editing ? 'Edit' : 'Add' }} Academic Year</DialogTitle>
        <DialogDescription>Enter the details for the academic year</DialogDescription>
      </DialogHeader>

      <form @submit.prevent="saveAcademicYear" class="grid gap-4 pt-4">
        <div class="grid gap-2">
          <Label for="yearName">Academic Year</Label>
          <div class="flex gap-2 flex-row">
            <div>
              <Input
                name="academic_year_start"
                id="academic_year_start"
                placeholder="e.g. 2025"
                :errors="errors"
              />
            </div>
            <span class="text-gray-500 mt-2">-</span>
            <div>
              <Input
                name="academic_year_end"
                id="academic_year_end"
                placeholder="e.g. 2025"
                :errors="errors"
              />
            </div>
          </div>
        </div>

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
