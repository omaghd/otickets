<script setup lang="ts">
import { ref, watch } from 'vue'

const props = defineProps<{
  dates: Date[]
}>()

const dates = ref(props.dates)

const today = new Date()
const thisYearStart = new Date(today.getFullYear(), 0, 1)
const thisMonthStart = new Date(today.getFullYear(), today.getMonth(), 1)
const lastMonthStart = new Date(today.getFullYear(), today.getMonth() - 1, 1)
const lastMonthEnd = new Date(today.getFullYear(), today.getMonth(), 0)
const lastYearStart = new Date(today.getFullYear() - 1, 0, 1)
const lastYearEnd = new Date(today.getFullYear() - 1, 11, 31)

const presetRanges = ref([
  { label: 'Today', range: [today, today] },
  { label: 'This month', range: [thisMonthStart, today] },
  { label: 'Last month', range: [lastMonthStart, lastMonthEnd] },
  { label: 'This year', range: [thisYearStart, today] },
  { label: 'Last year', range: [lastYearStart, lastYearEnd] }
])

const emit = defineEmits<{
  (event: 'update', value: Date[]): void
}>()

watch(dates, (value) => {
  emit('update', value)
})
</script>

<template>
  <div>
    <label class="mb-1 block text-sm font-medium text-gray-700">Date range</label>
    <VueDatePicker
      input-class-name="w-full cursor-default rounded-md transition-none border border-gray-300 bg-white py-2 pl-8 pr-10 text-left shadow-sm focus:border-teal-500 focus:outline-none focus:ring-1 focus:ring-teal-500 sm:text-sm"
      v-model="dates"
      placeholder="Select a date range"
      range
      six-weeks
      auto-apply
      :preset-ranges="presetRanges"
      :enable-time-picker="false"
      :month-change-on-scroll="false"
    >
      <template #yearly="{ label, range, presetDateRange }">
        <span @click="presetDateRange(range)">{{ label }}</span>
      </template>
    </VueDatePicker>
  </div>
</template>
