<script setup lang="ts">
import Modal from '@/components/Common/Modal.vue'
import FormInput from '@/components/Forms/FormInput.vue'
import Autocomplete from '@/components/Forms/Autocomplete.vue'
import PrimaryButton from '@/components/Forms/PrimaryButton.vue'

import type Option from '@/types/Option'
import type Category from '@/types/Category'

import useCategories from '@/composables/categories/useCategories'

import { ref, computed, watch } from 'vue'

import { useToast } from 'vue-toastification'

const props = defineProps<{
  open: boolean
  departments: Option[]
  categoryToEdit: Category
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'success', keepQuery: boolean): void
}>()

const name = ref('')
const slug = ref('')
const department = ref({} as Option)

const method = computed(() => (props.categoryToEdit.id ? 'put' : 'post'))

const resetInput = ref(false)

const title = computed(() => {
  if (props.categoryToEdit.id) {
    return 'Edit Category'
  } else {
    return 'New Category'
  }
})

const buttonText = computed(() => {
  if (props.categoryToEdit.id) {
    return 'Update'
  } else {
    return 'Create'
  }
})

const toast = useToast()

const { save, isLoading, isSuccess, errors, message } = useCategories()

const reset = () => {
  resetInput.value = true

  name.value = ''
  slug.value = ''
  department.value = {} as Option
}

const onSubmit = async () => {
  const departmentId = isNaN(Number(department.value?.value))
    ? null
    : Number(department.value?.value)
  await save(
    {
      name: name.value,
      slug: slug.value,
      department_id: departmentId
    },
    method.value,
    props.categoryToEdit.id ?? null
  )

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
  () => props.categoryToEdit,
  () => {
    if (props.categoryToEdit.id) {
      name.value = props.categoryToEdit.name
      slug.value = props.categoryToEdit.slug
    } else {
      reset()
    }
  }
)

watch(
  () => props.departments && props.categoryToEdit,
  () => {
    if (props.categoryToEdit.id) {
      department.value =
        props.departments.find(
          (department) => department.value === props.categoryToEdit.department?.id.toString()
        ) ?? ({} as Option)
    }
  }
)
</script>

<template>
  <Modal :open="open" @close="$emit('close')" :title="title">
    <form @submit.prevent="onSubmit">
      <div class="space-y-4 p-6">
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

        <FormInput
          class="w-full"
          id="slug"
          label="Slug"
          placeholder="Slug"
          type="text"
          :value="slug"
          @change="(value) => (slug = value)"
          :errors="errors.slug"
          :reset="resetInput"
          @reset="() => (resetInput = false)"
        />

        <Autocomplete
          null-text="Select a department"
          class="w-full"
          label="Department"
          :selected="department"
          :options="departments"
          @update="(value) => (department = value)"
          :errors="errors.department_id"
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
