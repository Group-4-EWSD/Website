<script setup lang="ts">
import dayjs from 'dayjs'
import {
  ArrowDown,
  ArrowUp,
  Edit,
  KeyRound,
  Plus,
  Search,
} from 'lucide-vue-next'
import { computed, onMounted, ref } from 'vue'

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
import { Skeleton } from '@/components/ui/skeleton'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
// import {
//   Pagination,
//   PaginationContent,
//   PaginationEllipsis,
//   PaginationItem,
//   PaginationLink,
//   PaginationNext,
//   PaginationPrevious,
// } from '@/components/ui/pagination'

interface Faculty {
  id: string
  name: string
}

interface User {
  id: number
  name: string
  email: string
  userType: string
  faculty: Faculty | null
  createdAt: string
}

interface Column {
  key: string
  label: string
}

type SortDirection = 'asc' | 'desc'

const searchQuery = ref('')
const selectedUserType = ref('5')
const selectedFaculty = ref('')
const users = ref<User[]>([])
const faculties = ref<Faculty[]>([])
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

// Form data for add/edit user
const formData = ref({
  name: '',
  email: '',
  userType: '',
  faculty: '',
})

const columns: Column[] = [
  { key: 'id', label: 'ID' },
  { key: 'name', label: 'Name' },
  { key: 'email', label: 'Email' },
  { key: 'userType', label: 'User Type' },
  { key: 'faculty', label: 'Faculty' },
  { key: 'createdAt', label: 'Created At' },
]

// Fetch users with pagination
async function fetchUsers(page: number = 1) {
  try {
    isLoading.value = true
    // Simulate API call with pagination
    await new Promise((resolve) => setTimeout(resolve, 500))

    // In a real application, you would make an API call here with the following parameters:
    // - page
    // - pageSize
    // - searchQuery
    // - selectedUserType
    // - selectedFaculty
    // - sortColumn
    // - sortDirection

    // For demo purposes, we'll simulate the API response
    const start = (page - 1) * pageSize.value
    const end = start + pageSize.value
    const filteredData = users.value.slice(start, end)
    totalItems.value = users.value.length // In real app, this would come from API

    return filteredData
  } catch (error) {
    console.error('Error fetching users:', error)
    return []
  } finally {
    isLoading.value = false
  }
}

// Sample data - replace with actual API call
onMounted(async () => {
  try {
    // Simulate API call for initial data
    await new Promise((resolve) => setTimeout(resolve, 500))

    // Sample data
    users.value = [
      {
        id: 1,
        name: 'John Doe',
        email: 'john@example.com',
        userType: '1',
        faculty: { id: '1', name: 'Engineering' },
        createdAt: '2023-01-15T08:30:00Z',
      },
      {
        id: 2,
        name: 'Jane Smith',
        email: 'jane@example.com',
        userType: '2',
        faculty: { id: '2', name: 'Business' },
        createdAt: '2023-02-20T10:15:00Z',
      },
      {
        id: 3,
        name: 'Admin User',
        email: 'admin@example.com',
        userType: '4',
        faculty: null,
        createdAt: '2023-03-05T14:45:00Z',
      },
      {
        id: 4,
        name: 'Guest User',
        email: 'guest@example.com',
        userType: '0',
        faculty: null,
        createdAt: '2023-04-10T09:20:00Z',
      },
      {
        id: 5,
        name: 'Manager User',
        email: 'manager@example.com',
        userType: '3',
        faculty: null,
        createdAt: '2023-05-25T16:30:00Z',
      },
      {
        id: 6,
        name: 'Student A',
        email: 'studenta@example.com',
        userType: '1',
        faculty: { id: '3', name: 'Science' },
        createdAt: '2023-06-18T11:10:00Z',
      },
      {
        id: 7,
        name: 'Student B',
        email: 'studentb@example.com',
        userType: '1',
        faculty: { id: '1', name: 'Engineering' },
        createdAt: '2023-07-22T13:40:00Z',
      },
      {
        id: 8,
        name: 'Coordinator C',
        email: 'coordinatorc@example.com',
        userType: '2',
        faculty: { id: '3', name: 'Science' },
        createdAt: '2023-08-30T15:20:00Z',
      },
      {
        id: 9,
        name: 'Student D',
        email: 'studentd@example.com',
        userType: '1',
        faculty: { id: '2', name: 'Business' },
        createdAt: '2023-09-12T08:50:00Z',
      },
      {
        id: 10,
        name: 'Guest E',
        email: 'gueste@example.com',
        userType: '0',
        faculty: null,
        createdAt: '2023-10-05T12:15:00Z',
      },
      {
        id: 11,
        name: 'Admin F',
        email: 'adminf@example.com',
        userType: '4',
        faculty: null,
        createdAt: '2023-11-18T17:30:00Z',
      },
      {
        id: 12,
        name: 'Student G',
        email: 'studentg@example.com',
        userType: '1',
        faculty: { id: '4', name: 'Arts' },
        createdAt: '2023-12-01T09:45:00Z',
      },
    ]

    faculties.value = [
      { id: '1', name: 'Engineering' },
      { id: '2', name: 'Business' },
      { id: '3', name: 'Science' },
      { id: '4', name: 'Arts' },
    ]

    // Fetch first page of users
    await fetchUsers(1)
  } catch (error) {
    console.error('Error initializing data:', error)
  }
})

const totalPages = computed(() => {
  return Math.ceil(totalItems.value / pageSize.value)
})

function handlePageChange(page: number) {
  currentPage.value = page
  fetchUsers(page)
}

function handleSearch(): void {
  currentPage.value = 1
  fetchUsers(1)
}

function handleFilters(): void {
  currentPage.value = 1
  fetchUsers(1)
}

function getUserTypeName(type: string): string {
  const types: Record<string, string> = {
    '0': 'Guest',
    '1': 'Student',
    '2': 'Marketing Coordinator',
    '3': 'Marketing Manager',
    '4': 'Admin',
  }
  return types[type] || type
}

function getUserTypeLabel(value: string): string {
  if (!value) return 'All User Types'
  return getUserTypeName(value)
}

function getFacultyLabel(value: string): string {
  if (!value) return 'All Faculties'
  const faculty = faculties.value.find((f) => f.id === value)
  return faculty ? faculty.name : 'All Faculties'
}

function formatDate(dateString: string): string {
  return dayjs(dateString).format('MMM d, yyyy')
}

function updateUserType(value: string): void {
  selectedUserType.value = value
  // Reset faculty if user type is manager or admin
  if (['3', '4'].includes(value)) {
    selectedFaculty.value = ''
  }
  handleFilters()
}

function updateFaculty(value: string): void {
  selectedFaculty.value = value
  handleFilters()
}

function sortBy(column: string): void {
  if (sortColumn.value === column) {
    sortDirection.value = sortDirection.value === 'asc' ? 'desc' : 'asc'
  } else {
    sortColumn.value = column
    sortDirection.value = 'asc'
  }
}

function editUser(user: User): void {
  userToEdit.value = user
  formData.value = {
    name: user.name,
    email: user.email,
    userType: user.userType,
    faculty: user.faculty?.id || '',
  }
  showUserDialog.value = true
}

function addUser(): void {
  userToEdit.value = null
  formData.value = {
    name: '',
    email: '',
    userType: '',
    faculty: '',
  }
  showUserDialog.value = true
}

function confirmForcePasswordChange(user: User): void {
  userToForcePasswordChange.value = user
  showForcePasswordDialog.value = true
}

function forcePasswordChange(): void {
  if (userToForcePasswordChange.value) {
    // Implement your force password change logic here
    console.log('Force password change for user:', userToForcePasswordChange.value)
    showForcePasswordDialog.value = false
    userToForcePasswordChange.value = null
  }
}

function saveUser(): void {
  if (userToEdit.value) {
    // Update existing user
    const index = users.value.findIndex((u) => u.id === userToEdit.value!.id)
    if (index !== -1) {
      users.value[index] = {
        ...users.value[index],
        name: formData.value.name,
        email: formData.value.email,
        userType: formData.value.userType,
        faculty: formData.value.faculty
          ? faculties.value.find((f) => f.id === formData.value.faculty) || null
          : null,
      }
    }
  } else {
    // Add new user
    const newUser: User = {
      id: users.value.length + 1,
      name: formData.value.name,
      email: formData.value.email,
      userType: formData.value.userType,
      faculty: formData.value.faculty
        ? faculties.value.find((f) => f.id === formData.value.faculty) || null
        : null,
      createdAt: new Date().toISOString(),
    }
    users.value.push(newUser)
  }
  showUserDialog.value = false
  userToEdit.value = null
}
</script>

<template>
  <Layout>
    <div class="p-6 space-y-6">
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
            <SelectTrigger class="w-full md:w-[180px]">
              <SelectValue :placeholder="getUserTypeLabel(selectedUserType)" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="5">All User Types</SelectItem>
              <SelectItem value="0">Guest</SelectItem>
              <SelectItem value="1">Student</SelectItem>
              <SelectItem value="2">Marketing Coordinator</SelectItem>
              <SelectItem value="3">Marketing Manager</SelectItem>
              <SelectItem value="4">Admin</SelectItem>
            </SelectContent>
          </Select>

          <!-- Faculty Filter (conditional) -->
          <Select
            v-if="!['3', '4'].includes(selectedUserType)"
            :modelValue="selectedFaculty"
            @update:modelValue="updateFaculty"
          >
            <SelectTrigger class="w-full md:w-[180px]">
              <SelectValue :placeholder="getFacultyLabel(selectedFaculty)" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="0">All Faculties</SelectItem>
              <SelectItem v-for="faculty in faculties" :key="faculty.id" :value="faculty.id">
                {{ faculty.name }}
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
                  </div>
                </TableCell>
              </TableRow>
            </template>
            <template v-else>
              <TableRow v-if="users.length === 0">
                <TableCell :colspan="columns.length + 1" class="text-center">
                  No users found
                </TableCell>
              </TableRow>
              <TableRow v-for="user in users" :key="user.id">
                <TableCell class="pl-6">{{ user.id }}</TableCell>
                <TableCell>{{ user.name }}</TableCell>
                <TableCell>{{ user.email }}</TableCell>
                <TableCell>{{ getUserTypeName(user.userType) }}</TableCell>
                <TableCell>{{ user.faculty ? user.faculty.name : '-' }}</TableCell>
                <TableCell>{{ formatDate(user.createdAt) }}</TableCell>
                <TableCell>
                  <div class="flex space-x-2">
                    <Button variant="ghost" size="icon" @click="editUser(user)">
                      <Edit class="h-4 w-4" />
                    </Button>
                    <Button variant="ghost" size="icon" @click="confirmForcePasswordChange(user)">
                      <KeyRound class="h-4 w-4" />
                    </Button>
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
                    @click="handlePageChange(item.value)"
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
          <Card v-for="user in users" :key="user.id" class="p-4">
            <div class="space-y-2">
              <div class="flex justify-between items-start">
                <div>
                  <h3 class="font-medium">{{ user.name }}</h3>
                  <p class="text-sm text-muted-foreground">{{ user.email }}</p>
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
                  <p>{{ getUserTypeName(user.userType) }}</p>
                </div>
                <div>
                  <span class="text-muted-foreground">Faculty:</span>
                  <p>{{ user.faculty ? user.faculty.name : '-' }}</p>
                </div>
                <div class="col-span-2">
                  <span class="text-muted-foreground">Created:</span>
                  <p>{{ formatDate(user.createdAt) }}</p>
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
                    @click="handlePageChange(item.value)"
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
        <DialogContent>
          <DialogHeader>
            <DialogTitle>{{ userToEdit ? 'Edit User' : 'Add User' }}</DialogTitle>
            <DialogDescription>
              {{ userToEdit ? 'Update user information below.' : 'Fill in the user information below.' }}
            </DialogDescription>
          </DialogHeader>
          <div class="grid gap-4 py-4">
            <div class="grid gap-2">
              <Label for="name">Name</Label>
              <Input id="name" v-model="formData.name" placeholder="Enter name" />
            </div>
            <div class="grid gap-2">
              <Label for="email">Email</Label>
              <Input id="email" v-model="formData.email" type="email" placeholder="Enter email" />
            </div>
            <div class="grid gap-2">
              <Label for="userType">User Type</Label>
              <Select v-model="formData.userType">
                <SelectTrigger>
                  <SelectValue :placeholder="getUserTypeLabel(formData.userType)" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="0">Guest</SelectItem>
                  <SelectItem value="1">Student</SelectItem>
                  <SelectItem value="2">Marketing Coordinator</SelectItem>
                  <SelectItem value="3">Marketing Manager</SelectItem>
                  <SelectItem value="4">Admin</SelectItem>
                </SelectContent>
              </Select>
            </div>
            <div v-if="!['3', '4'].includes(formData.userType)" class="grid gap-2">
              <Label for="faculty">Faculty</Label>
              <Select v-model="formData.faculty">
                <SelectTrigger>
                  <SelectValue :placeholder="getFacultyLabel(formData.faculty)" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem v-for="faculty in faculties" :key="faculty.id" :value="faculty.id">
                    {{ faculty.name }}
                  </SelectItem>
                </SelectContent>
              </Select>
            </div>
          </div>
          <DialogFooter>
            <Button variant="outline" @click="showUserDialog = false">Cancel</Button>
            <Button @click="saveUser">{{ userToEdit ? 'Update' : 'Add' }}</Button>
          </DialogFooter>
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
            <Button @click="forcePasswordChange">Confirm</Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </div>
  </Layout>
</template>
