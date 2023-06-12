<script setup lang="ts">
import { ref, watch } from 'vue'
import Chart from 'vue3-apexcharts'

const props = defineProps<{
  agentsResponseTime: any[]
}>()

const series = ref([] as any[])

const chartOptions = ref({
  chart: { type: 'bar' },
  colors: ['#0d9488'],
  plotOptions: {
    bar: {
      horizontal: false,
      columnWidth: '55%',
      endingShape: 'rounded'
    }
  },
  dataLabels: {
    enabled: false
  },
  stroke: {
    show: true,
    width: 2,
    colors: ['transparent']
  },
  fill: {
    opacity: 1
  },
  tooltip: {
    y: {
      formatter: function (val: any) {
        return val + ' hours'
      }
    }
  }
} as any)

watch(
  () => props.agentsResponseTime,
  (agentsResponseTime) => {
    if (!agentsResponseTime) return

    const agents = agentsResponseTime.map((agent) => agent.name)

    chartOptions.value = {
      ...chartOptions.value,
      xaxis: { categories: agents }
    }

    const data = agentsResponseTime.map((agent) => agent.value)

    series.value = [
      {
        name: 'Response Time',
        data: data
      }
    ]
  }
)
</script>

<template>
  <chart type="bar" :height="300" :options="chartOptions" :series="series"></chart>
</template>
