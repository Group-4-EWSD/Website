<script setup lang="ts">
import dayjs from 'dayjs'
import { PencilIcon, PlusIcon, TrashIcon } from 'lucide-vue-next'
import { computed, onMounted, reactive, ref } from 'vue'

import AcademicYears from '@/components/pagespecific/admin-management/AcademicYears.vue'
import Faculties from '@/components/pagespecific/admin-management/Faculties.vue'
import {
  Accordion,
  AccordionContent,
  AccordionItem,
  AccordionTrigger,
} from '@/components/ui/accordion'
import {
  AlertDialog,
  AlertDialogAction,
  AlertDialogCancel,
  AlertDialogContent,
  AlertDialogDescription,
  AlertDialogFooter,
  AlertDialogHeader,
  AlertDialogTitle,
} from '@/components/ui/alert-dialog'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardHeader, CardTitle } from '@/components/ui/card'
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import Layout from '@/components/ui/Layout.vue'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'

// Import child components

// Type definitions
interface AcademicYear {
  id: number
  name: string
}

interface Faculty {
  id: number
  name: string
}

interface SubmissionDate {
  id: number
  academicYearId: number
  facultyId: number
  preSubmissionDate: string
  actualSubmissionDate: string
}

interface SubmissionForm {
  id: number | null
  academicYearId: number | string
  facultyId: number | string
  preSubmissionDate: string
  actualSubmissionDate: string
}

type DeleteType = 'submission' | 'academic-year' | 'faculty' | ''

// Data for academic years
const academicYears = ref<AcademicYear[]>([
  { id: 1, name: '2023-2024' },
  { id: 2, name: '2024-2025' },
])

// Data for faculties
const faculties = ref<Faculty[]>([
  { id: 1, name: 'Science' },
  { id: 2, name: 'Arts' },
  { id: 3, name: 'Engineering' },
])

// Data for submission dates (now with facultyId)
const submissionDates = ref<SubmissionDate[]>([
  {
    id: 1,
    academicYearId: 1,
    facultyId: 1,
    preSubmissionDate: '2023-11-15',
    actualSubmissionDate: '2023-12-01',
  },
  {
    id: 2,
    academicYearId: 1,
    facultyId: 2,
    preSubmissionDate: '2023-11-10',
    actualSubmissionDate: '2023-11-25',
  },
  {
    id: 3,
    academicYearId: 2,
    facultyId: 1,
    preSubmissionDate: '2024-11-15',
    actualSubmissionDate: '2024-12-01',
  },
  {
    id: 4,
    academicYearId: 2,
    facultyId: 3,
    preSubmissionDate: '2024-11-20',
    actualSubmissionDate: '2024-12-05',
  },
])

// Default open accordion item
const openAccordionItems = ref(['submission-dates'])

const selectedAcademicYearFilter = ref<number>(getCurrentAcademicYearId())

// Form states
const isSubmissionModalOpen = ref<boolean>(false)
const isDeleteDialogOpen = ref<boolean>(false)
const editingSubmission = ref<SubmissionDate | null>(null)
const itemToDelete = ref<any>(null)
const deleteType = ref<DeleteType>('')

const submissionForm = reactive<SubmissionForm>({
  id: null,
  academicYearId: '',
  facultyId: '',
  preSubmissionDate: '',
  actualSubmissionDate: '',
})

// Helper functions
const getAcademicYearName = (id: number): string => {
  const year = academicYears.value.find((y) => y.id === id)
  return year ? year.name : 'Unknown'
}

const getFacultyName = (id: number): string => {
  const faculty = faculties.value.find((f) => f.id === id)
  return faculty ? faculty.name : 'Unknown'
}

const formatDate = (dateString: string): string => {
  if (!dateString) return 'Not set'
  try {
    return dayjs(new Date(dateString)).format('MMM DD, YYYY')
  } catch (e) {
    return dateString
  }
}

// Modal functions
const openSubmissionModal = (submission: SubmissionDate | null): void => {
  if (submission) {
    editingSubmission.value = submission
    submissionForm.id = submission.id
    submissionForm.academicYearId = submission.academicYearId
    submissionForm.facultyId = submission.facultyId
    submissionForm.preSubmissionDate = submission.preSubmissionDate
    submissionForm.actualSubmissionDate = submission.actualSubmissionDate
  } else {
    editingSubmission.value = null
    submissionForm.id = null
    submissionForm.academicYearId = ''
    submissionForm.facultyId = ''
    submissionForm.preSubmissionDate = ''
    submissionForm.actualSubmissionDate = ''
  }
  isSubmissionModalOpen.value = true
}

// Submission dates functions
const saveSubmissionDate = (): void => {
  // Type assertion for academicYearId and facultyId to convert from string if needed
  const academicYearId =
    typeof submissionForm.academicYearId === 'string'
      ? parseInt(submissionForm.academicYearId, 10)
      : submissionForm.academicYearId

  const facultyId =
    typeof submissionForm.facultyId === 'string'
      ? parseInt(submissionForm.facultyId, 10)
      : submissionForm.facultyId

  if (editingSubmission.value) {
    // Update existing submission date
    const index = submissionDates.value.findIndex((d) => d.id === submissionForm.id)
    if (index !== -1) {
      submissionDates.value[index] = {
        ...submissionForm,
        academicYearId,
        facultyId,
      } as SubmissionDate
    }
  } else {
    // Add new submission date
    const newId = Math.max(0, ...submissionDates.value.map((d) => d.id)) + 1
    submissionDates.value.push({
      id: newId,
      academicYearId,
      facultyId,
      preSubmissionDate: submissionForm.preSubmissionDate,
      actualSubmissionDate: submissionForm.actualSubmissionDate,
    })
  }
  isSubmissionModalOpen.value = false
}

// Helper function to get the current academic year ID
function getCurrentAcademicYearId(): number {
  if (academicYears.value.length === 0) return 0
  return academicYears.value.reduce(
    (latest, year) => (year.id > latest ? year.id : latest),
    academicYears.value[0].id,
  )
}

// Get filtered submission dates based on selected academic year
const filteredSubmissionDates = computed(() => {
  if (!selectedAcademicYearFilter.value) return submissionDates.value
  return submissionDates.value.filter(
    (date) => date.academicYearId === selectedAcademicYearFilter.value,
  )
})

// Check if selected academic year is current (latest)
const isCurrentAcademicYear = computed(() => {
  return selectedAcademicYearFilter.value === getCurrentAcademicYearId()
})

// Confirm delete dialog
const confirmDeleteSubmission = (submission: SubmissionDate): void => {
  itemToDelete.value = submission
  deleteType.value = 'submission'
  isDeleteDialogOpen.value = true
}

const confirmDelete = (): void => {
  if (deleteType.value === 'submission') {
    submissionDates.value = submissionDates.value.filter((d) => d.id !== itemToDelete.value.id)
  }
  isDeleteDialogOpen.value = false
  itemToDelete.value = null
}

// Academic years CRUD operations
const addAcademicYear = (year: { name: string }): void => {
  const newId = Math.max(0, ...academicYears.value.map((y) => y.id)) + 1
  academicYears.value.push({ id: newId, name: year.name })
}

const updateAcademicYear = (year: AcademicYear): void => {
  const index = academicYears.value.findIndex((y) => y.id === year.id)
  if (index !== -1) {
    academicYears.value[index] = { ...year }
  }
}

const deleteAcademicYear = (yearId: number): void => {
  academicYears.value = academicYears.value.filter((y) => y.id !== yearId)
  // Also delete associated submission dates
  submissionDates.value = submissionDates.value.filter((d) => d.academicYearId !== yearId)
}

// Faculties CRUD operations
const addFaculty = (faculty: { name: string }): void => {
  const newId = Math.max(0, ...faculties.value.map((f) => f.id)) + 1
  faculties.value.push({ id: newId, name: faculty.name })
}

const updateFaculty = (faculty: Faculty): void => {
  const index = faculties.value.findIndex((f) => f.id === faculty.id)
  if (index !== -1) {
    faculties.value[index] = { ...faculty }
  }
}

const deleteFaculty = (facultyId: number): void => {
  faculties.value = faculties.value.filter((f) => f.id !== facultyId)
  // Also delete associated submission dates
  submissionDates.value = submissionDates.value.filter((d) => d.facultyId !== facultyId)
}

onMounted(() => {
  // You would typically fetch data from an API here
  console.log('Component mounted')
})
</script>
<template>
  <Layout>
    <h1 class="text-2xl font-bold mb-4">Academic Management Dashboard</h1>

    <!-- Accordion for switching between different sections -->
    <div class="mb-8">
      <Accordion type="multiple" v-model="openAccordionItems" class="w-full">
        <!-- Submission Dates Accordion (Open by default) -->

        <AccordionItem value="submission-dates">
          <AccordionTrigger>
            <div class="flex flex-col gap-2 items-start">
              <h3 class="text-xl font-semibold">Submission Dates</h3>
              <p>Manage pre-submission and actual submission dates for each academic year and faculty </p>
            </div>
          </AccordionTrigger>
          <AccordionContent>
            <Card class="mt-4">
              <CardHeader>
                <CardTitle class="text-2xl">Submission Dates</CardTitle>
              </CardHeader>
              <CardContent>
                <div class="flex flex-col sm:flex-row sm:justify-between mb-4 gap-4">
                  <div class="flex items-center gap-3">
                    <Label for="academicYearFilter" class="font-medium whitespace-nowrap"
                      >Academic Year:</Label
                    >
                    <Select v-model="selectedAcademicYearFilter" class="w-40">
                      <SelectTrigger id="academicYearFilter">
                        <SelectValue placeholder="Select year" />
                      </SelectTrigger>
                      <SelectContent>
                        <SelectItem v-for="year in academicYears" :key="year.id" :value="year.id">
                          {{ year.name }}
                        </SelectItem>
                      </SelectContent>
                    </Select>
                  </div>

                  <Button @click="openSubmissionModal(null)" :disabled="!isCurrentAcademicYear">
                    <PlusIcon class="mr-2 h-4 w-4" /> Add New Date
                  </Button>
                </div>

                <!-- Submission Dates Table -->
                <div class="rounded-md border">
                  <Table>
                    <TableHeader>
                      <TableRow>
                        <TableHead>Academic Year</TableHead>
                        <TableHead>Faculty</TableHead>
                        <TableHead>Pre-Submission Date</TableHead>
                        <TableHead>Actual Submission Date</TableHead>
                        <TableHead class="text-right">Actions</TableHead>
                      </TableRow>
                    </TableHeader>
                    <TableBody>
                      <TableRow v-for="date in filteredSubmissionDates" :key="date.id">
                        <TableCell>{{ getAcademicYearName(date.academicYearId) }}</TableCell>
                        <TableCell>{{ getFacultyName(date.facultyId) }}</TableCell>
                        <TableCell>{{ formatDate(date.preSubmissionDate) }}</TableCell>
                        <TableCell>{{ formatDate(date.actualSubmissionDate) }}</TableCell>
                        <TableCell class="text-right">
                          <div class="flex justify-end gap-2">
                            <Button
                              variant="ghost"
                              size="icon"
                              @click="openSubmissionModal(date)"
                              :disabled="date.academicYearId !== getCurrentAcademicYearId()"
                            >
                              <PencilIcon class="h-4 w-4" />
                            </Button>
                            <Button
                              variant="ghost"
                              size="icon"
                              @click="confirmDeleteSubmission(date)"
                              :disabled="date.academicYearId !== getCurrentAcademicYearId()"
                            >
                              <TrashIcon class="h-4 w-4" />
                            </Button>
                          </div>
                        </TableCell>
                      </TableRow>
                      <TableRow v-if="filteredSubmissionDates.length === 0">
                        <TableCell colspan="5" class="text-center py-6 text-gray-500">
                          No submission dates found for the selected academic year.
                        </TableCell>
                      </TableRow>
                    </TableBody>
                  </Table>
                </div>
              </CardContent>
            </Card>
          </AccordionContent>
        </AccordionItem>

        <!-- Academic Years Accordion -->
        <AccordionItem value="academic-years">
          <AccordionTrigger>
            <div class="flex flex-col gap-2 items-start">
              <h3 class="text-xl font-semibold">Academic Years</h3>
              <p>Manage academic years in the system</p>
            </div>
          </AccordionTrigger>
          <AccordionContent>
            <div class="mt-4">
              <AcademicYears
                :academicYears="academicYears"
                @add="addAcademicYear"
                @update="updateAcademicYear"
                @delete="deleteAcademicYear"
              />
            </div>
          </AccordionContent>
        </AccordionItem>

        <!-- Faculties Accordion -->
        <AccordionItem value="faculties">
          <AccordionTrigger>
            <div class="flex flex-col gap-2 items-start">
              <h3 class="text-xl font-semibold">Faculties</h3>
              <p>Manage faculties in the system</p>
            </div>
          </AccordionTrigger>
          <AccordionContent>
            <div class="mt-4">
              <Faculties
                :faculties="faculties"
                @add="addFaculty"
                @update="updateFaculty"
                @delete="deleteFaculty"
              />
            </div>
          </AccordionContent>
        </AccordionItem>
      </Accordion>
    </div>

    <!-- Submission Date Modal -->
    <Dialog :open="isSubmissionModalOpen" @update:open="isSubmissionModalOpen = $event">
      <DialogContent class="sm:max-w-[500px]">
        <DialogHeader>
          <DialogTitle>{{ editingSubmission ? 'Edit' : 'Add' }} Submission Date</DialogTitle>
          <DialogDescription>
            Set the pre-submission and actual submission dates for an academic year and faculty
          </DialogDescription>
        </DialogHeader>

        <div class="grid gap-4 py-4">
          <div class="grid gap-2">
            <Label for="academicYear">Academic Year</Label>
            <Select v-model="submissionForm.academicYearId">
              <SelectTrigger>
                <SelectValue placeholder="Select academic year" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem v-for="year in academicYears" :key="year.id" :value="year.id">
                  {{ year.name }}
                </SelectItem>
              </SelectContent>
            </Select>
          </div>

          <div class="grid gap-2">
            <Label for="faculty">Faculty</Label>
            <Select v-model="submissionForm.facultyId">
              <SelectTrigger>
                <SelectValue placeholder="Select faculty" />
              </SelectTrigger>
              <SelectContent>
                <SelectItem v-for="faculty in faculties" :key="faculty.id" :value="faculty.id">
                  {{ faculty.name }}
                </SelectItem>
              </SelectContent>
            </Select>
          </div>

          <div class="grid gap-2">
            <Label for="preSubmissionDate">Pre-Submission Date</Label>
            <Input id="preSubmissionDate" type="date" v-model="submissionForm.preSubmissionDate" />
          </div>

          <div class="grid gap-2">
            <Label for="actualSubmissionDate">Actual Submission Date</Label>
            <Input
              id="actualSubmissionDate"
              type="date"
              v-model="submissionForm.actualSubmissionDate"
            />
          </div>
        </div>

        <DialogFooter>
          <Button variant="outline" @click="isSubmissionModalOpen = false">Cancel</Button>
          <Button @click="saveSubmissionDate">Save</Button>
        </DialogFooter>
      </DialogContent>
    </Dialog>

    <!-- Delete Confirmation Modal -->
    <AlertDialog :open="isDeleteDialogOpen" @update:open="isDeleteDialogOpen = $event">
      <AlertDialogContent>
        <AlertDialogHeader>
          <AlertDialogTitle>Are you sure?</AlertDialogTitle>
          <AlertDialogDescription>
            This action cannot be undone. This will permanently delete this item from the database.
          </AlertDialogDescription>
        </AlertDialogHeader>
        <AlertDialogFooter>
          <AlertDialogCancel @click="isDeleteDialogOpen = false">Cancel</AlertDialogCancel>
          <AlertDialogAction @click="confirmDelete">Delete</AlertDialogAction>
        </AlertDialogFooter>
      </AlertDialogContent>
    </AlertDialog>
  </Layout>
</template>
