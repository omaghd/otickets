<script setup lang="ts">
import type MinimalCount from '@/types/MinimalCount'

import { ref, watch } from 'vue'

import Chart from 'vue3-apexcharts'

const props = defineProps<{ ticketsCountsByStatus?: MinimalCount[] }>()

const series = ref([])

const chartOptions = ref({
  chart: { type: 'donut' },
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
  () => props.ticketsCountsByStatus,
  (ticketsCountsByStatus) => {
    if (!ticketsCountsByStatus) return

    series.value = ticketsCountsByStatus.slice(1).map((count) => Number(count.count)) as any

    chartOptions.value = {
      ...chartOptions.value,
      labels: ticketsCountsByStatus.slice(1).map((count) => count.name)
    }
  }
)
</script>

<template>
  <chart type="donut" :height="300" :options="chartOptions" :series="series"></chart>
</template>
