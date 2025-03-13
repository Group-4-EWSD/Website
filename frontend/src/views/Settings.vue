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
import { useUserStore } from '@/stores/user'

const showEditDialog = ref(false)

const userStore = useUserStore();
const user = userStore.currentUser!;

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
                <AvatarImage :src="userStore.profilePhotoUrl ?? ''" alt="@unovue" />
                <AvatarFallback class="text-white">{{
                  getAvatarFallBack(userStore.fullName)
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
              <SettingsRow label="Name" :value="user?.user_name" />
              <SettingsRow label="Nick Name" :value="user?.nickname" />
              <SettingsRow label="Email" :value="user?.user_email" />
              <SettingsRow label="Faculty" :value="user?.faculty_name" />
              <SettingsRow label="Date of Birth" :value="user?.date_of_birth" />
              <SettingsRow label="Gender" :value="user?.gender" />
              <SettingsRow label="Phone Number" :value="user?.phone_number" />
            </div>
          </CardContent>
        </Card>
      </div>
    </div>
  </Layout>
</template>
