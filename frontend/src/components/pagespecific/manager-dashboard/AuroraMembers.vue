<script setup lang="ts">
import { computed, ref } from 'vue'

import type { AuroraMember } from '@/types/manager'
import { Avatar, AvatarFallback, AvatarImage } from '@/components/ui/avatar'
import { getInitials } from '@/lib/utils'

const props = defineProps<{
  members: AuroraMember[]
  isLoading: boolean
}>()

const showAll = ref(false)

const visibleMembers = computed(() => (showAll.value ? props.members : props.members.slice(0, 8)))
</script>

<template>
  <div class="p-6 rounded-2xl">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-xl font-semibold">Aurora Members</h2>
      <a href="#" class="text-blue-600 hover:underline text-sm" @click.prevent="showAll = !showAll">
        {{ showAll ? 'Show Less' : 'View All' }}
      </a>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
      <div
        v-for="member in visibleMembers"
        :key="member.user_name"
        class="flex flex-col items-center text-center p-4 border rounded-xl shadow-sm"
      >
        <Avatar>
          <AvatarImage :src="member.user_photo_path || ''" />
          <AvatarFallback class="text-white">{{
            getInitials(member.user_name || '')
          }}</AvatarFallback>
        </Avatar>
        <div class="font-medium">{{ member.user_name }}</div>
        <div class="text-sm text-gray-500">{{ member.user_type_name }}</div>
      </div>
    </div>
  </div>
</template>
