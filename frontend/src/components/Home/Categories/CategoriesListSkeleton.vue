<template>
  <div class="animate-pulse">
    <div class="mb-2 h-7 w-60 max-w-full rounded bg-gray-200"></div>

    <div class="divide-y overflow-hidden rounded-lg border bg-white">
      <div class="flex items-center justify-between gap-12 px-4 py-3" v-for="i in 8" :key="i">
        <div class="h-6 w-full rounded bg-gray-200"></div>
        <div class="">
          <div class="h-6 w-6 rounded-full bg-gray-200"></div>
        </div>
      </div>
    </div>
  </div>
</template>
