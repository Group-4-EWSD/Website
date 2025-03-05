<script setup lang="ts">
import {} from 'lucide-vue-next'
import { PencilIcon } from 'lucide-vue-next'
import { reactive, ref } from 'vue'

import EditInformationDialog from '@/components/pagespecific/settings/EditInformationDialog.vue'
import SettingsRow from '@/components/pagespecific/settings/SettingsRow.vue'
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import { Button } from '@/components/ui/button'
import { Card, CardContent } from '@/components/ui/card'
import Layout from '@/components/ui/Layout.vue'
import type { User } from '@/types/user'

const showEditDialog = ref(false)

// sample User data
const user = reactive<User>({
  name: 'Zar Li',
  nickName: 'Diana',
  email: 'zlmaw1@kmd.edu.mm',
  faculty: 'Art & Science',
  dateOfBirth: '01.Jan.2000',
  gender: 'Female',
  phoneNumber: '+95 091234 567809',
  photoUrl: null,
})

const getAvatarFallBack = (name: string): string => {
  return name
    .split(' ')
    .map((n) => n.charAt(0))
    .join('')
    .slice(0, 2)
}
</script>
<template>
  <Layout>
    <div class="mb-4">
      <div class="flex flex-col h-full gap-4">
        <h2 class="text-lg md:text-xl font-bold uppercase">Settings</h2>
        <Card>
          <CardContent class="py-6">
            <h2 class="md:text-lg font-semibold mb-4">Profile</h2>
            <div class="flex flex-row items-center gap-6">
              <Avatar size="medium">
                <AvatarImage :src="user.photoUrl ?? ''" alt="@unovue" />
                <AvatarFallback class="text-white">{{
                  getAvatarFallBack(user.name)
                }}</AvatarFallback>
              </Avatar>
              <div class="flex flex-row flex-wrap gap-2">
                <Button variant="outline" class="bg-accent hover:bg-secondary/80 hover:text-white"
                  >Remove</Button
                >
                <Button variant="secondary">Change</Button>
              </div>
            </div>
          </CardContent>
        </Card>
        <Card>
          <CardContent class="py-6">
            <div class="flex items-center justify-between">
              <h2 class="md:text-lg font-semibold mb-4">Personal Information</h2>

              <EditInformationDialog
                :isOpen="showEditDialog"
                :initialData="user"
                @update:isOpen="showEditDialog = $event"
              >
                <template #trigger>
                  <Button variant="secondary" class="hidden md:flex">
                    <PencilIcon class="w-4 h-4" />Edit</Button
                  >
                  <Button variant="ghost" class="p-0 m-0 hover:bg-0 md:hidden">
                    <PencilIcon class="w-4 h-4" />
                  </Button>
                </template>
              </EditInformationDialog>
            </div>
            <div class="flex flex-col">
              <SettingsRow label="Name" :value="user.name" />
              <SettingsRow label="Nick Name" :value="user.nickName" />
              <SettingsRow label="Email" :value="user.email" />
              <SettingsRow label="Faculty" :value="user.faculty" />
              <SettingsRow label="Date of Birth" :value="user.dateOfBirth" />
              <SettingsRow label="Gender" :value="user.gender" />
              <SettingsRow label="Phone Number" :value="user.phoneNumber" />
            </div>
          </CardContent>
        </Card>
      </div>

      <!-- Edit Dialog -->
      <!-- <Dialog :open="showDialog" @update:open="showDialog = $event">
      <DialogContent class="sm:max-w-[425px]">
        <DialogHeader>
          <DialogTitle>Edit {{ currentField }}</DialogTitle>
          <DialogDescription>
            Make changes to your {{ currentField.toLowerCase() }}. Click save when you're done.
          </DialogDescription>
        </DialogHeader>

        <div class="py-4">
          <div class="space-y-4">
            <div v-if="currentField !== 'gender'">
              <Label :for="currentField">{{ currentField }}</Label>
              <Input
                :id="currentField"
                v-model="editValue"
                :type="currentField === 'password' ? 'password' : 'text'"
                :placeholder="`Enter your ${currentField.toLowerCase()}`"
                class="w-full"
              />
            </div>
            <div v-else>
              <Label for="gender">Gender</Label>
              <Select v-model="editValue">
                <SelectTrigger id="gender">
                  <SelectValue placeholder="Select a gender" />
                </SelectTrigger>
                <SelectContent>
                  <SelectItem value="Male">Male</SelectItem>
                  <SelectItem value="Female">Female</SelectItem>
                  <SelectItem value="Other">Other</SelectItem>
                  <SelectItem value="Prefer not to say">Prefer not to say</SelectItem>
                </SelectContent>
              </Select>
            </div>
          </div>
        </div>

        <DialogFooter>
          <Button variant="outline" @click="showDialog = false">Cancel</Button>
          <Button @click="saveEdit">Save changes</Button>
        </DialogFooter>
      </DialogContent>
    </Dialog> -->
    </div>
  </Layout>
</template>
