import { defineStore } from 'pinia'

import { ref } from 'vue'

export const useDashboard = defineStore('dashboard', () => {
  const title = ref('')

  const setTitle = (newTitle: string) => (title.value = newTitle)

  return { title, setTitle }
})
