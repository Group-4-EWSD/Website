<script setup lang="ts">
import dayjs from 'dayjs'
import { Search } from 'lucide-vue-next'
import { computed, onMounted, ref, watch } from 'vue'

import { Card } from '@/components/ui/card'
import { Input } from '@/components/ui/input'
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
import { Button } from '@/components/ui/button'
import { Skeleton } from '@/components/ui/skeleton'
import {
  Table,
  TableBody,
  TableCell,
  TableHead,
  TableHeader,
  TableRow,
} from '@/components/ui/table'
import {
  Dialog,
  DialogContent,
  DialogDescription,
  DialogFooter,
  DialogHeader,
  DialogTitle,
} from '@/components/ui/dialog'
import { getContactUsData } from '@/api/system-data'
import type { ContactUsInfo } from '@/types/system-data'

interface Column {
  key: string
  label: string
}

// Component state
const searchQuery = ref('')
const allContacts = ref<ContactUsInfo[]>([])
const displayedContacts = ref<ContactUsInfo[]>([])
const isLoading = ref(true)
const currentPage = ref(1)
const pageSize = ref(10)
const totalItems = ref(0)
const showDetailsDialog = ref(false)
const selectedContact = ref<ContactUsInfo | null>(null)

const columns: Column[] = [
  { key: 'visitor_name', label: 'Name' },
  { key: 'visitor_email', label: 'Email' },
  { key: 'title', label: 'Title' },
  { key: 'description', label: 'Description' },
  { key: 'created_at', label: 'Date' },
]

// Fetch all contact us messages
async function fetchContacts() {
  try {
    isLoading.value = true
    const data = await getContactUsData()
    allContacts.value = data
    totalItems.value = data.length
    applyFiltersAndPagination()
  } catch (error) {
    console.error('Error fetching contact messages:', error)
  } finally {
    isLoading.value = false
  }
}

// Apply search filter and pagination to the data
function applyFiltersAndPagination() {
  let filteredData = [...allContacts.value]

  // Apply search filter
  if (searchQuery.value) {
    const query = searchQuery.value.toLowerCase()
    filteredData = filteredData.filter(
      (contact) =>
        contact.visitor_name.toLowerCase().includes(query) ||
        contact.visitor_email.toLowerCase().includes(query) ||
        contact.title.toLowerCase().includes(query) ||
        contact.description.toLowerCase().includes(query)
    )
  }

  totalItems.value = filteredData.length

  // Apply pagination
  const start = (currentPage.value - 1) * pageSize.value
  const end = start + pageSize.value
  displayedContacts.value = filteredData.slice(start, end)
}

// Initialize data
onMounted(async () => {
  try {
    await fetchContacts()
  } catch (error) {
    console.error('Error initializing data:', error)
  }
})

// Watch for changes to search query
watch(searchQuery, () => {
  currentPage.value = 1 // Reset to first page when search changes
  applyFiltersAndPagination()
})

// Watch for page changes
watch(currentPage, () => {
  applyFiltersAndPagination()
})

const totalPages = computed(() => {
  return Math.ceil(totalItems.value / pageSize.value)
})

function handlePageChange(page: number) {
  currentPage.value = page
}

function handleSearch() {
  applyFiltersAndPagination()
}

function showContactDetails(contact: ContactUsInfo) {
  selectedContact.value = contact
  showDetailsDialog.value = true
}

function formatDate(dateString: string | null): string {
  if (!dateString) return '-'
  return dayjs(dateString).format('MMM D, YYYY h:mm A')
}

function truncateText(text: string, maxLength: number = 50): string {
  if (!text) return '-'
  if (text.length <= maxLength) return text
  return text.substring(0, maxLength) + '...'
}
</script>

<template>
  <Layout>
    <div class="space-y-6">
      <!-- Header and Search -->
      <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <h1 class="text-2xl font-bold">Contact Us Messages</h1>
        <div class="relative w-full sm:w-auto">
          <Input
            v-model="searchQuery"
            placeholder="Search messages..."
            class="w-full sm:w-80"
            @update:model-value="handleSearch"
          >
            <template #prefix>
              <Search class="h-4 w-4 text-muted-foreground" />
            </template>
          </Input>
        </div>
      </div>

      <!-- Table View -->
      <Card>
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead v-for="column in columns" :key="column.key">
                {{ column.label }}
              </TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <template v-if="isLoading">
              <TableRow v-for="n in pageSize" :key="n">
                <TableCell v-for="column in columns" :key="column.key">
                  <Skeleton class="h-4 my-4 w-full" />
                </TableCell>
              </TableRow>
            </template>
            <template v-else>
              <TableRow v-if="displayedContacts.length === 0">
                <TableCell :colspan="columns.length" class="text-center">
                  No messages found
                </TableCell>
              </TableRow>
              <TableRow 
                v-for="contact in displayedContacts" 
                :key="contact.contact_us_id" 
                class="cursor-pointer hover:bg-muted/50"
                @click="showContactDetails(contact)"
              >
                <TableCell class="py-4">{{ contact.visitor_name }}</TableCell>
                <TableCell>{{ contact.visitor_email }}</TableCell>
                <TableCell>{{ contact.title }}</TableCell>
                <TableCell>
                  <div class="max-w-sm">{{ truncateText(contact.description) }}</div>
                </TableCell>
                <TableCell>{{ formatDate(contact.created_at) }}</TableCell>
              </TableRow>
            </template>
          </TableBody>
        </Table>

        <!-- Pagination -->
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between px-6 py-4 border-t">
          <div class="text-sm text-muted-foreground text-center sm:text-left mb-4 sm:mb-0">
            Showing {{ (currentPage - 1) * pageSize + 1 }} to
            {{ Math.min(currentPage * pageSize, totalItems) }} of {{ totalItems }} messages
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

      <!-- Contact Details Dialog -->
      <Dialog v-model:open="showDetailsDialog">
        <DialogContent class="sm:max-w-lg">
          <DialogHeader>
            <DialogTitle>Message Details</DialogTitle>
            <DialogDescription>
              Received on {{ selectedContact ? formatDate(selectedContact.created_at) : '' }}
            </DialogDescription>
          </DialogHeader>
          
          <div v-if="selectedContact" class="py-4 space-y-4">
            <div class="grid grid-cols-4 gap-4">
              <div class="font-medium text-sm">Name:</div>
              <div class="col-span-3 text-sm">{{ selectedContact.visitor_name }}</div>
              
              <div class="font-medium text-sm">Email:</div>
              <div class="col-span-3 text-sm">{{ selectedContact.visitor_email }}</div>
              
              <div class="font-medium text-sm">Title:</div>
              <div class="col-span-3 text-sm">{{ selectedContact.title }}</div>
              
            </div>
            
            <div>
              <div class="font-medium text-sm mb-2">Message:</div>
              <div class="p-3 bg-muted rounded-md whitespace-pre-wrap">{{ selectedContact.description }}</div>
            </div>
          </div>
          
          <DialogFooter>
            <Button @click="showDetailsDialog = false">Close</Button>
          </DialogFooter>
        </DialogContent>
      </Dialog>
    </div>
  </Layout>
</template>