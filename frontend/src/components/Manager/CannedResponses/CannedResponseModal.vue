<script setup lang="ts">
import Modal from '@/components/Common/Modal.vue'
import FormInput from '@/components/Forms/FormInput.vue'
import Autocomplete from '@/components/Forms/Autocomplete.vue'
import PrimaryButton from '@/components/Forms/PrimaryButton.vue'
import FormTextarea from '@/components/Forms/FormTextarea.vue'

import type Option from '@/types/Option'
import type CannedResponse from '@/types/CannedResponse'

import useCannedResponses from '@/composables/canned-responses/useCannedResponses'

import { ref, computed, watch } from 'vue'

import { useToast } from 'vue-toastification'

const props = defineProps<{
  open: boolean
  agents: Option[]
  categories: Option[]
  cannedResponseToEdit: CannedResponse
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'success', keepQuery: boolean): void
}>()

const title = ref('')
const content = ref('')
const agent = ref({} as Option)
const category = ref({} as Option)

const method = computed(() => (props.cannedResponseToEdit.id ? 'put' : 'post'))

const resetInput = ref(false)

const modalTitle = computed(() => {
  if (props.cannedResponseToEdit.id) {
    return 'Edit Canned Response'
  } else {
    return 'New Canned Response'
  }
})

const buttonText = computed(() => {
  if (props.cannedResponseToEdit.id) {
    return 'Update'
  } else {
    return 'Create'
  }
})

const toast = useToast()

const { save, isLoading, isSuccess, errors, message } = useCannedResponses()

const reset = () => {
  resetInput.value = true

  title.value = ''
  content.value = ''
  agent.value = {} as Option
  category.value = {} as Option
}

const onSubmit = async () => {
  const agentId = isNaN(Number(agent.value?.value)) ? null : Number(agent.value?.value)
  const categoryId = isNaN(Number(category.value?.value)) ? null : Number(category.value?.value)
  await save(
    {
      title: title.value,
      content: content.value,
      agent_id: agentId,
      category_id: categoryId
    },
    method.value,
    props.cannedResponseToEdit.id ?? null
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
  () => props.cannedResponseToEdit,
  () => {
    if (props.cannedResponseToEdit.id) {
      title.value = props.cannedResponseToEdit.title
      content.value = props.cannedResponseToEdit.content
    } else {
      reset()
    }
  }
)

watch(
  () => props.agents && props.cannedResponseToEdit,
  () => {
    if (props.cannedResponseToEdit.id) {
      agent.value =
        props.agents.find(
          (agent) => agent.value === props.cannedResponseToEdit.agent?.id.toString()
        ) ?? ({} as Option)
    }
  }
)

watch(
  () => props.categories && props.cannedResponseToEdit,
  () => {
    if (props.cannedResponseToEdit.id) {
      category.value =
        props.categories.find(
          (category) => category.value === props.cannedResponseToEdit.category?.id.toString()
        ) ?? ({} as Option)
    }
  }
)
</script>

<template>
  <Modal :open="open" @close="$emit('close')" :title="modalTitle">
    <form @submit.prevent="onSubmit">
      <div class="space-y-4 p-6">
        <FormInput
          class="w-full"
          id="title"
          label="Title"
          placeholder="Title"
          type="text"
          :value="title"
          @change="(value) => (title = value)"
          :errors="errors.title"
          :reset="resetInput"
          @reset="() => (resetInput = false)"
        />

        <FormTextarea
          id="content"
          label="Content"
          placeholder="Content"
          :value="content"
          @change="(value) => (content = value)"
          :errors="errors.content"
        />

        <Autocomplete
          null-text="Select a category"
          class="w-full"
          label="Category"
          :selected="category"
          :options="categories"
          @update="(value) => (category = value)"
          :errors="errors.category_id"
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
