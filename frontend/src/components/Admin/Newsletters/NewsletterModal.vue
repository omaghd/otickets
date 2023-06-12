<script setup lang="ts">
import Modal from '@/components/Common/Modal.vue'
import FormInput from '@/components/Forms/FormInput.vue'
import PrimaryButton from '@/components/Forms/PrimaryButton.vue'

import type Newsletter from '@/types/Newsletter'

import useNewsletters from '@/composables/newsletters/useNewsletters'

import { ref, computed, watch } from 'vue'

import { useToast } from 'vue-toastification'

const props = defineProps<{
  open: boolean
  newsletterToEdit: Newsletter
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'success', keepQuery: boolean): void
}>()

const email = ref('')

const method = computed(() => (props.newsletterToEdit.id ? 'put' : 'post'))

const resetInput = ref(false)

const title = computed(() => {
  if (props.newsletterToEdit.id) {
    return 'Edit Newsletter'
  } else {
    return 'New Newsletter'
  }
})

const buttonText = computed(() => {
  if (props.newsletterToEdit.id) {
    return 'Update'
  } else {
    return 'Create'
  }
})

const toast = useToast()

const { save, isLoading, isSuccess, errors, message } = useNewsletters()

const reset = () => {
  resetInput.value = true

  email.value = ''
}

const onSubmit = async () => {
  await save(email.value, method.value, props.newsletterToEdit.id ?? null)

  if (isSuccess.value) {
    toast.success(message.value)

    reset()

    if (method.value === 'post') emit('success', false)
    else emit('success', true)

    emit('close')
  } else {
    toast.error(message.value)
  }
}

watch(
  () => props.newsletterToEdit,
  () => {
    if (props.newsletterToEdit.id) {
      email.value = props.newsletterToEdit.email
    } else {
      reset()
    }
  }
)
</script>

<template>
  <Modal :open="open" @close="$emit('close')" :title="title">
    <form @submit.prevent="onSubmit">
      <div class="p-6">
        <FormInput
          class="w-full"
          id="email"
          label="Email"
          placeholder="Email"
          type="email"
          :value="email"
          @change="(value) => (email = value)"
          :errors="errors.email"
          :reset="resetInput"
          @reset="() => (resetInput = false)"
        />
      </div>

      <div class="mt-3 flex justify-end border-t bg-gray-50 px-6 py-3">
        <PrimaryButton type="submit" :text="buttonText" :loading="isLoading" />
      </div>
    </form>
  </Modal>
</template>
