<template>
  <div class="animate-pulse divide-y rounded-lg border shadow">
    <div class="p-3">
      <div class="flex flex-wrap items-center justify-between gap-3">
        <div class="w-full flex-1">
          <div class="h-7 w-full rounded bg-gray-200"></div>
          <div class="mt-2 h-3 w-96 max-w-full rounded bg-gray-200"></div>
        </div>

        <div class="h-5 w-24 rounded-full bg-gray-200"></div>
      </div>
    </div>

    <div class="px-3 py-5">
      <div class="h-52 w-full rounded bg-gray-200"></div>
    </div>

    <div class="p-3">
      <div class="flex items-center gap-1 font-semibold text-gray-500">
        <div class="h-4 w-40 max-w-full rounded bg-gray-200"></div>
      </div>

      <div class="mt-3 flex flex-wrap gap-3">
        <div v-for="i in 4" :key="i">
          <div class="h-20 w-20 rounded-lg border bg-gray-200"></div>
        </div>
      </div>
    </div>
  </div>
</template>
