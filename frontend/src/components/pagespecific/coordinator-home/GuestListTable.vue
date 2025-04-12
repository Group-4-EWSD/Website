<script setup lang="ts">
import Card from '@/components/ui/card/Card.vue'
import Table from '@/components/ui/table/Table.vue'
import TableBody from '@/components/ui/table/TableBody.vue'
import TableCell from '@/components/ui/table/TableCell.vue'
import TableHead from '@/components/ui/table/TableHead.vue'
import TableHeader from '@/components/ui/table/TableHeader.vue'
import TableRow from '@/components/ui/table/TableRow.vue'
import type { GuestList } from '@/types/coordinator'

const props = defineProps<{
  guests: GuestList[]
  isLoading: boolean
}>()
</script>

<template>
  <Card class="p-4">
    <h2 class="text-lg font-bold">Guest List</h2>

    <div class="max-h-[400px] overflow-auto">
      <Table>
        <TableHeader class="bg-gray-100 text-gray-700 sticky top-0">
          <TableRow>
            <TableHead class="px-3 py-2">Guest Name</TableHead>
            <TableHead class="px-3 py-2">Email</TableHead>
            <TableHead class="px-3 py-2">Faculty</TableHead>
            <TableHead class="px-3 py-2">Phone Number</TableHead>
          </TableRow>
        </TableHeader>
        <TableBody>
          <template v-if="isLoading">
            <TableRow v-for="index in 5" :key="index">
              <TableCell class="border px-2 py-2">
                <div class="h-4 bg-gray-200 rounded w-3/4 animate-pulse"></div>
              </TableCell>
              <TableCell class="border px-2 py-2">
                <div class="h-4 bg-gray-200 rounded w-full animate-pulse"></div>
              </TableCell>
              <TableCell class="border px-2 py-2">
                <div class="h-4 bg-gray-200 rounded w-2/3 animate-pulse"></div>
              </TableCell>
              <TableCell class="border px-2 py-2">
                <div class="h-4 bg-gray-200 rounded w-1/2 animate-pulse"></div>
              </TableCell>
            </TableRow>
          </template>
          <template v-else>
            <TableRow v-for="guest in guests" :key="guest.user_email">
              <TableCell class="border px-2 py-2">{{ guest.user_name }}</TableCell>
              <TableCell class="border px-2 py-2 truncate max-w-[150px]">{{
                guest.user_email
              }}</TableCell>
              <TableCell class="border px-2 py-2">{{ guest.faculty_name }}</TableCell>
              <TableCell class="border px-2 py-2">{{ guest.phone_number }}</TableCell>
            </TableRow>
          </template>
        </TableBody>
      </Table>
    </div>
  </Card>
</template>
