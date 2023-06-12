<script setup lang="ts">
import FormLabel from './FormLabel.vue'

import vueFilePond from 'vue-filepond'

import FilePondPluginFileValidateType from 'filepond-plugin-file-validate-type'
import FilePondPluginImagePreview from 'filepond-plugin-image-preview'

import 'filepond/dist/filepond.min.css'
import 'filepond-plugin-image-preview/dist/filepond-plugin-image-preview.min.css'

import { ref, watch } from 'vue'

const FilePond = vueFilePond(FilePondPluginFileValidateType, FilePondPluginImagePreview)

const props = defineProps<{
  label: string
  multiple: boolean
  errors?: string[]
  resetFiles?: boolean
}>()

const files = ref([] as any)

const emit = defineEmits<{
  (event: 'change', files: File[]): void
}>()

const change = (files: any) => {
  const newFiles = files.map((file: any) => file.file)

  emit('change', newFiles as File[])
}

watch(
  () => props.resetFiles,
  () => {
    if (props.resetFiles) {
      files.value = []
    }
  }
)
</script>

<template>
  <div>
    <FormLabel text="Attachments" />

    <file-pond
      @updatefiles="change"
      :label-idle="label"
      :allow-multiple="multiple"
      :accepted-file-types="['image/*']"
      check-validity
      :files="files"
    />

    <ul v-if="errors?.length">
      <li v-for="error in errors" :key="error" class="mt-1 text-sm text-red-600">
        {{ error }}
      </li>
    </ul>
  </div>
</template>
