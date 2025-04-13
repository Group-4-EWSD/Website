<script setup lang="ts">
import dayjs from 'dayjs'
import { PencilIcon, PlusIcon, TrashIcon } from 'lucide-vue-next'
import { useField, useForm } from 'vee-validate'
import { computed, onMounted, reactive, ref } from 'vue'
import { toast } from 'vue-sonner'
import * as yup from 'yup'

import { getAcademicYearList } from '@/api/academic-years'
import { getFacultyList } from '@/api/faculties'
import { createSubmissionDate, getSubmissionDates, updateSubmissionDate } from '@/api/system-data'
import AcademicYears from '@/components/pagespecific/admin-management/AcademicYears.vue'
import Faculties from '@/components/pagespecific/admin-management/Faculties.vue'
import FormElement from '@/components/shared/FormElement.vue'
import Input from '@/components/shared/Input.vue'
import CustomSelect from '@/components/shared/Select.vue'
import {
  Accordion,
  AccordionContent,
  AccordionItem,
  AccordionTrigger,
} from '@/components/ui/accordion'
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
import { Label } from '@/components/ui/label'
import Layout from '@/components/ui/Layout.vue'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import { Skeleton } from '@/components/ui/skeleton'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import type { AcademicYear } from '@/types/academic-years'
import type { Faculty } from '@/types/faculty'
import type {
  SubmissionDate,
  SubmissionDateForm,
  SubmissionDateParams,
  SubmissionDateUpdateParams,
} from '@/types/system-data'

// Loading states
const isLoading = ref<boolean>(true)

// Data for academic years
const academicYears = ref<AcademicYear[]>([])

// Data for faculties
const faculties = ref<Faculty[]>([])

const submissionDates = ref<SubmissionDate[]>([])

// Default open accordion item
const openAccordionItems = ref(['submission-dates'])

const selectedAcademicYearFilter = ref<string>('')

// Form states
const isSubmissionModalOpen = ref<boolean>(false)
const editingSubmission = ref<SubmissionDate | null>(null)
const isSubmitting = ref<boolean>(false)


// Form validation schema
const validationSchema = yup.object({
  system_title: yup.string().required('System title is required'),
  faculty_id: yup.string().required('Faculty is required'),
  academic_year_id: yup.string().required('Academic year is required'),
  pre_submission_date: yup.string().required('Pre-submission date is required'),
  actual_submission_date: yup
    .string()
    .required('Actual submission date is required')
    .test(
      'is-after-pre-submission',
      'Actual submission date cannot be earlier than pre-submission date',
      function (value) {
        const { pre_submission_date } = this.parent
        if (!value || !pre_submission_date) return true // Skip validation if either date is missing
        return new Date(value) >= new Date(pre_submission_date)
      },
    ),
})

// Initialize form
const { handleSubmit, errors, resetForm, values, setValues } = useForm<SubmissionDateForm>({
  validationSchema,
  initialValues: {
    system_id: '',
    system_title: '',
    faculty_id: '',
    academic_year_id: '',
    pre_submission_date: '',
    actual_submission_date: '',
  },
})

const formatDate = (dateString: string): string => {
  if (!dateString) return 'Not set'
  try {
    return dayjs(new Date(dateString)).format('MMM DD, YYYY')
  } catch (e) {
    return dateString
  }
}

// Helper function to get the current academic year ID
function getCurrentAcademicYearId(): string {
  if (academicYears.value.length === 0) return ''
  const latestYear = academicYears.value.reduce((latest, year) => {
    return parseInt(year.academic_year_id) > parseInt(latest.academic_year_id) ? year : latest
  }, academicYears.value[0])
  return latestYear.academic_year_id
}

// Modal functions
const openSubmissionModal = (submission: SubmissionDate | null): void => {
  resetForm()

  if (submission) {
    editingSubmission.value = submission
    setValues({
      system_id: submission.system_id,
      system_title: submission.system_title,
      faculty_id: submission.faculty_id,
      academic_year_id: submission.academic_year_id,
      pre_submission_date: submission.pre_submission_date,
      actual_submission_date: submission.actual_submission_date,
    } as SubmissionDateForm)
  } else {
    editingSubmission.value = null
    setValues({
      system_id: '',
      system_title: '',
      faculty_id: '',
      academic_year_id: getCurrentAcademicYearId(),
      pre_submission_date: '',
      actual_submission_date: '',
    } as SubmissionDateForm)
  }

  isSubmissionModalOpen.value = true
}

// Submission dates functions
const saveSubmissionDate = handleSubmit(async (formValues) => {
  try {
    isSubmitting.value = true

    const submissionData: SubmissionDateParams = {
      system_title: formValues.system_title,
      faculty_id: formValues.faculty_id,
      academic_year_id: formValues.academic_year_id,
      pre_submission_date: formValues.pre_submission_date,
      actual_submission_date: formValues.actual_submission_date || '',
    }

    if (editingSubmission.value) {
      // Update existing submission date
      const updateData: SubmissionDateUpdateParams = {
        ...submissionData,
        updated_at: editingSubmission.value.updated_at,
        system_id: editingSubmission.value.system_id,
      }

      await updateSubmissionDate(updateData)
      toast.success('Submission date updated successfully')
    } else {
      // Add new submission date
      await createSubmissionDate(submissionData)
      toast.success('Submission date added successfully')
    }

    // Refresh submission dates
    await fetchSubmissionDates()
    isSubmissionModalOpen.value = false
  } catch (error) {
    toast.error(
      editingSubmission.value
        ? 'Failed to update submission date'
        : 'Failed to add submission date',
    )
    console.error(error)
  } finally {
    isSubmitting.value = false
  }
})

// Get filtered submission dates based on selected academic year
const filteredSubmissionDates = computed(() => {
  if (!selectedAcademicYearFilter.value) return submissionDates.value
  return submissionDates.value.filter(
    (date) => date.academic_year_id === selectedAcademicYearFilter.value,
  )
})

// Check if selected academic year is current (latest)
const isCurrentAcademicYear = computed(() => {
  return selectedAcademicYearFilter.value === getCurrentAcademicYearId()
})

const fetchSubmissionDates = async (): Promise<void> => {
  try {
    submissionDates.value = await getSubmissionDates()
  } catch (error) {
    toast.error('Failed to fetch submission dates')
    console.error(error)
  }
}

const refreshAcademicYears = async (): Promise<void> => {
  try {
    academicYears.value = await getAcademicYearList()
  } catch (error) {
    toast.error('Failed to refresh academic years')
    console.error(error)
  }
}

// Faculties CRUD operations
const refreshFaculties = async (): Promise<void> => {
  try {
    faculties.value = await getFacultyList()
  } catch (error) {
    toast.error('Failed to refresh faculties')
    console.error(error)
  }
}


onMounted(async () => {
  isLoading.value = true

  try {
    // Load all data in parallel
    const promises: Promise<any>[] = [getAcademicYearList(), getFacultyList(), getSubmissionDates()]

    const results = await Promise.all(promises)

    academicYears.value = results[0]
    faculties.value = results[1]
    submissionDates.value = results[2]

    // Set default academic year filter to current year
    selectedAcademicYearFilter.value = getCurrentAcademicYearId()
  } catch (error) {
    toast.error('Failed to load data')
    console.error(error)
  } finally {
    isLoading.value = false
  }
})
</script>

<template>
  <Layout>
    <h1 class="text-2xl font-bold mb-4">Academic Management</h1>

    <!-- Accordion for switching between different sections -->
    <div class="mb-8">
      <Accordion type="multiple" v-model="openAccordionItems" class="w-full">
        <!-- Submission Dates Accordion (Open by default) -->
        <AccordionItem value="submission-dates">
          <AccordionTrigger class="[&[data-state=open]]:text-secondary">
            <div class="flex flex-col gap-2 items-start justify-start">
              <h3 class="text-lg font-semibold">Submission Dates</h3>
              <p class="text-sm text-left">
                Manage pre-submission and actual submission dates for each academic year and faculty
              </p>
            </div>
          </AccordionTrigger>
          <AccordionContent>
            <Card class="mt-4">
              <CardContent class="pt-6">
                <!-- Loading skeleton -->
                <div v-if="isLoading">
                  <div class="flex flex-col sm:flex-row sm:justify-between mb-4 gap-4">
                    <div class="flex items-center gap-3">
                      <Skeleton class="h-4 w-24" />
                      <Skeleton class="h-10 w-32 md:w-40" />
                    </div>
                    <Skeleton class="h-10 w-full sm:w-32" />
                  </div>

                  <!-- Table skeleton for larger screens -->
                  <div class="rounded-md border hidden md:block">
                    <Table>
                      <TableHeader>
                        <TableRow>
                          <TableHead v-for="i in 5" :key="i"
                            ><Skeleton class="h-4 w-full"
                          /></TableHead>
                        </TableRow>
                      </TableHeader>
                      <TableBody>
                        <TableRow v-for="i in 3" :key="i">
                          <TableCell v-for="j in 5" :key="j"
                            ><Skeleton class="h-4 w-full"
                          /></TableCell>
                        </TableRow>
                      </TableBody>
                    </Table>
                  </div>

                  <!-- Mobile Card skeleton -->
                  <div class="md:hidden space-y-4">
                    <div v-for="i in 3" :key="i" class="p-4 border rounded-md shadow-sm">
                      <div class="flex justify-between items-start mb-2">
                        <Skeleton class="h-5 w-32" />
                        <div class="flex gap-1">
                          <Skeleton class="h-8 w-8" />
                          <Skeleton class="h-8 w-8" />
                        </div>
                      </div>
                      <Skeleton class="h-4 w-24 mt-2" />
                      <div class="grid grid-cols-2 gap-2 mt-3">
                        <div>
                          <Skeleton class="h-4 w-24 mb-1" />
                          <Skeleton class="h-4 w-20" />
                        </div>
                        <div>
                          <Skeleton class="h-4 w-24 mb-1" />
                          <Skeleton class="h-4 w-20" />
                        </div>
                      </div>
                    </div>
                  </div>
                </div>

                <!-- Actual content -->
                <div v-else>
                  <div class="flex flex-col sm:flex-row sm:justify-between mb-4 gap-4">
                    <div class="flex items-center gap-3">
                      <Label
                        for="academicYearFilter"
                        class="font-medium whitespace-nowrap text-sm md:text-base"
                        >Academic Year:</Label
                      >
                      <Select v-model="selectedAcademicYearFilter" class="w-32 md:w-40">
                        <SelectTrigger id="academicYearFilter">
                          <SelectValue placeholder="Select year" />
                        </SelectTrigger>
                        <SelectContent>
                          <SelectItem
                            v-for="year in academicYears"
                            :key="year.academic_year_id"
                            :value="year.academic_year_id"
                          >
                            {{ year.academic_year_description }}
                          </SelectItem>
                        </SelectContent>
                      </Select>
                    </div>

                    <Button @click="openSubmissionModal(null)" class="w-full sm:w-auto">
                      <PlusIcon class="mr-2 h-4 w-4" /> Add New Date
                    </Button>
                  </div>

                  <!-- Submission Dates Table for larger screens -->
                  <div class="rounded-md border hidden md:block">
                    <Table>
                      <TableHeader>
                        <TableRow>
                          <TableHead>Academic Year</TableHead>
                          <TableHead>Faculty</TableHead>
                          <TableHead>System Title</TableHead>
                          <TableHead>Pre-Submission Date</TableHead>
                          <TableHead>Actual Submission Date</TableHead>
                          <TableHead class="text-right">Actions</TableHead>
                        </TableRow>
                      </TableHeader>
                      <TableBody>
                        <TableRow v-for="date in filteredSubmissionDates" :key="date.system_id">
                          <TableCell>{{ date.academic_year_description }}</TableCell>
                          <TableCell>{{ date.faculty_name }}</TableCell>
                          <TableCell>{{ date.system_title }}</TableCell>
                          <TableCell>{{ formatDate(date.pre_submission_date) }}</TableCell>
                          <TableCell>{{ formatDate(date.actual_submission_date) }}</TableCell>
                          <TableCell class="text-right">
                            <!-- :disabled="date.academic_year_id !== getCurrentAcademicYearId()" -->
                            <Button variant="ghost" size="icon" @click="openSubmissionModal(date)">
                              <PencilIcon class="h-4 w-4" />
                            </Button>
                          </TableCell>
                        </TableRow>
                        <TableRow v-if="filteredSubmissionDates.length === 0">
                          <TableCell colspan="6" class="text-center py-6 text-gray-500">
                            No submission dates found for the selected academic year.
                          </TableCell>
                        </TableRow>
                      </TableBody>
                    </Table>
                  </div>

                  <!-- Mobile Card List View -->
                  <div class="md:hidden space-y-4">
                    <div
                      v-for="date in filteredSubmissionDates"
                      :key="date.system_id"
                      class="p-4 border rounded-md shadow-sm"
                    >
                      <div class="flex justify-between items-start mb-2">
                        <div class="font-medium">{{ date.faculty_name }}</div>
                        <div class="flex gap-1">
                          <Button
                            variant="ghost"
                            size="icon"
                            @click="openSubmissionModal(date)"
                            :disabled="date.academic_year_id !== getCurrentAcademicYearId()"
                            class="h-8 w-8"
                          >
                            <PencilIcon class="h-4 w-4" />
                          </Button>
                        </div>
                      </div>
                      <div class="text-sm text-gray-500">{{ date.academic_year_description }}</div>
                      <div class="text-sm font-medium mt-1">{{ date.system_title }}</div>
                      <div class="grid grid-cols-2 gap-2 mt-3 text-sm">
                        <div>
                          <div class="font-medium">Pre-Submission:</div>
                          <div>{{ formatDate(date.pre_submission_date) }}</div>
                        </div>
                        <div>
                          <div class="font-medium">Actual Submission:</div>
                          <div>{{ formatDate(date.actual_submission_date) }}</div>
                        </div>
                      </div>
                    </div>
                    <div
                      v-if="filteredSubmissionDates.length === 0"
                      class="text-center py-6 text-gray-500 border rounded-md"
                    >
                      No submission dates found for the selected academic year.
                    </div>
                  </div>
                </div>
              </CardContent>
            </Card>
          </AccordionContent>
        </AccordionItem>

        <!-- Academic Years Accordion -->
        <AccordionItem value="academic-years">
          <AccordionTrigger class="[&[data-state=open]]:text-secondary">
            <div class="flex flex-col gap-2 items-start">
              <h3 class="text-lg font-semibold">Academic Years</h3>
              <p class="text-sm">Manage academic years in the system</p>
            </div>
          </AccordionTrigger>
          <AccordionContent>
            <div class="mt-4">
              <!-- Skeleton loader for academic years -->
              <div v-if="isLoading" class="space-y-4">
                <Skeleton class="h-10 w-full sm:w-32 mb-4" />
                <Skeleton class="h-56 w-full rounded-md" />
              </div>
              <AcademicYears
                v-else
                :academicYears="academicYears"
                @success="refreshAcademicYears"
              />
            </div>
          </AccordionContent>
        </AccordionItem>

        <!-- Faculties Accordion -->
        <AccordionItem value="faculties">
          <AccordionTrigger class="[&[data-state=open]]:text-secondary">
            <div class="flex flex-col gap-2 items-start">
              <h3 class="text-lg font-semibold">Faculties</h3>
              <p class="text-sm">Manage faculties in the system</p>
            </div>
          </AccordionTrigger>
          <AccordionContent>
            <div class="mt-4">
              <!-- Skeleton loader for faculties -->
              <div v-if="isLoading" class="space-y-4">
                <Skeleton class="h-10 w-full sm:w-32 mb-4" />
                <Skeleton class="h-56 w-full rounded-md" />
              </div>
              <Faculties v-else :faculties="faculties" @refresh="refreshFaculties" />
            </div>
          </AccordionContent>
        </AccordionItem>
      </Accordion>
    </div>

    <!-- Submission Date Modal -->
    <Dialog :open="isSubmissionModalOpen" @update:open="isSubmissionModalOpen = $event">
      <DialogContent class="sm:max-w-[650px] w-[95vw] mx-auto">
        <DialogHeader>
          <DialogTitle>{{ editingSubmission ? 'Edit' : 'Add' }} Submission Date</DialogTitle>
          <DialogDescription>
            Set the pre-submission and actual submission dates for an academic year and faculty
          </DialogDescription>
        </DialogHeader>

        <form @submit="saveSubmissionDate" class="grid gap-4 py-4">
          <FormElement>
            <template #label>
              <Label for="system_title">System Title</Label>
            </template>
            <template #field>
              <Input
                name="system_title"
                id="system_title"
                placeholder="Enter system title"
                :errors="errors"
              />
            </template>
          </FormElement>

          <FormElement>
            <template #label>
              <Label for="academic_year_id">Academic Year</Label>
            </template>
            <template #field>
              <CustomSelect
                name="academic_year_id"
                id="academic_year_id"
                :errors="errors"
                :options="
                  academicYears.map((year) => ({
                    value: year.academic_year_id,
                    label: year.academic_year_description,
                  }))
                "
                :modelValue="values.academic_year_id"
              ></CustomSelect>
            </template>
          </FormElement>

          <FormElement>
            <template #label>
              <Label for="faculty_id">Faculty</Label>
            </template>
            <template #field>
              <CustomSelect
                name="faculty_id"
                id="faculty_id"
                :errors="errors"
                :options="
                  faculties.map((faculty) => ({
                    value: faculty.faculty_id,
                    label: faculty.faculty_name,
                  }))
                "
                :modelValue="values.faculty_id"
              ></CustomSelect>
            </template>
          </FormElement>

          <FormElement>
            <template #label>
              <Label for="pre_submission_date">Pre-Submission Date</Label>
            </template>
            <template #field>
              <Input
                name="pre_submission_date"
                id="pre_submission_date"
                type="date"
                :errors="errors"
              />
            </template>
          </FormElement>

          <FormElement>
            <template #label>
              <Label for="actual_submission_date">Actual Submission Date</Label>
            </template>
            <template #field>
              <Input
                name="actual_submission_date"
                id="actual_submission_date"
                type="date"
                :errors="errors"
              />
            </template>
          </FormElement>

          <DialogFooter class="flex-col sm:flex-row gap-2">
            <Button
              type="button"
              variant="outline"
              @click="isSubmissionModalOpen = false"
              class="w-full sm:w-auto"
              >Cancel</Button
            >
            <Button type="submit" :disabled="isSubmitting" class="w-full sm:w-auto">
              <span v-if="isSubmitting" class="mr-2">Saving...</span>
              <span v-else>Save</span>
            </Button>
          </DialogFooter>
        </form>
      </DialogContent>
    </Dialog>
  </Layout>
</template>
