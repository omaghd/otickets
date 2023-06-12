<script setup lang="ts">
import Modal from '@/components/Common/Modal.vue'
import FormInput from '@/components/Forms/FormInput.vue'
import PrimaryButton from '@/components/Forms/PrimaryButton.vue'

import type Department from '@/types/Department'

import useDepartments from '@/composables/departments/useDepartments'

import { ref, computed, watch } from 'vue'

import { useToast } from 'vue-toastification'

const props = defineProps<{
  open: boolean
  departmentToEdit: Department
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'success', keepQuery: boolean): void
}>()

const name = ref('')

const method = computed(() => (props.departmentToEdit.id ? 'put' : 'post'))

const resetInput = ref(false)

const title = computed(() => {
  if (props.departmentToEdit.id) {
    return 'Edit Department'
  } else {
    return 'New Department'
  }
})

const buttonText = computed(() => {
  if (props.departmentToEdit.id) {
    return 'Update'
  } else {
    return 'Create'
  }
})

const toast = useToast()

const { save, isLoading, isSuccess, errors, message } = useDepartments()

const reset = () => {
  resetInput.value = true

  name.value = ''
}

const onSubmit = async () => {
  await save({ name: name.value }, method.value, props.departmentToEdit.id ?? null)

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
  () => props.departmentToEdit,
  () => {
    if (props.departmentToEdit.id) {
      name.value = props.departmentToEdit.name
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
          id="name"
          label="Name"
          placeholder="Name"
          type="text"
          :value="name"
          @change="(value) => (name = value)"
          :errors="errors.name"
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
