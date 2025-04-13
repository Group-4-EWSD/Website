<script setup lang="ts">
import dayjs from 'dayjs'
import { useForm } from 'vee-validate'
import * as yup from 'yup'
import { ArrowDown, ArrowUp, Edit, KeyRound, Plus, Search } from 'lucide-vue-next'
import { computed, onMounted, ref, watch } from 'vue'

import { Button } from '@/components/ui/button'
import { Card } from '@/components/ui/card'
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog'
import CustomInput from '@/components/shared/Input.vue'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import Layout from '@/components/ui/Layout.vue'
import {
  Pagination,
  PaginationEllipsis,
  PaginationFirst,
  PaginationLast,
  PaginationList,
  PaginationListItem,
  PaginationNext,
  PaginationPrev,
} from '@/components/ui/pagination'
import {
  Select,
  SelectContent,
  SelectItem,
  SelectTrigger,
  SelectValue,
} from '@/components/ui/select'
import CustomSelect from '@/components/shared/Select.vue'
import { Skeleton } from '@/components/ui/skeleton'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import FormElement from '@/components/shared/FormElement.vue'
import { getAllUsers, createUser, updateUser, forcePasswordReset } from '@/api/user'
import { getFacultyList } from '@/api/faculties'
import type { CreateUserParams, UpdateUserParams, User } from '@/types/user'
import type { Faculty } from '@/types/faculty'
import { calculateAge } from '@/lib/utils'
import { toast } from 'vue-sonner'
import TooltipWrapper from '@/components/shared/TooltipWrapper.vue'
import type { AcceptableValue } from 'reka-ui'

interface Column {
  key: string
  label: string
}

type SortDirection = 'asc' | 'desc'

// Component state
const searchQuery = ref('')
const selectedUserType = ref('')
const selectedFaculty = ref('')
const allUsers = ref<User[]>([])
const displayedUsers = ref<User[]>([])
const faculties = ref<Faculty[]>([])
const userTypes = ref<{ value: string; label: string }[]>([
  { value: '0', label: 'Guest' },
  { value: '1', label: 'Student' },
  { value: '2', label: 'Marketing Coordinator' },
  { value: '3', label: 'Marketing Manager' },
  { value: '4', label: 'Admin' },
])
const isLoading = ref(true)
const currentPage = ref(1)
const pageSize = ref(10)
const totalItems = ref(0)
const sortColumn = ref('id')
const sortDirection = ref<SortDirection>('asc')
const showUserDialog = ref(false)
const showForcePasswordDialog = ref(false)
const userToEdit = ref<User | null>(null)
const userToForcePasswordChange = ref<User | null>(null)
const isSubmitting = ref(false)

// Gender options mapping
const genderOptions = [
  { label: 'Male', value: 1 },
  { label: 'Female', value: 2 },
  { label: 'Prefer not to say', value: 0 },
]

const validationSchema = yup.object({
  user_name: yup.string().required('Name is required'),
  nickname: yup.string().optional(),
  user_email: yup.string().email('Must be a valid email').required('Email is required'),
  user_type_id: yup.string().required('User type is required'),
  faculty_id: yup.string().when('user_type_id', {
    is: (val: string) => !['3', '4'].includes(val),
    then: () => yup.string().required('Faculty is required'),
    otherwise: () => yup.string().nullable(),
  }),
  gender: yup.number().required('Gender is required'),
  date_of_birth: yup
    .string()
    .nullable()
    .optional()
    .test('format-if-exists', 'Date of birth must be in the format YYYY-MM-DD', (value) => {
      if (!value) return true
      return /^\d{4}-\d{2}-\d{2}$/.test(value)
    })
    .test('valid-date-if-exists', 'Invalid date', (value) => {
      if (!value) return true
      const date = new Date(value)
      return date instanceof Date && !isNaN(date.getTime())
    })
    .test('age-min-if-exists', 'You must be at least 16 years old', (value) => {
      if (!value) return true
      const age = calculateAge(value)
      return age >= 16
    })
    .test('age-max-if-exists', 'Age cannot be greater than 100 years', (value) => {
      if (!value) return true
      const age = calculateAge(value)
      return age <= 100
    }),
  phone_number: yup
    .string()
    .nullable()
    .optional()
    .test('format-if-exists', 'Invalid phone number format', (value) => {
      if (!value) return true
      return /^\+?[0-9\s\-()]+$/.test(value)
    }),
})

// VeeValidate form setup
const { handleSubmit, errors, values, resetForm, setValues } = useForm({
  validationSchema,
  initialValues: {
    user_name: '',
    nickname: '',
    user_email: '',
    user_type_id: '',
    faculty_id: '',
    gender: 0,
    date_of_birth: "",
    phone_number: "",
  },
})

const columns: Column[] = [
  { key: 'id', label: 'ID' },
  { key: 'user_name', label: 'Name' },
  { key: 'user_email', label: 'Email' },
  { key: 'user_type_name', label: 'User Type' },
  { key: 'faculty_name', label: 'Faculty' },
  { key: 'date_of_birth', label: 'Date of Birth' },
]

// Fetch all users
async function fetchUsers() {
  try {
    isLoading.value = true
    const data = await getAllUsers()
    allUsers.value = data
    totalItems.value = data.length
    applyFiltersAndSort()
  } catch (error) {
    console.error('Error fetching users:', error)
  } finally {
    isLoading.value = false
  }
}

// Fetch all faculties
async function fetchFaculties() {
  try {
    const data = await getFacultyList()
    faculties.value = data
  } catch (error) {
    console.error('Error fetching faculties:', error)
  }
}

// Apply filters, sorting, and pagination to the data
function applyFiltersAndSort() {
  let filteredData = [...allUsers.value]

  // Apply search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filteredData = filteredData.filter(
      (user) =>
        user.user_name.toLowerCase().includes(query) ||
        user.user_email.toLowerCase().includes(query) ||
        user.nickname.toLowerCase().includes(query),
    )
  }

  // Apply user type filter
  if (selectedUserType.value) {
    filteredData = filteredData.filter((user) => user.user_type_id === selectedUserType.value)
  }

  // Apply faculty filter
  if (selectedFaculty.value) {
    filteredData = filteredData.filter((user) => user.faculty_id === selectedFaculty.value)
  }

  // Apply sorting
  filteredData.sort((a, b) => {
    const aValue = a[sortColumn.value as keyof User]
    const bValue = b[sortColumn.value as keyof User]

    if (aValue === null || aValue === undefined) return 1
    if (bValue === null || bValue === undefined) return -1

    if (aValue < bValue) return sortDirection.value === 'asc' ? -1 : 1
    if (aValue > bValue) return sortDirection.value === 'asc' ? 1 : -1
    return 0
  })

  totalItems.value = filteredData.length

  // Apply pagination
  const start = (currentPage.value - 1) * pageSize.value
  const end = start + pageSize.value
  displayedUsers.value = filteredData.slice(start, end)
}

// Initialize data
onMounted(async () => {
  try {
    // Fetch both users and faculties in parallel
    await Promise.all([fetchUsers(), fetchFaculties()])
  } catch (error) {
    console.error('Error initializing data:', error)
  }
})

// Watch for changes to filters and sort criteria
watch([searchQuery, selectedUserType, selectedFaculty, sortColumn, sortDirection], () => {
  currentPage.value = 1 // Reset to first page when filters change
  applyFiltersAndSort()
})

// Watch for page changes
watch(currentPage, () => {
  applyFiltersAndSort()
})

const totalPages = computed(() => {
  return Math.ceil(totalItems.value / pageSize.value)
})

function handlePageChange(page: number) {
  currentPage.value = page
}

function handleSearch() {
  applyFiltersAndSort()
}

// Then update your functions:
function updateUserType(value: AcceptableValue) {
  selectedUserType.value = value ? value.toString() : ''
  // Reset faculty if user type is manager or admin
  if (value && ['3', '4'].includes(selectedUserType.value)) {
    selectedFaculty.value = ''
  }
}

function updateFaculty(value: AcceptableValue) {
  selectedFaculty.value = value ? value.toString() : ''
}

function getUserTypeLabel(value: string): string {
  if (!value) return 'All User Types'
  return getUserTypeName(value)
}

function getFacultyLabel(value: string): string {
  if (!value) return 'All Faculties'
  const faculty = faculties.value.find((f) => f.faculty_id === value)
  return faculty ? faculty.faculty_name : 'All Faculties'
}

function sortBy(column: string) {
  if (sortColumn.value === column) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortColumn.value = column
    sortDirection.value = 'asc'
  }
}

function editUser(user: User) {
  userToEdit.value = user
  setValues({
    user_name: user.user_name,
    nickname: user.nickname,
    user_email: user.user_email,
    user_type_id: user.user_type_id,
    faculty_id: user.faculty_id,
    gender: user.gender,
    date_of_birth: user.date_of_birth as string,
    phone_number: user.phone_number as string,
  })
  showUserDialog.value = true
}

function addUser() {
  userToEdit.value = null
  resetForm()
  showUserDialog.value = true
}

function confirmForcePasswordChange(user: User) {
  userToForcePasswordChange.value = user
  showForcePasswordDialog.value = true
}

async function forcePasswordChange() {
  if (userToForcePasswordChange.value) {
    try {
      isSubmitting.value = true
      await forcePasswordReset(userToForcePasswordChange.value.id)

      toast.success('Password reset successfully')

      showForcePasswordDialog.value = false
      userToForcePasswordChange.value = null
    } catch (error) {
      toast.error('Failed to reset password. Please try again.')
    } finally {
      isSubmitting.value = false
    }
  }
}

const onSubmit = handleSubmit(async (formValues) => {
  try {
    isSubmitting.value = true
    if (userToEdit.value) {
      // Update existing user
      const updateParams: UpdateUserParams = {
        ...formValues,
        id: userToEdit.value.id,
      }

      await updateUser(updateParams)

      toast.success('User updated successfully')

      // Update the local state after successful API call
      const index = allUsers.value.findIndex((u) => u.id === userToEdit.value!.id)
      if (index !== -1) {
        allUsers.value[index] = {
          ...allUsers.value[index],
          ...updateParams,
          user_type_name: getUserTypeName(updateParams.user_type_id),
          faculty_name: getFacultyName(updateParams.faculty_id),
        }
      }
    } else {
      // Add new user
      const createParams: CreateUserParams = formValues
      await createUser(createParams)

      toast.success('User created successfully')

      // Refresh the user list after successful user creation
      fetchUsers()
    }
    showUserDialog.value = false
    userToEdit.value = null
  } catch (error) {
    toast.error('Failed to save user. Please try again.')
  } finally {
    isSubmitting.value = false
  }
})

function getUserTypeName(typeId: string): string {
  const userType = userTypes.value.find((t) => t.value === typeId)
  return userType ? userType.label : ''
}

function getFacultyName(facultyId: string): string {
  const faculty = faculties.value.find((f) => f.faculty_id === facultyId)
  return faculty ? faculty.faculty_name : ''
}

function formatDate(dateString: string | null): string {
  if (!dateString) return '-'
  return dayjs(dateString).format('MMM D, YYYY')
}
</script>

<template>
  <Layout>
    <div class="space-y-6">
      <!-- Header and Filters - Make responsive -->
      <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <h1 class="text-2xl font-bold">Users Management</h1>
        <div class="flex flex-col gap-4 md:flex-row">
          <!-- Search Input -->
          <div class="relative w-full md:w-auto">
            <Input
              v-model="searchQuery"
              placeholder="Search users..."
              class="w-full md:w-80"
              @update:model-value="handleSearch"
            >
              <template #prefix>
                <Search class="h-4 w-4 text-muted-foreground" />
              </template>
            </Input>
          </div>

          <!-- User Type Filter -->
          <Select :modelValue="selectedUserType" @update:modelValue="updateUserType">
            <SelectTrigger class="w-full md:w-40">
              <SelectValue :placeholder="getUserTypeLabel(selectedUserType)" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem :value="null">All User Types</SelectItem>
              <SelectItem v-for="type in userTypes" :key="type.value" :value="type.value">
                {{ type.label }}
              </SelectItem>
            </SelectContent>
          </Select>

          <!-- Faculty Filter (conditional) -->
          <Select
            :disabled="['3', '4'].includes(selectedUserType)"
            :modelValue="selectedFaculty"
            @update:modelValue="updateFaculty"
          >
            <SelectTrigger class="w-full md:w-40">
              <SelectValue :placeholder="getFacultyLabel(selectedFaculty)" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem :value="null">All Faculties</SelectItem>
              <SelectItem
                v-for="faculty in faculties"
                :key="faculty.faculty_id"
                :value="faculty.faculty_id"
              >
                {{ faculty.faculty_name }}
              </SelectItem>
            </SelectContent>
          </Select>

          <!-- Add User Button -->
          <Button @click="addUser" class="w-full md:w-auto">
            <Plus class="h-4 w-4 mr-2" />
            Add User
          </Button>
        </div>
      </div>

      <!-- Desktop Table View -->
      <Card class="hidden md:block">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead
                v-for="(column, index) in columns"
                :key="column.key"
                class="cursor-pointer"
                :class="{ 'pl-6': index === 0 }"
                @click="sortBy(column.key)"
              >
                {{ column.label }}
                <ArrowUp
                  v-if="sortColumn === column.key && sortDirection === 'asc'"
                  class="ml-2 inline h-4 w-4"
                />
                <ArrowDown
                  v-if="sortColumn === column.key && sortDirection === 'desc'"
                  class="ml-2 inline h-4 w-4"
                />
              </TableHead>
              <TableHead>Actions</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <template v-if="isLoading">
              <TableRow v-for="n in pageSize" :key="n">
                <TableCell class="pl-6">
                  <Skeleton class="h-4 w-8" />
                </TableCell>
                <TableCell>
                  <Skeleton class="h-4 w-32" />
                </TableCell>
                <TableCell>
                  <Skeleton class="h-4 w-48" />
                </TableCell>
                <TableCell>
                  <Skeleton class="h-4 w-24" />
                </TableCell>
                <TableCell>
                  <Skeleton class="h-4 w-24" />
                </TableCell>
                <TableCell>
                  <Skeleton class="h-4 w-24" />
                </TableCell>
                <TableCell>
                  <div class="flex space-x-2">
                    <Skeleton class="h-8 w-8" />
                    <Skeleton class="h-8 w-8" />
                  </div>
                </TableCell>
              </TableRow>
            </template>
            <template v-else>
              <TableRow v-if="displayedUsers.length === 0">
                <TableCell :colspan="columns.length + 1" class="text-center">
                  No users found
                </TableCell>
              </TableRow>
              <TableRow v-for="user in displayedUsers" :key="user.id">
                <TableCell class="pl-6">{{ user.id }}</TableCell>
                <TableCell>{{ user.user_name }}</TableCell>
                <TableCell>{{ user.user_email }}</TableCell>
                <TableCell>{{ user.user_type_name }}</TableCell>
                <TableCell>{{ user.faculty_name || '-' }}</TableCell>
                <TableCell>{{ formatDate(user.date_of_birth) }}</TableCell>
                <TableCell>
                  <div class="flex space-x-2">
                    <TooltipWrapper text="Edit User">
                      <Button variant="ghost" size="icon" @click="editUser(user)">
                        <Edit class="h-4 w-4" />
                      </Button>
                    </TooltipWrapper>
                    <TooltipWrapper text="Force Password Change">
                      <Button variant="ghost" size="icon" @click="confirmForcePasswordChange(user)">
                        <KeyRound class="h-4 w-4" />
                      </Button>
                    </TooltipWrapper>
                  </div>
                </TableCell>
              </TableRow>
            </template>
          </TableBody>
        </Table>

        <!-- Desktop Pagination -->
        <div class="flex items-center justify-between px-6 py-4 border-t">
          <div class="text-sm text-muted-foreground">
            Showing {{ (currentPage - 1) * pageSize + 1 }} to
            {{ Math.min(currentPage * pageSize, totalItems) }} of {{ totalItems }} users
          </div>
          <Pagination
            :items-per-page="pageSize"
            :total="totalItems"
            :sibling-count="1"
            show-edges
            :default-page="currentPage"
            @update:page="handlePageChange"
          >
            <PaginationList v-slot="{ items }" class="flex items-center gap-1">
              <PaginationFirst />
              <PaginationPrev />
              <template v-for="(item, index) in items" :key="index">
                <PaginationListItem v-if="item.type === 'page'" :value="item.value" as-child>
                  <Button
                    class="w-10 h-10 p-0"
                    :variant="item.value === currentPage ? 'default' : 'outline'"
                  >
                    {{ item.value }}
                  </Button>
                </PaginationListItem>
                <PaginationEllipsis v-else />
              </template>
              <PaginationNext />
              <PaginationLast />
            </PaginationList>
          </Pagination>
        </div>
      </Card>

      <!-- Mobile Card View -->
      <div class="grid gap-4 md:hidden">
        <template v-if="isLoading">
          <Card v-for="n in pageSize" :key="n" class="p-4">
            <div class="space-y-3">
              <Skeleton class="h-4 w-1/4" />
              <Skeleton class="h-4 w-3/4" />
              <Skeleton class="h-4 w-1/2" />
              <div class="flex justify-between items-center pt-2">
                <Skeleton class="h-8 w-8" />
                <Skeleton class="h-8 w-8" />
              </div>
            </div>
          </Card>
        </template>
        <template v-else>
          <Card v-for="user in displayedUsers" :key="user.id" class="p-4">
            <div class="space-y-2">
              <div class="flex justify-between items-start">
                <div>
                  <h3 class="font-medium">{{ user.user_name }}</h3>
                  <p class="text-sm text-muted-foreground">{{ user.user_email }}</p>
                </div>
                <div class="flex gap-2">
                  <Button variant="ghost" size="icon" @click="editUser(user)">
                    <Edit class="h-4 w-4" />
                  </Button>
                  <Button variant="ghost" size="icon" @click="confirmForcePasswordChange(user)">
                    <KeyRound class="h-4 w-4" />
                  </Button>
                </div>
              </div>
              <div class="grid grid-cols-2 gap-2 text-sm">
                <div>
                  <span class="text-muted-foreground">User Type:</span>
                  <p>{{ user.user_type_name }}</p>
                </div>
                <div>
                  <span class="text-muted-foreground">Faculty:</span>
                  <p>{{ user.faculty_name || '-' }}</p>
                </div>
                <div class="col-span-2">
                  <span class="text-muted-foreground">Date of Birth:</span>
                  <p>{{ formatDate(user.date_of_birth) }}</p>
                </div>
              </div>
            </div>
          </Card>
        </template>

        <!-- Mobile Pagination -->
        <div class="flex flex-col gap-4 items-center">
          <div class="text-sm text-muted-foreground text-center">
            Showing {{ (currentPage - 1) * pageSize + 1 }} to
            {{ Math.min(currentPage * pageSize, totalItems) }} of {{ totalItems }} users
          </div>
          <Pagination
            :items-per-page="pageSize"
            :total="totalItems"
            :sibling-count="1"
            show-edges
            :default-page="currentPage"
            @update:page="handlePageChange"
          >
            <PaginationList v-slot="{ items }" class="flex items-center gap-1">
              <PaginationFirst />
              <PaginationPrev />
              <template v-for="(item, index) in items" :key="index">
                <PaginationListItem v-if="item.type === 'page'" :value="item.value" as-child>
                  <Button
                    class="w-8 h-8 p-0"
                    :variant="item.value === currentPage ? 'default' : 'outline'"
                  >
                    {{ item.value }}
                  </Button>
                </PaginationListItem>
                <PaginationEllipsis v-else />
              </template>
              <PaginationNext />
              <PaginationLast />
            </PaginationList>
          </Pagination>
        </div>
      </div>

      <!-- Add/Edit User Dialog -->
      <Dialog v-model:open="showUserDialog">
        <DialogContent class="w-[90vw] max-w-2xl">
          <DialogHeader>
            <DialogTitle>{{ userToEdit ? 'Edit User' : 'Add User' }}</DialogTitle>
            <DialogDescription>
              {{
                userToEdit
                  ? 'Update user information below.'
                  : 'Fill in the user information below.'
              }}
            </DialogDescription>
          </DialogHeader>
          <form @submit.prevent="onSubmit" class="space-y-4 pt-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <FormElement>
                <template #label>
                  <Label for="user_name">Name</Label>
                </template>
                <template #field>
                  <CustomInput
                    name="user_name"
                    id="user_name"
                    placeholder="Enter name"
                    :errors="errors"
                  />
                </template>
              </FormElement>

              <FormElement>
                <template #label>
                  <Label for="user_email">Email</Label>
                </template>
                <template #field>
                  <CustomInput
                    name="user_email"
                    id="user_email"
                    type="email"
                    placeholder="Enter email"
                    :errors="errors"
                  />
                </template>
              </FormElement>
            </div>

            <FormElement>
              <template #label>
                <Label for="user_type_id">User Type</Label>
              </template>
              <template #field>
                <CustomSelect
                  name="user_type_id"
                  id="user_type_id"
                  :errors="errors"
                  :options="userTypes"
                  :modelValue="values.user_type_id"
                />
              </template>
            </FormElement>

            <FormElement v-if="!['3', '4'].includes(values.user_type_id)">
              <template #label>
                <Label for="faculty_id">Faculty</Label>
              </template>
              <template #field>
                <CustomSelect
                  name="faculty_id"
                  id="faculty_id"
                  :errors="errors"
                  :options="faculties.map((f) => ({ label: f.faculty_name, value: f.faculty_id }))"
                  :modelValue="values.faculty_id"
                />
              </template>
            </FormElement>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <FormElement>
                <template #label>
                  <Label for="gender">Gender</Label>
                </template>
                <template #field>
                  <CustomSelect
                    name="gender"
                    id="gender"
                    :errors="errors"
                    :options="genderOptions"
                    :modelValue="values.gender"
                  ></CustomSelect>
                </template>
              </FormElement>

              <FormElement>
                <template #label>
                  <Label for="date_of_birth">Date of Birth</Label>
                </template>
                <template #field>
                  <CustomInput
                    name="date_of_birth"
                    id="date_of_birth"
                    type="date"
                    :errors="errors"
                  />
                </template>
              </FormElement>
            </div>

            <FormElement>
              <template #label>
                <Label for="phone_number">Phone Number</Label>
              </template>
              <template #field>
                <CustomInput
                  name="phone_number"
                  id="phone_number"
                  placeholder="Enter phone number"
                  :errors="errors"
                />
              </template>
            </FormElement>

            <DialogFooter class="flex flex-col sm:flex-row gap-2 mt-4">
              <Button type="button" variant="outline" @click="showUserDialog = false"
                >Cancel</Button
              >
              <Button type="submit" :processing="isSubmitting">
                {{ isSubmitting ? 'Saving...' : 'Save' }}
              </Button>
            </DialogFooter>
          </form>
        </DialogContent>
      </Dialog>

      <!-- Force Password Change Dialog -->
      <Dialog v-model:open="showForcePasswordDialog">
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Force Password Change</DialogTitle>
            <DialogDescription>
              Are you sure you want to set this user's password to temporary password?
            </DialogDescription>
          </DialogHeader>
          <DialogFooter>
            <Button variant="outline" @click="showForcePasswordDialog = false">Cancel</Button>
            <Button @click="forcePasswordChange" :processing="isSubmitting">
              {{ isSubmitting ? 'Processing...' : 'Confirm' }}
            </Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </div>
  </Layout>
</template>
