<script setup lang="ts">
import Modal from '@/components/Common/Modal.vue'
import FormInput from '@/components/Forms/FormInput.vue'
import ListBox from '@/components/Forms/ListBox.vue'
import Autocomplete from '@/components/Forms/Autocomplete.vue'
import PrimaryButton from '@/components/Forms/PrimaryButton.vue'
import FormLabel from '@/components/Forms/FormLabel.vue'
import Errors from '@/components/Forms/Errors.vue'

import type Option from '@/types/Option'
import type User from '@/types/User'

import useUsers from '@/composables/users/useUsers'

import { ref, computed, watch } from 'vue'

import { useToast } from 'vue-toastification'

const props = defineProps<{
  open: boolean
  departments: Option[]
  userToEdit: User
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'success', keepQuery: boolean): void
}>()

const name = ref('')
const email = ref('')
const phone = ref('')
const password = ref('')
const role = ref({} as Option)
const department = ref({} as Option)
const picture = ref({} as File | null)

const roles = ref([
  { name: 'Agent', value: 'agent' },
  { name: 'Admin', value: 'admin' }
] as Option[])

const method = computed(() => (props.userToEdit.id ? 'put' : 'post'))

const isAgent = computed(() => role.value?.value === 'agent')

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
  if (props.userToEdit.id) {
    return 'Edit User'
  } else {
    return 'New User'
  }
})

const buttonText = computed(() => {
  if (props.userToEdit.id) {
    return 'Update'
  } else {
    return 'Create'
  }
})

const toast = useToast()

const { save, isLoading, isSuccess, errors, message } = useUsers()

const reset = () => {
  resetInput.value = true

  name.value = ''
  email.value = ''
  phone.value = ''
  password.value = ''
  role.value = {} as Option
  department.value = {} as Option
  picture.value = {} as File | null
  previewImage.value = defaultImagePath
}

const onSubmit = async () => {
  const departmentId = isNaN(Number(department.value?.value))
    ? null
    : Number(department.value?.value)

  await save(
    {
      name: name.value,
      email: email.value,
      phone: phone.value,
      password: password.value,
      role: role.value?.value,
      department_id: isAgent.value ? departmentId : null,
      picture: picture.value
    },
    method.value,
    props.userToEdit.id ?? null
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
  () => props.userToEdit,
  () => {
    if (props.userToEdit.id) {
      name.value = props.userToEdit.name
      email.value = props.userToEdit.email
      phone.value = props.userToEdit.phone
      role.value =
        roles.value.find((role) => role.value === props.userToEdit.role) ?? ({} as Option)
      previewImage.value = API_URL + props.userToEdit.picture
    } else {
      reset()
    }
  }
)

watch(
  () => props.departments && props.userToEdit,
  () => {
    if (props.userToEdit.id) {
      department.value =
        props.departments.find(
          (department) => department.value === props.userToEdit.department?.id.toString()
        ) ?? ({} as Option)
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

        <ListBox
          null-text="Select a role"
          class="w-full"
          label="Role"
          :selected="role"
          :options="roles"
          @update="(value) => (role = value)"
          :errors="errors.role"
          :reset="resetInput"
          @reset="() => (resetInput = false)"
        />

        <Autocomplete
          v-if="isAgent"
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
