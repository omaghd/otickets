<script setup lang="ts">
import Modal from '@/components/Common/Modal.vue'
import FormInput from '@/components/Forms/FormInput.vue'
import PrimaryButton from '@/components/Forms/PrimaryButton.vue'
import FormLabel from '@/components/Forms/FormLabel.vue'
import Errors from '@/components/Forms/Errors.vue'

import type Client from '@/types/Client'

import useClients from '@/composables/clients/useClients'

import { ref, computed, watch } from 'vue'

import { useToast } from 'vue-toastification'

const props = defineProps<{
  open: boolean
  clientToEdit: Client
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'success', keepQuery: boolean): void
}>()

const name = ref('')
const email = ref('')
const phone = ref('')
const password = ref('')
const picture = ref({} as File | null)

const method = computed(() => (props.clientToEdit.id ? 'put' : 'post'))

const API_URL = import.meta.env.VITE_API_URL

const defaultImagePath = API_URL + 'storage/examples/user.jpg'

const previewImage = ref(defaultImagePath)

const onFileChange = (e: any) => {
  const file = e.target.files[0]
  previewImage.value = URL.createObjectURL(file)
  picture.value = file
}

const resetInput = ref(false)

const title = computed(() => {
  if (props.clientToEdit.id) {
    return 'Edit Client'
  } else {
    return 'New Client'
  }
})

const buttonText = computed(() => {
  if (props.clientToEdit.id) {
    return 'Update'
  } else {
    return 'Create'
  }
})

const toast = useToast()

const { save, isLoading, isSuccess, errors, message } = useClients()

const reset = () => {
  resetInput.value = true

  name.value = ''
  email.value = ''
  phone.value = ''
  password.value = ''
  picture.value = {} as File | null
  previewImage.value = defaultImagePath
}

const onSubmit = async () => {
  await save(
    {
      name: name.value,
      email: email.value,
      phone: phone.value,
      password: password.value,
      picture: picture.value
    },
    method.value,
    props.clientToEdit.id ?? null
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
  () => props.clientToEdit,
  () => {
    if (props.clientToEdit.id) {
      name.value = props.clientToEdit.name
      email.value = props.clientToEdit.email
      phone.value = props.clientToEdit.phone
      previewImage.value = API_URL + props.clientToEdit.picture
    } else {
      reset()
    }
  }
)
</script>

<template>
  <Modal :open="open" @close="$emit('close')" :title="title">
    <form @submit.prevent="onSubmit">
      <div class="space-y-4 p-6">
        <div class="flex flex-col gap-4 sm:flex-row">
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

        <div class="flex flex-col gap-4 sm:flex-row">
          <FormInput
            class="w-full"
            id="phone"
            label="Phone"
            placeholder="Phone"
            type="tel"
            :value="phone"
            @change="(value) => (phone = value)"
            :errors="errors.phone"
            :reset="resetInput"
            @reset="() => (resetInput = false)"
          />

          <FormInput
            class="w-full"
            id="password"
            label="Password"
            placeholder="Password"
            type="password"
            @change="(value) => (password = value)"
            :errors="errors.password"
            :reset="resetInput"
            @reset="() => (resetInput = false)"
          />
        </div>

        <div>
          <FormLabel text="Picture" id="profile-picture" />

          <div class="flex flex-wrap items-center gap-3">
            <img
              class="relative h-20 w-20 rounded-full border object-cover"
              :src="previewImage"
              alt="Profile Picture"
            />

            <div>
              <label
                for="profile-picture"
                class="cursor-pointer rounded-md border border-gray-300 py-2 px-3 text-sm font-medium text-gray-700 hover:bg-gray-100"
              >
                Change
              </label>

              <input
                ref="temp"
                @change="onFileChange"
                type="file"
                id="profile-picture"
                class="hidden"
              />
            </div>
          </div>

          <Errors v-if="errors.picture" :errors="errors.picture" />
        </div>
      </div>

      <div class="mt-3 flex justify-end border-t bg-gray-50 px-6 py-3">
        <PrimaryButton type="submit" :text="buttonText" :loading="isLoading" />
      </div>
    </form>
  </Modal>
</template>
