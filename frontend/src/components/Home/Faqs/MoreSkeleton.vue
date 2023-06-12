<template>
  <div class="animate-pulse">
    <div class="mb-2 h-7 w-60 max-w-full rounded bg-gray-200"></div>

    <div class="divide-y overflow-hidden rounded-lg border bg-white">
      <div class="px-4 py-3" v-for="i in 10" :key="i">
        <div class="h-6 w-full rounded bg-gray-200"></div>
      </div>
    </div>
  </div>
</template>
