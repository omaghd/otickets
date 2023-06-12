<script setup lang="ts">
import FormLabel from './FormLabel.vue'

import { ref, watch } from 'vue'

import processHTML from '@/util/processHTML'

const props = defineProps<{
  label?: string
  placeholder: string
  value?: string
  errors?: Array<string>
  resetContent?: boolean
  headings?: boolean
}>()

const emit = defineEmits<{
  (event: 'change', value: string): void
}>()

const editor = ref()

const value = ref(props.value ?? '')

const defaultToolbar = ['bold', 'italic', 'underline', { list: 'ordered' }, { list: 'bullet' }]
const toolbar = props.headings
  ? [{ header: [1, 2, 3, 4, 5, 6, false] }, ...defaultToolbar]
  : defaultToolbar

watch(value, (newValue) => {
  emit('change', processHTML(newValue))
})

watch(
  () => props.resetContent,
  () => {
    if (props.resetContent) {
      editor.value.setHTML('')
    }
  }
)
</script>

<template>
  <div>
    <FormLabel v-if="label" :text="label" />

    <QuillEditor
      ref="editor"
      theme="snow"
      :placeholder="placeholder"
      contentType="html"
      :toolbar="toolbar"
      v-model:content="value"
    />

    <ul v-if="errors?.length">
      <li v-for="error in errors" :key="error" class="mt-1 text-sm text-red-600">
        {{ error }}
      </li>
    </ul>
  </div>
</template>
