<script setup>
const members = [
  {
    id: 1,
    name: 'John William',
    role: 'Marketing Manager',
    avatar: 'https://randomuser.me/api/portraits/men/32.jpg',
  },
  {
    id: 2,
    name: 'John William',
    role: 'Marketing Manager',
    avatar: 'https://randomuser.me/api/portraits/men/31.jpg',
  },
  {
    id: 3,
    name: 'John William',
    role: 'Marketing Manager',
    avatar: 'https://randomuser.me/api/portraits/women/34.jpg',
  },
  {
    id: 4,
    name: 'John William',
    role: 'Marketing Manager',
    avatar: 'https://randomuser.me/api/portraits/women/35.jpg',
  },
  {
    id: 5,
    name: 'John William',
    role: 'Marketing Manager',
    avatar: 'https://randomuser.me/api/portraits/men/36.jpg',
  },
  {
    id: 6,
    name: 'John William',
    role: 'Marketing Manager',
    avatar: 'https://randomuser.me/api/portraits/women/37.jpg',
  },
  {
    id: 7,
    name: 'John William',
    role: 'Marketing Manager',
    avatar: 'https://randomuser.me/api/portraits/men/38.jpg',
  },
  {
    id: 8,
    name: 'John William',
    role: 'Marketing Manager',
    avatar: 'https://randomuser.me/api/portraits/women/39.jpg',
  },
]
</script>

<template>
  <div class="p-6 rounded-2xl">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-xl font-semibold">Aurora Members</h2>
      <a href="#" class="text-blue-600 hover:underline text-sm">View All</a>
    </div>
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
      <div
        v-for="member in members"
        :key="member.id"
        class="flex flex-col items-center text-center p-4 border rounded-xl shadow-sm"
      >
        <img :src="member.avatar" alt="Avatar" class="w-16 h-16 rounded-full mb-2 object-cover" />
        <div class="font-medium">{{ member.name }}</div>
        <div class="text-sm text-gray-500">{{ member.role }}</div>
      </div>
    </div>
  </div>
</template>
