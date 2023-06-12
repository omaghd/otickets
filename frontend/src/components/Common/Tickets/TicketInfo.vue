<script setup lang="ts">
import CalendarIcon from '@heroicons/vue/24/outline/CalendarIcon'
import Squares2X2Icon from '@heroicons/vue/24/outline/Squares2X2Icon'
import ClockIcon from '@heroicons/vue/24/outline/ClockIcon'

defineProps<{
  reference: string
  createdAt: string
  priority: string
  categoryName: string
  lastReplyOn?: string
}>()
</script>

<template>
  <div class="mt-1 flex space-x-2 divide-x text-xs">
    <div v-tooltip.bottom="'Reference'">#{{ reference }}</div>

    <div v-tooltip.bottom="'Date'" class="flex items-center gap-1 pl-2">
      <CalendarIcon class="h-4 w-4 text-gray-400" />
      <span>{{ createdAt }}</span>
    </div>

    <div
      v-tooltip.bottom="'Priority'"
      :class="{
        'text-red-500': priority === 'high',
        'text-orange-500': priority === 'medium'
      }"
      class="flex items-center gap-1 pl-2"
    >
      <div
        :class="{
          'bg-red-500': priority === 'high',
          'bg-orange-500': priority === 'medium',
          'bg-gray-400': priority === 'low'
        }"
        class="h-3 w-3 rounded-full"
      ></div>
      <span>{{ priority }}</span>
    </div>

    <div v-tooltip.bottom="'Category'" class="flex items-center gap-1 pl-2">
      <Squares2X2Icon class="h-4 w-4 text-gray-400" />
      <span>{{ categoryName }}</span>
    </div>

    <div v-tooltip.bottom="'Last reply'" class="flex items-center gap-1 pl-2">
      <ClockIcon class="h-4 w-4 text-gray-400" />
      <span>{{ lastReplyOn ?? '--' }}</span>
    </div>
  </div>
</template>
