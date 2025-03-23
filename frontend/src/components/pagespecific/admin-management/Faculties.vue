<script setup lang="ts">
import { PencilIcon, PlusIcon, TrashIcon } from 'lucide-vue-next';
import { reactive, ref } from 'vue';

import { AlertDialog, AlertDialogAction, AlertDialogCancel, AlertDialogContent, AlertDialogDescription, AlertDialogFooter, AlertDialogHeader, AlertDialogTitle } from '@/components/ui/alert-dialog';
import { Button } from '@/components/ui/button';
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from '@/components/ui/card';
import { Dialog, DialogContent, DialogDescription, DialogFooter, DialogHeader, DialogTitle } from '@/components/ui/dialog';
import { Input } from '@/components/ui/input';
import { Label } from '@/components/ui/label';
import { Table, TableBody, TableCell, TableHead, TableHeader, TableRow } from '@/components/ui/table';

interface Faculty {
  id: number;
  name: string;
}

interface FacultyForm {
  id: number | null;
  name: string;
}

// Props and emits
const props = defineProps<{
  faculties: Faculty[]
}>();

const emit = defineEmits<{
  (e: 'add', faculty: { name: string }): void
  (e: 'update', faculty: Faculty): void
  (e: 'delete', facultyId: number): void
}>();

// Local state
const isModalOpen = ref<boolean>(false);
const isDeleteDialogOpen = ref<boolean>(false);
const editing = ref<Faculty | null>(null);
const facultyToDelete = ref<Faculty | null>(null);

const form = reactive<FacultyForm>({
  id: null,
  name: '',
});

// Methods
const openModal = (faculty: Faculty | null): void => {
  if (faculty) {
    editing.value = faculty;
    form.id = faculty.id;
    form.name = faculty.name;
  } else {
    editing.value = null;
    form.id = null;
    form.name = '';
  }
  isModalOpen.value = true;
};

const saveFaculty = (): void => {
  if (!form.name.trim()) {
    // Handle validation
    return;
  }
  
  if (editing.value && form.id !== null) {
    emit('update', { id: form.id, name: form.name });
  } else {
    emit('add', { name: form.name });
  }
  
  isModalOpen.value = false;
};

const confirmDelete = (faculty: Faculty): void => {
  facultyToDelete.value = faculty;
  isDeleteDialogOpen.value = true;
};

const deleteFaculty = (): void => {
  if (facultyToDelete.value) {
    emit('delete', facultyToDelete.value.id);
  }
  isDeleteDialogOpen.value = false;
};
</script>
<template>
  <Card>
    <CardContent class="pt-6">
      <div class="flex justify-between mb-4">
        <h3 class="text-lg font-medium">Current Faculties</h3>
        <Button @click="openModal(null)">
          <PlusIcon class="mr-2 h-4 w-4" /> Add Faculty
        </Button>
      </div>
      
      <!-- Faculties Table -->
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
            <TableRow v-for="faculty in faculties" :key="faculty.id">
              <TableCell>{{ faculty.id }}</TableCell>
              <TableCell>{{ faculty.name }}</TableCell>
              <TableCell class="text-right">
                <div class="flex justify-end gap-2">
                  <Button variant="ghost" size="icon" @click="openModal(faculty)">
                    <PencilIcon class="h-4 w-4" />
                  </Button>
                  <Button variant="ghost" size="icon" @click="confirmDelete(faculty)">
                    <TrashIcon class="h-4 w-4" />
                  </Button>
                </div>
              </TableCell>
            </TableRow>
            <TableRow v-if="faculties.length === 0">
              <TableCell colspan="3" class="text-center py-6 text-gray-500">
                No faculties found. Add one to get started.
              </TableCell>
            </TableRow>
          </TableBody>
        </Table>
      </div>
    </CardContent>
  </Card>
  
  <!-- Faculty Modal -->
  <Dialog :open="isModalOpen" @update:open="isModalOpen = $event">
    <DialogContent class="sm:max-w-[425px]">
      <DialogHeader>
        <DialogTitle>{{ editing ? 'Edit' : 'Add' }} Faculty</DialogTitle>
        <DialogDescription>
          Enter the details for the faculty
        </DialogDescription>
      </DialogHeader>
      
      <div class="grid gap-4 py-4">
        <div class="grid gap-2">
          <Label for="facultyName">Faculty Name</Label>
          <Input
            id="facultyName"
            v-model="form.name"
            placeholder="e.g. Computer Science"
          />
        </div>
      </div>
      
      <DialogFooter>
        <Button variant="outline" @click="isModalOpen = false">Cancel</Button>
        <Button @click="saveFaculty">Save</Button>
      </DialogFooter>
    </DialogContent>
  </Dialog>
  
  <!-- Delete Confirmation Modal -->
  <AlertDialog :open="isDeleteDialogOpen" @update:open="isDeleteDialogOpen = $event">
    <AlertDialogContent>
      <AlertDialogHeader>
        <AlertDialogTitle>Are you sure?</AlertDialogTitle>
        <AlertDialogDescription>
          This action cannot be undone. This will permanently delete this faculty.
        </AlertDialogDescription>
      </AlertDialogHeader>
      <AlertDialogFooter>
        <AlertDialogCancel @click="isDeleteDialogOpen = false">Cancel</AlertDialogCancel>
        <AlertDialogAction @click="deleteFaculty">Delete</AlertDialogAction>
      </AlertDialogFooter>
    </AlertDialogContent>
  </AlertDialog>
</template>