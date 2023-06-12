<script setup lang="ts">
import Modal from '@/components/Common/Modal.vue'
import Autocomplete from '@/components/Forms/Autocomplete.vue'
import ListBox from '@/components/Forms/ListBox.vue'
import PrimaryButton from '@/components/Forms/PrimaryButton.vue'

import useTickets from '@/composables/tickets/useTickets'

import type Category from '@/types/Category'
import type Option from '@/types/Option'
import type Ticket from '@/types/Ticket'

import { ref, onMounted, watch } from 'vue'

import { useToast } from 'vue-toastification'

const props = defineProps<{
  open: boolean
  ticketToEdit: Ticket
  tempCategories: Category[]
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'success'): void
}>()

const priorities = [
  { name: 'Low', value: 'low' },
  { name: 'Medium', value: 'medium' },
  { name: 'High', value: 'high' }
]

const priority = ref({} as Option)

const categories = ref([] as Option[])

const category = ref({} as Option)

watch(
  () => props.ticketToEdit,
  (newTicket) => {
    if (newTicket) {
      priority.value = priorities.find(
        (priority) => priority.value === newTicket.priority
      ) as Option

      category.value = categories.value.find(
        (category) => category.value === newTicket.category?.id.toString()
      ) as Option
    }
  }
)

watch(
  () => props.tempCategories,
  (newCategories) => {
    categories.value = newCategories.map((category: Category) => ({
      name: category.name,
      value: category.id.toString()
    }))
  }
)

const resetInput = ref(false)

const toast = useToast()

const { update, isLoading, isSuccess, errors, message } = useTickets()

const reset = () => {
  resetInput.value = true

  priority.value = {} as Option
  category.value = {} as Option
}

const onSubmit = async () => {
  await update(
    {
      priority: priority.value?.value,
      category_id: isNaN(+category.value?.value) ? null : +category.value?.value
    },
    props.ticketToEdit.id
  )

  if (isSuccess.value) {
    toast.success(message.value)

    reset()

    emit('success')

    emit('close')
  } else {
    toast.error(message.value)
  }
}
</script>

<template>
  <Modal :open="open" @close="$emit('close')" title="Edit Ticket" width-class="max-w-sm">
    <form @submit.prevent="onSubmit">
      <div class="p-6">
        <div class="space-y-6">
          <ListBox
            @update="(newPriority) => (priority = newPriority)"
            :selected="priority"
            label="Priority"
            :options="priorities"
            :errors="errors.priority"
            null-text="Select a priority"
          />

          <Autocomplete
            @update="(newCategory) => (category = newCategory)"
            :selected="category"
            label="Category"
            :options="categories"
            :errors="errors.category_id"
            null-text="Select a category"
          />
        </div>
      </div>

      <div class="mt-3 flex justify-end border-t bg-gray-50 px-6 py-3">
        <PrimaryButton type="submit" text="Update" :loading="isLoading" />
      </div>
    </form>
  </Modal>
</template>
