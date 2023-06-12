<script setup lang="ts">
defineProps<{
  headers: string[]
  actions?: {
    icon: any
    title: string
    function: Function
    className: string
  }[]
}>()
</script>

<template>
  <div class="overflow-x-auto rounded-lg border shadow">
    <div class="inline-block min-w-full align-middle">
      <div class="overflow-hidden">
        <table class="min-w-full divide-y divide-gray-300">
          <thead class="bg-gray-50 uppercase">
            <tr>
              <th
                v-for="(header, index) in headers"
                :key="index"
                scope="col"
                class="px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
              >
                {{ header }}
              </th>

              <th
                v-if="actions"
                scope="col"
                class="relative px-3 py-3.5 text-left text-sm font-semibold text-gray-900"
              >
                <span class="sr-only">Actions</span>
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 bg-white">
            <slot></slot>
          </tbody>
        </table>

        <slot name="pagination"></slot>
      </div>
    </div>
  </div>
</template>
