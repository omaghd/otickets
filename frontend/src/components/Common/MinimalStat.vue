<script setup lang="ts">
import type MinimalCount from '@/types/MinimalCount'

defineProps<{ item: MinimalCount }>()
</script>

<template>
  <div class="overflow-hidden rounded-lg border bg-white px-4 py-5 shadow sm:p-6">
    <dt class="truncate text-sm font-medium text-gray-500">
      {{ $route.name == 'ClientTickets' ? item.name.replace(' Tickets', '') : item.name }}
    </dt>
    <dd class="mt-1 text-3xl font-semibold text-gray-900">
      {{ item.count }}
    </dd>
  </div>
</template>
