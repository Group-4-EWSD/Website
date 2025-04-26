<script setup lang="ts">
import {} from 'lucide-vue-next'
import { PencilIcon, LockIcon } from 'lucide-vue-next'
import { ref } from 'vue'

import ChangeProfileImageDialog from '@/components/pagespecific/settings/ChangeProfileImageDialog.vue'
import EditInformationDialog from '@/components/pagespecific/settings/EditInformationDialog.vue'
import PasswordChangeDialog from '@/components/pagespecific/settings/PasswordChangeDialog.vue'
import SettingsRow from '@/components/pagespecific/settings/SettingsRow.vue'
import ConfirmationDialog from '@/components/shared/ConfirmDialog.vue'
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import { Button } from '@/components/ui/button'
import { Card, CardContent } from '@/components/ui/card'
import Layout from '@/components/ui/Layout.vue'
import { useUserStore } from '@/stores/user'
import { GenderOptions } from '@/types/user'

const showEditDialog = ref(false)
const showRemoveDialog = ref(false)

const userStore = useUserStore()
// const user = userStore.currentUser!

const getAvatarFallBack = (name: string): string => {
  return name
    .split(' ')
    .map((n) => n.charAt(0))
    .join('')
    .slice(0, 2)
}

const handleRemovePhoto = () => {
  userStore.removeProfilePhoto()
  showRemoveDialog.value = false
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
                <AvatarImage :src="userStore.profilePhotoUrl ?? ''" alt="@unovue" />
                <AvatarFallback class="text-white">{{
                  getAvatarFallBack(userStore.fullName)
                }}</AvatarFallback>
              </Avatar>
              <div class="flex flex-row flex-wrap gap-2">
                <Button variant="outline" class="bg-accent hover:bg-secondary/80 hover:text-white" @click="showRemoveDialog = true" :disabled="!userStore.profilePhotoUrl">
                  Remove
                </Button>

                <ChangeProfileImageDialog />
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
                :initialData="userStore.currentUser"
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
              <SettingsRow label="Name" :value="userStore.currentUser?.user_name || ''" />
              <SettingsRow label="Nick Name" :value="userStore.currentUser?.nickname || ''" />
              <SettingsRow label="Email" :value="userStore.currentUser?.user_email || ''" />
              <SettingsRow v-if="userStore.currentUser?.user_type_name !== 'Admin' && userStore.currentUser?.user_type_name !== 'Marketing Manager'" label="Faculty" :value="userStore.currentUser?.faculty_name || ''" />
              <SettingsRow label="Date of Birth" :value="userStore.currentUser?.date_of_birth || ''" />
              <SettingsRow label="Gender" :value="GenderOptions[Number(userStore.currentUser?.gender || '3')]" />
              <SettingsRow label="Phone Number" :value="userStore.currentUser?.phone_number || ''" />
            </div>
          </CardContent>
        </Card>
        <Card>
          <CardContent class="py-6">
            <div class="flex items-center justify-between">
              <h2 class="md:text-lg font-semibold mb-4">Security</h2>

              <PasswordChangeDialog>
                <template #trigger>
                  <Button variant="secondary" class="hidden md:flex">
                    <LockIcon class="w-4 h-4 mr-2" />Change Password</Button
                  >
                  <Button variant="ghost" class="p-0 m-0 hover:bg-0 md:hidden">
                    <LockIcon class="w-4 h-4" />
                  </Button>
                </template>
              </PasswordChangeDialog>
            </div>
            <div class="flex flex-col">
              <SettingsRow label="Password" value="••••••••" />
            </div>
          </CardContent>
        </Card>
      </div>
    </div>

    <ConfirmationDialog
      v-model="showRemoveDialog"
      title="Confirmation"
      confirmButtonText="Confirm"
      cancelButtonText="Cancel"
      :confirmAction="handleRemovePhoto"
    >
      Are you sure to remove your profile picture?
    </ConfirmationDialog>
  </Layout>
</template>