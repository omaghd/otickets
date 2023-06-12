<template>
  <div class="animate-pulse rounded-lg bg-white p-4 shadow">
    <div class="mb-8">
      <div class="h-10 w-full rounded bg-gray-200"></div>
      <div class="mt-2 h-3 w-24 rounded bg-gray-200"></div>
    </div>
    <div class="space-y-10">
      <div class="space-y-3" v-for="i in 4" :key="i">
        <div class="h-8 w-96 max-w-full rounded bg-gray-200"></div>

        <div v-for="i in 5" :key="i" class="h-4 w-full rounded bg-gray-200"></div>
        <div class="h-4 w-72 max-w-full rounded bg-gray-200"></div>
      </div>
    </div>
  </div>
</template>
