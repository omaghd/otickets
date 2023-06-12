<script setup lang="ts">
import type MinimalCount from '@/types/MinimalCount'
import { ref, watch } from 'vue'

import Chart from 'vue3-apexcharts'

const props = defineProps<{ ticketsCountsByCategory: MinimalCount[] }>()

const series = ref([])

const chartOptions = ref({
  chart: { type: 'pie' },
  theme: {
    monochrome: {
      enabled: true,
      color: '#0d9488'
    }
  },
  responsive: [
    {
      breakpoint: 480,
      options: {
        chart: {
          width: 200
        },
        legend: {
          position: 'bottom'
        }
      }
    }
  ]
} as any)

watch(
  () => props.ticketsCountsByCategory,
  (ticketsCountsByCategory) => {
    if (!ticketsCountsByCategory) return

    series.value = ticketsCountsByCategory.map((count) => Number(count.count)) as any

    chartOptions.value = {
      ...chartOptions.value,
      labels: ticketsCountsByCategory.map((count) => count.name)
    }
  }
)
</script>

<template>
  <chart type="pie" :height="300" :options="chartOptions" :series="series"></chart>
</template>
