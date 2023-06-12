import { defineStore } from 'pinia'

import { ref } from 'vue'

export const useSearchPalette = defineStore('searchPalette', () => {
  const isOpen = ref(false)

  const open = () => (isOpen.value = true)

  const close = () => (isOpen.value = false)

  return { isOpen, open, close }
})
