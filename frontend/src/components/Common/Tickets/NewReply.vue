<script setup lang="ts">
import SectionHeader from '@/components/Common/SectionHeader.vue'
import Autocomplete from '@/components/Forms/Autocomplete.vue'
import FileUpload from '@/components/Forms/FileUpload.vue'
import RichText from '@/components/Forms/RichText.vue'
import ActionButton from '../ActionButton.vue'

import ChevronUpIcon from '@heroicons/vue/24/solid/ChevronUpIcon'
import ChevronDownIcon from '@heroicons/vue/24/solid/ChevronDownIcon'
import ClipboardIcon from '@heroicons/vue/24/outline/ClipboardIcon'

import {
  Disclosure,
  DisclosureButton,
  DisclosurePanel,
  Menu,
  MenuButton,
  MenuItem,
  MenuItems
} from '@headlessui/vue'

import { ref, onMounted, computed } from 'vue'

import useTickets from '@/composables/tickets/useTickets'
import getCannedResponses from '@/composables/canned-responses/getCannedResponses'

import { useToast } from 'vue-toastification'

import { onBeforeRouteLeave } from 'vue-router'

import type CannedResponse from '@/types/CannedResponse'
import type Option from '@/types/Option'

import { useClipboard } from '@vueuse/core'

import { useAuthStore } from '@/stores/auth'
import { storeToRefs } from 'pinia'

const authStore = useAuthStore()
const { isAgent } = storeToRefs(authStore)

const content = ref('')

const attachments = ref([] as File[])

const props = defineProps<{ ticketId: number }>()

const emit = defineEmits<{
  (event: 'reload'): void
}>()

const cannedResponses = ref([] as Option[])

const { load, cannedResponses: tempCannedResponses } = getCannedResponses()

onMounted(async () => {
  if (!isAgent.value) return

  await load()

  cannedResponses.value = tempCannedResponses.value.map((cannedResponse: CannedResponse) => ({
    name: cannedResponse.title,
    value: cannedResponse.content
  }))
})

const cannedResponse = ref({} as Option)

const cannedResponseToCopy = computed(() => cannedResponse.value?.value ?? null)

const { copy, copied } = useClipboard({
  source: cannedResponseToCopy
})

const { reply, isLoading, isSuccess, message, errors } = useTickets()

const toast = useToast()

const resetFiles = ref(false)

const resetContent = ref(false)

const onSuccess = () => {
  toast.success(message.value)

  attachments.value = []

  content.value = ''

  resetFiles.value = true

  resetContent.value = true

  emit('reload')
}

const action = ref('')

const handleSubmit = async () => {
  if (isLoading.value) return

  resetFiles.value = false

  resetContent.value = false

  await reply({
    action: action.value,
    ticket_id: props.ticketId,
    content: content.value,
    attachments: attachments.value
  })

  if (isSuccess.value) onSuccess()
  else toast.error(message.value)
}

const justReply = () => {
  action.value = 'reply'

  handleSubmit()
}

const replyAndMarkAsResolved = () => {
  action.value = 'resolve'

  handleSubmit()
}

const replyAndMarkAsClosed = () => {
  action.value = 'close'

  handleSubmit()
}

onBeforeRouteLeave(() => {
  errors.value = {}
})
</script>

<template>
  <Disclosure as="form" v-slot="{ open }">
    <DisclosureButton
      :class="open ? 'rounded-t-lg' : 'rounded-lg'"
      class="flex w-full items-center justify-between border border-b-0 bg-gray-50 px-3 py-4 text-left shadow"
    >
      <SectionHeader title="New Reply" />

      <ChevronUpIcon :class="open ? 'rotate-180 transform' : ''" class="h-5 w-5 text-gray-500" />
    </DisclosureButton>

    <DisclosurePanel class="divide-y rounded-b-lg border shadow">
      <div class="space-y-3 px-3 pb-3 pt-6">
        <div class="flex items-end gap-3" v-if="isAgent">
          <Autocomplete
            class="max-w-sm flex-1"
            null-text="Select a canned response"
            label="Canned Response"
            :options="cannedResponses"
            @update="(value) => (cannedResponse = value)"
          />

          <div>
            <ActionButton :text="copied ? 'Copied' : 'Copy'" :action="copy" :Icon="ClipboardIcon" />
          </div>
        </div>

        <RichText
          @change="(value) => (content = value)"
          placeholder="Reply to this ticket"
          :value="content"
          :errors="errors.content"
          :reset-content="resetContent"
        />

        <FileUpload
          @change="(value) => (attachments = value)"
          label="Drop attachments here or click to browse"
          multiple
          :errors="errors.attachments"
          :reset-files="resetFiles"
        />
      </div>

      <div class="flex justify-end bg-gray-50 p-3">
        <div class="relative z-0 inline-flex rounded-md shadow-sm">
          <button
            @click="justReply"
            type="button"
            class="relative inline-flex items-center rounded-l-md border border-teal-700 bg-teal-600 px-4 py-2 font-medium text-white hover:bg-teal-700 focus:z-10 focus:border-teal-700 focus:outline-none focus:ring-1 focus:ring-teal-700"
          >
            Reply
          </button>
          <Menu as="span" class="relative -ml-px block">
            <MenuButton
              class="hover:teal-700 relative inline-flex items-center rounded-r-md border border-teal-700 bg-teal-600 px-2 py-2 font-medium text-white focus:z-10 focus:border-teal-700 focus:outline-none focus:ring-1 focus:ring-teal-700"
            >
              <span class="sr-only">Open options</span>
              <ChevronDownIcon class="h-6 w-6" />
            </MenuButton>
            <transition
              enter-active-class="transition ease-out duration-100"
              enter-from-class="transform opacity-0 scale-95"
              enter-to-class="transform opacity-100 scale-100"
              leave-active-class="transition ease-in duration-75"
              leave-from-class="transform opacity-100 scale-100"
              leave-to-class="transform opacity-0 scale-95"
            >
              <MenuItems
                class="absolute right-0 mt-2 -mr-1 w-60 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none"
              >
                <div class="divide-y">
                  <MenuItem
                    class="cursor-pointer"
                    @click="replyAndMarkAsResolved"
                    v-slot="{ active }"
                  >
                    <div
                      :class="[
                        active ? 'bg-gray-100 text-gray-900' : 'text-gray-700',
                        'block px-4 py-3 text-sm'
                      ]"
                    >
                      Reply and Mark as Resolved
                    </div>
                  </MenuItem>

                  <MenuItem
                    class="cursor-pointer"
                    @click="replyAndMarkAsClosed"
                    v-slot="{ active }"
                  >
                    <div
                      :class="[
                        active ? 'bg-gray-100 text-gray-900' : 'text-gray-700',
                        'block px-4 py-3 text-sm'
                      ]"
                    >
                      Reply and Mark as Closed
                    </div>
                  </MenuItem>
                </div>
              </MenuItems>
            </transition>
          </Menu>
        </div>
      </div>
    </DisclosurePanel>
  </Disclosure>
</template>
