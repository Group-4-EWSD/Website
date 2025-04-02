<script setup lang="ts">
import { ref, onMounted } from 'vue'
import { Bar } from 'vue-chartjs'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  BarElement,
  CategoryScale,
  LinearScale,
} from 'chart.js'
import Card from '@/components/ui/card/Card.vue'

ChartJS.register(Title, Tooltip, Legend, BarElement, CategoryScale, LinearScale)

const chartData = ref({
  labels: ['2020', '2021', '2022', '2023', '2024', '2025'],
  datasets: [
    {
      label: 'Participants',
      data: [70, 77, 83, 126, 151, 143],
      backgroundColor: ['#D1D5DB', '#9CA3AF', '#6B7280', '#374151', '#1F2937', '#111827'],
    },
  ],
})

const chartOptions = ref({
  responsive: true,
  maintainAspectRatio: false,
})
</script>

<template>
  <Card class="p-4">
    <h2 class="text-lg font-bold">Total Uploaded Articles as per Academic Year</h2>
    <div class="h-64">
      <Bar :data="chartData" :options="chartOptions" />
    </div>
  </Card>
</template>
