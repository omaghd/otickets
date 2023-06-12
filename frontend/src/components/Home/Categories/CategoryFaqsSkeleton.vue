<template>
  <div v-for="i in 10" :key="i" class="animate-pulse">
    <div class="rounded-lg border bg-white p-4">
      <div class="h-6 w-full rounded bg-gray-200"></div>

      <div class="mt-2 h-4 w-52 max-w-full rounded bg-gray-200"></div>

      <article class="mt-4 h-11 w-full rounded bg-gray-200"></article>
    </div>
  </div>
</template>
