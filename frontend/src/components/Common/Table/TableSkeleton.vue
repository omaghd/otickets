<template>
  <div class="animate-pulse overflow-x-auto rounded-lg border shadow">
    <div class="inline-block min-w-full align-middle">
      <div class="overflow-hidden">
        <table class="min-w-full divide-y divide-gray-300">
          <thead class="bg-gray-50 uppercase">
            <tr>
              <th v-for="i in 5" :key="i" scope="col" class="py-5 px-3">
                <div class="h-3 w-28 max-w-full rounded bg-gray-200"></div>
              </th>
            </tr>
          </thead>
          <tbody class="divide-y divide-gray-200 bg-white">
            <tr v-for="i in 5" :key="i">
              <td v-for="i in 5" :key="i" class="px-3 py-8">
                <div class="h-3 w-28 max-w-full rounded bg-gray-200"></div>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</template>
