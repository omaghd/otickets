<script setup lang="ts">
import type Ticket from '@/types/Ticket'
import { ref, watch } from 'vue'

import Chart from 'vue3-apexcharts'

const props = defineProps<{ tickets: Ticket[] }>()

const series = ref([] as any[])

const chartOptions = ref({
  chart: {
    type: 'line',
    dropShadow: {
      enabled: true,
      color: '#000',
      top: 18,
      left: 7,
      blur: 10,
      opacity: 0.2
    }
  },
  colors: ['#0d9488'],
  dataLabels: {
    enabled: true
  },
  stroke: {
    curve: 'smooth'
  },
  yaxis: {
    labels: {
      formatter: (val: any) => val.toFixed(0)
    }
  },
  xaxis: {
    type: 'datetime'
  }
})

watch(
  () => props.tickets,
  (tickets) => {
    const ticketDates = tickets?.reduce((acc: any, ticket) => {
      const timestamp = new Date(ticket.created_at.split('T')[0]).getTime()
      if (acc[timestamp]) {
        acc[timestamp]++
      } else {
        acc[timestamp] = 1
      }
      return acc
    }, {})

    series.value = [
      {
        name: 'Tickets',
        data: Object.entries(ticketDates).map(([x, y]) => ({ x: Number(x), y }))
      }
    ]
  }
)
</script>

<template>
  <chart type="line" :height="300" :options="chartOptions" :series="series"></chart>
</template>
