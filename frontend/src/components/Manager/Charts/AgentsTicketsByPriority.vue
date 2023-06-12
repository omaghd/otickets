<script setup lang="ts">
import { ref, watch } from 'vue'

import Chart from 'vue3-apexcharts'

const props = defineProps<{ ticketsCountsByAgentAndPriority: any[] }>()

const series = ref([] as any[])

const chartOptions = ref({
  chart: {
    type: 'bar',
    stacked: true
  },
  theme: {
    monochrome: {
      enabled: true,
      color: '#0d9488'
    }
  },
  yaxis: {
    labels: {
      formatter: (val: number) => val?.toFixed(0)
    }
  },
  legend: {
    position: 'top'
  },
  plotOptions: {
    bar: {
      horizontal: false
    }
  }
} as any)

watch(
  () => props.ticketsCountsByAgentAndPriority,
  (ticketsCountsByAgentAndPriority) => {
    if (!ticketsCountsByAgentAndPriority) return

    const agents = ticketsCountsByAgentAndPriority.map((ticket) => ticket.name)

    chartOptions.value = {
      ...chartOptions.value,
      xaxis: { categories: agents }
    }

    const highData = ticketsCountsByAgentAndPriority.map((ticket) => ticket.high)
    const mediumData = ticketsCountsByAgentAndPriority.map((ticket) => ticket.medium)
    const lowData = ticketsCountsByAgentAndPriority.map((ticket) => ticket.low)

    series.value = [
      {
        name: 'High',
        data: highData
      },
      {
        name: 'Medium',
        data: mediumData
      },
      {
        name: 'Low',
        data: lowData
      }
    ]
  }
)
</script>

<template>
  <chart
    type="bar"
    v-if="series.length"
    :height="300"
    :options="chartOptions"
    :series="series"
  ></chart>
</template>
