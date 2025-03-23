<script setup lang="ts">
import { PencilIcon, PlusIcon, TrashIcon } from 'lucide-vue-next'
import { reactive, ref } from 'vue'

import { AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent, AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle } from '@/components/ui/alert-dialog'
import { Button } from '@/components/ui/button'
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card'
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog'
import { Input } from '@/components/ui/input'
import { Label } from '@/components/ui/label'
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table'

interface AcademicYear {
  id: number
  name: string
}

interface AcademicYearForm {
  id: number | null
  name: string
}

// Props and emits
const props = defineProps<{
  academicYears: AcademicYear[]
}>()

const emit = defineEmits<{
  (e: 'add', year: { name: string }): void
  (e: 'update', year: AcademicYear): void
  (e: 'delete', yearId: number): void
}>()

// Local state
const isModalOpen = ref<boolean>(false)
const isDeleteDialogOpen = ref<boolean>(false)
const editing = ref<AcademicYear | null>(null)
const yearToDelete = ref<AcademicYear | null>(null)

const form = reactive<AcademicYearForm>({
  id: null,
  name: '',
})

// Methods
const openModal = (year: AcademicYear | null): void => {
  if (year) {
    editing.value = year
    form.id = year.id
    form.name = year.name
  } else {
    editing.value = null
    form.id = null
    form.name = ''
  }
  isModalOpen.value = true
}

const saveAcademicYear = (): void => {
  if (!form.name.trim()) {
    // Handle validation
    return
  }

  if (editing.value && form.id !== null) {
    emit('update', { id: form.id, name: form.name })
  } else {
    emit('add', { name: form.name })
  }

  isModalOpen.value = false
}

const confirmDelete = (year: AcademicYear): void => {
  yearToDelete.value = year
  isDeleteDialogOpen.value = true
}

const deleteAcademicYear = (): void => {
  if (yearToDelete.value) {
    emit('delete', yearToDelete.value.id)
  }
  isDeleteDialogOpen.value = false
}
</script>
<template>
  <Card>
    <CardContent class="pt-6">
      <div class="flex justify-between mb-4">
        <h3 class="text-lg font-medium">Current Academic Years</h3>
        <Button @click="openModal(null)">
          <PlusIcon class="mr-2 h-4 w-4" /> Add Academic Year
        </Button>
      </div>

      <!-- Academic Years Table -->
      <div class="rounded-md border">
        <Table>
          <TableHeader>
            <TableRow>
              <TableHead>ID</TableHead>
              <TableHead>Name</TableHead>
              <TableHead class="text-right">Actions</TableHead>
            </TableRow>
          </TableHeader>
          <TableBody>
            <TableRow v-for="year in academicYears" :key="year.id">
              <TableCell>{{ year.id }}</TableCell>
              <TableCell>{{ year.name }}</TableCell>
              <TableCell class="text-right">
                <div class="flex justify-end gap-2">
                  <Button variant="ghost" size="icon" @click="openModal(year)">
                    <PencilIcon class="h-4 w-4" />
                  </Button>
                  <Button variant="ghost" size="icon" @click="confirmDelete(year)">
                    <TrashIcon class="h-4 w-4" />
                  </Button>
                </div>
              </TableCell>
            </TableRow>
            <TableRow v-if="academicYears.length === 0">
              <TableCell colspan="3" class="text-center py-6 text-gray-500">
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
    <DialogContent class="sm:max-w-[425px]">
      <DialogHeader>
        <DialogTitle>{{ editing ? 'Edit' : 'Add' }} Academic Year</DialogTitle>
        <DialogDescription> Enter the details for the academic year </DialogDescription>
      </DialogHeader>

      <div class="grid gap-4 py-4">
        <div class="grid gap-2">
          <Label for="yearName">Academic Year Name</Label>
          <Input id="yearName" v-model="form.name" placeholder="e.g. 2025-2026" />
        </div>
      </div>

      <DialogFooter>
        <Button variant="outline" @click="isModalOpen = false">Cancel</Button>
        <Button @click="saveAcademicYear">Save</Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>

  <!-- Delete Confirmation Modal -->
  <AlertDialog :open="isDeleteDialogOpen" @update:open="isDeleteDialogOpen = $event">
    <AlertDialogContent>
      <AlertDialogHeader>
        <AlertDialogTitle>Are you sure?</AlertDialogTitle>
        <AlertDialogDescription>
          This action cannot be undone. This will permanently delete this academic year and all
          associated submission dates.
        </AlertDialogDescription>
      </AlertDialogHeader>
      <AlertDialogFooter>
        <AlertDialogCancel @click="isDeleteDialogOpen = false">Cancel</AlertDialogCancel>
        <AlertDialogAction @click="deleteAcademicYear">Delete</AlertDialogAction>
      </AlertDialogFooter>
    </AlertDialogContent>
  </AlertDialog>
</template>
