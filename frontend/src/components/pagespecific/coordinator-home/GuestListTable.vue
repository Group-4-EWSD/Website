<script setup lang="ts">
import Table from '@/components/ui/table/Table.vue'
import TableBody from '@/components/ui/table/TableBody.vue'
import TableCell from '@/components/ui/table/TableCell.vue'
import TableHead from '@/components/ui/table/TableHead.vue'
import TableHeader from '@/components/ui/table/TableHeader.vue'
import TableRow from '@/components/ui/table/TableRow.vue'
import Card from '@/components/ui/card/Card.vue'
import { ref } from 'vue'

const guests = ref([
  {
    name: 'Mary Lim',
    email: 'marylim@gmail.com',
    faculty: 'Business',
    phone: '+9591234 567809',
  },
  {
    name: 'Kelvin Edward',
    email: 'kelvined@gmail.com',
    faculty: 'Mathematics',
    phone: '+9591234 567809',
  },
  {
    name: 'Peter',
    email: 'peter@gmail.com',
    faculty: 'Physics',
    phone: '+9591234 567809',
  },
  {
    name: 'Peter',
    email: 'peter@gmail.com',
    faculty: 'Physics',
    phone: '+9591234 567809',
  },
  {
    name: 'Peter',
    email: 'peter@gmail.com',
    faculty: 'Physics',
    phone: '+9591234 567809',
  },
  {
    name: 'Peter',
    email: 'peter@gmail.com',
    faculty: 'Physics',
    phone: '+9591234 567809',
  },
  {
    name: 'Peter',
    email: 'peter@gmail.com',
    faculty: 'Physics',
    phone: '+9591234 567809',
  },
  {
    name: 'Peter',
    email: 'peter@gmail.com',
    faculty: 'Physics',
    phone: '+9591234 567809',
  },
  {
    name: 'Peter',
    email: 'peter@gmail.com',
    faculty: 'Physics',
    phone: '+9591234 567809',
  },
  {
    name: 'Peter',
    email: 'peter@gmail.com',
    faculty: 'Physics',
    phone: '+9591234 567809',
  },
  {
    name: 'Peter',
    email: 'peter@gmail.com',
    faculty: 'Physics',
    phone: '+9591234 567809',
  },
])
</script>

<template>
  <Card class="p-4">
    <h2 class="text-lg font-bold">Guest List</h2>

    <div class="max-h-[300px] overflow-auto">
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
          <TableRow v-for="guest in guests" :key="guest.email">
            <TableCell class="border px-2 py-2">{{ guest.name }}</TableCell>
            <TableCell class="border px-2 py-2 truncate max-w-[150px]">{{ guest.email }}</TableCell>
            <TableCell class="border px-2 py-2">{{ guest.faculty }}</TableCell>
            <TableCell class="border px-2 py-2">{{ guest.phone }}</TableCell>
          </TableRow>
        </TableBody>
      </Table>
    </div>
  </Card>
</template>
