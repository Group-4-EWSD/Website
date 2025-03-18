<script setup lang="ts">
import { ref, computed, onMounted } from 'vue'
import dayjs from 'dayjs'
import {
  Search,
  Edit,
  Trash,
  ArrowUp,
  ArrowDown,
  ChevronLeft,
  ChevronRight,
  Loader2,
} from 'lucide-vue-next'
import Layout from '@/components/ui/Layout.vue'
import { Button } from '@/components/ui/button'
import { Input } from '@/components/ui/input'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'

import {
  Select,
  SelectTrigger,
  SelectContent,
  SelectValue,
  SelectItem,
} from '@/components/ui/select'

import { Card } from '@/components/ui/card'

import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog'
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
const sortColumn = ref('id')
const sortDirection = ref<SortDirection>('asc')
const showDeleteDialog = ref(false)
const userToDelete = ref<User | null>(null)

const columns: Column[] = [
  { key: 'id', label: 'ID' },
  { key: 'name', label: 'Name' },
  { key: 'email', label: 'Email' },
  { key: 'userType', label: 'User Type' },
  { key: 'faculty', label: 'Faculty' },
  { key: 'createdAt', label: 'Created At' },
]

// Sample data - replace with actual API call
onMounted(async () => {
  try {
    // Simulate API call
    await new Promise((resolve) => setTimeout(resolve, 500))

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

    isLoading.value = false
  } catch (error) {
    console.error('Error fetching users:', error)
    isLoading.value = false
  }
})

const filteredUsers = computed(() => {
  let result = [...users.value]

  // Apply user type filter
  if (selectedUserType.value) {
    if (selectedUserType.value !== '5') {
      result = result.filter((user) => user.userType === selectedUserType.value)
    }
  }

  // Apply faculty filter (only if applicable)
  if (selectedFaculty.value && !['3', '4'].includes(selectedUserType.value)) {
    result = result.filter((user) => user.faculty && user.faculty.id === selectedFaculty.value)
  }

  // Apply search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    result = result.filter(
      (user) => user.name.toLowerCase().includes(query) || user.email.toLowerCase().includes(query),
    )
  }

  // Apply sorting
  result.sort((a, b) => {
    let valueA: string | number
    let valueB: string | number

    if (sortColumn.value === 'faculty') {
      valueA = a.faculty ? a.faculty.name.toLowerCase() : ''
      valueB = b.faculty ? b.faculty.name.toLowerCase() : ''
    } else if (sortColumn.value === 'id') {
      valueA = a.id
      valueB = b.id
    } else {
      valueA =
        typeof a[sortColumn.value as keyof User] === 'string'
          ? (a[sortColumn.value as keyof User] as string).toLowerCase()
          : (a[sortColumn.value as keyof User] as number)
      valueB =
        typeof b[sortColumn.value as keyof User] === 'string'
          ? (b[sortColumn.value as keyof User] as string).toLowerCase()
          : (b[sortColumn.value as keyof User] as number)
    }

    if (valueA < valueB) return sortDirection.value === 'asc' ? -1 : 1
    if (valueA > valueB) return sortDirection.value === 'asc' ? 1 : -1
    return 0
  })

  return result
})

const totalPages = computed(() => {
  return Math.ceil(filteredUsers.value.length / pageSize.value)
})

const startIndex = computed(() => {
  return (currentPage.value - 1) * pageSize.value
})

const endIndex = computed(() => {
  return startIndex.value + pageSize.value
})

const paginatedUsers = computed(() => {
  return filteredUsers.value.slice(startIndex.value, endIndex.value)
})

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

function handleSearch(): void {
  currentPage.value = 1
}

function handleFilters(): void {
  currentPage.value = 1
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
  // Implement your edit logic here
  console.log('Edit user:', user)
}

function confirmDeleteUser(user: User): void {
  userToDelete.value = user
  showDeleteDialog.value = true
}

function deleteUser(): void {
  if (userToDelete.value) {
    // Implement your delete logic here
    console.log('Delete user:', userToDelete.value)
    users.value = users.value.filter((u) => u.id !== userToDelete.value!.id)
    showDeleteDialog.value = false
    userToDelete.value = null
  }
}
</script>

<template>
  <Layout>
    <div class="p-6 space-y-6">
      <div class="flex flex-col gap-4 md:flex-row md:items-center md:justify-between">
        <h1 class="text-2xl font-bold">Users Management</h1>
        <div class="flex flex-col gap-4 md:flex-row">
          <!-- Search Input -->
          <div class="relative">
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
            <SelectTrigger class="w-[180px]">
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
            <SelectTrigger class="w-[180px]">
              <SelectValue :placeholder="getFacultyLabel(selectedFaculty)" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="0">All Faculties</SelectItem>
              <SelectItem v-for="faculty in faculties" :key="faculty.id" :value="faculty.id">
                {{ faculty.name }}
              </SelectItem>
            </SelectContent>
          </Select>
        </div>
      </div>

      <!-- Users Table -->
      <Card>
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
            <TableRow v-if="isLoading">
              <TableCell :colspan="columns.length + 1" class="text-center">
                <div class="flex justify-center items-center">
                  <Loader2 class="h-4 w-4 mr-2 animate-spin" />
                  <span>Loading...</span>
                </div>
              </TableCell>
            </TableRow>
            <TableRow v-else-if="filteredUsers.length === 0">
              <TableCell :colspan="columns.length + 1" class="text-center">
                No users found
              </TableCell>
            </TableRow>
            <TableRow v-for="user in paginatedUsers" :key="user.id">
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
                  <Button variant="ghost" size="icon" @click="confirmDeleteUser(user)">
                    <Trash class="h-4 w-4" />
                  </Button>
                </div>
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>

        <!-- Pagination using Shadcn -->
        <div class="flex items-center justify-between px-6 py-4 border-t">
          <div class="text-sm text-muted-foreground">
            Showing {{ paginatedUsers.length > 0 ? startIndex + 1 : 0 }} to
            {{ Math.min(endIndex, filteredUsers.length) }} of {{ filteredUsers.length }} users
          </div>

        </div>
      </Card>

      <!-- Delete Confirmation Dialog -->
      <Dialog v-model:open="showDeleteDialog">
        <DialogContent>
          <DialogHeader>
            <DialogTitle>Delete User</DialogTitle>
            <DialogDescription>
              Are you sure you want to delete this user? This action cannot be undone.
            </DialogDescription>
          </DialogHeader>
          <DialogFooter>
            <Button variant="outline" @click="showDeleteDialog = false">Cancel</Button>
            <Button variant="destructive" @click="deleteUser">Delete</Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </div>
  </Layout>
</template>
