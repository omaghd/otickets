<script setup lang="ts">
import FormInput from '@/components/Forms/FormInput.vue'
import PrimaryButton from '@/components/Forms/PrimaryButton.vue'
import FormLabel from '@/components/Forms/FormLabel.vue'

import UserIcon from '@heroicons/vue/24/outline/UserIcon'
import PhoneIcon from '@heroicons/vue/24/outline/PhoneIcon'
import EnvelopeIcon from '@heroicons/vue/24/outline/EnvelopeIcon'

import { useAuthStore } from '@/stores/auth'
import { storeToRefs } from 'pinia'

import { ref, computed } from 'vue'

import { useToast } from 'vue-toastification'

import { onBeforeRouteLeave } from 'vue-router'

defineProps<{
  formClass?: string
  actionClass?: string
}>()

const store = useAuthStore()
const { user, isSuccess, message, errors, isLoading } = storeToRefs(store)

const API_URL = import.meta.env.VITE_API_URL

const picture = computed(() => API_URL + (user.value.picture ?? 'storage/examples/user.jpg'))

const name = ref(user.value.name ?? '')
const phone = ref(user.value.phone ?? '')
const email = ref(user.value.email ?? '')
const newPicture = ref()

const previewImage = ref(null as string | null)

const temp = ref()

const onFileChange = (e: any) => {
  const file = e.target.files[0]
  previewImage.value = URL.createObjectURL(file)
  newPicture.value = file

  temp.value.blur()
}

const toast = useToast()

const onSubmit = async () => {
  if (isLoading.value) return

  await store.updateProfile({
    name: name.value,
    phone: phone.value,
    email: email.value,
    picture: newPicture.value ?? null
  })

  if (isSuccess.value) {
    toast.success(message.value)

    previewImage.value = null
    newPicture.value = null
  } else {
    toast.error(message.value)
  }
}

onBeforeRouteLeave(() => {
  errors.value = {}
})
</script>

<template>
  <form @submit.prevent="onSubmit">
    <div :class="formClass">
      <div class="flex flex-col items-center gap-12 sm:flex-row">
        <div class="w-full flex-1 space-y-6">
          <slot></slot>

          <FormInput
            @change="(value) => (name = value)"
            :value="name"
            :Icon="UserIcon"
            placeholder="Name"
            label="Name"
            type="text"
            id="name"
            :errors="errors?.name"
          />

          <FormInput
            @change="(value) => (phone = value)"
            :value="phone"
            :Icon="PhoneIcon"
            placeholder="Phone Number"
            label="Phone Number"
            type="tel"
            id="phone"
            :errors="errors?.phone"
          />

          <FormInput
            @change="(value) => (email = value)"
            :value="email"
            :Icon="EnvelopeIcon"
            placeholder="Email"
            label="Email"
            type="email"
            id="email"
            :errors="errors?.email"
          />

          <div class="sm:hidden">
            <FormLabel text="Picture" id="mobile-profile-picture" />

            <div class="flex flex-wrap items-center gap-3">
              <img
                class="relative h-24 w-24 rounded-full border object-cover"
                :src="previewImage ?? picture"
                alt="Profile Picture"
              />

              <div>
                <label
                  for="mobile-profile-picture"
                  class="rounded-md border border-gray-300 py-2 px-3 text-sm font-medium text-gray-700"
                >
                  Change
                </label>
                <input
                  ref="temp"
                  @change="onFileChange"
                  type="file"
                  id="mobile-profile-picture"
                  class="hidden"
                />
              </div>
            </div>

            <ul>
              <li v-for="error in errors?.picture" :key="error" class="text-sm text-red-500">
                {{ error }}
              </li>
            </ul>
          </div>
        </div>

        <div class="hidden sm:block">
          <FormLabel text="Picture" id="desktop-user-photo" />

          <div class="relative overflow-hidden rounded-full">
            <img
              class="relative h-40 w-40 rounded-full border object-cover"
              :src="previewImage ?? picture"
              alt="Profile Picture"
            />
            <label
              for="desktop-user-photo"
              class="absolute inset-0 flex h-full w-full items-center justify-center bg-black bg-opacity-75 text-sm font-medium text-white opacity-0 focus-within:opacity-100 hover:opacity-100"
            >
              <span>Change</span>
              <span class="sr-only">user photo</span>
              <input
                ref="temp"
                @change="onFileChange"
                type="file"
                id="desktop-user-photo"
                class="absolute inset-0 h-full w-full cursor-pointer rounded-md border-gray-300 opacity-0"
              />
            </label>
          </div>

          <ul>
            <li v-for="error in errors?.picture" :key="error" class="text-sm text-red-500">
              {{ error }}
            </li>
          </ul>
        </div>
      </div>
    </div>

    <div :class="actionClass">
      <PrimaryButton type="submit" text="Update" :loading="isLoading" />
    </div>
  </form>
</template>
