<script setup lang="ts">
import SectionHeader from '@/components/Common/SectionHeader.vue'
import ListBox from '@/components/Forms/ListBox.vue'
import Autocomplete from '@/components/Forms/Autocomplete.vue'
import FormInput from '@/components/Forms/FormInput.vue'
import RichText from '@/components/Forms/RichText.vue'
import FileUpload from '@/components/Forms/FileUpload.vue'
import PrimaryButton from '@/components/Forms/PrimaryButton.vue'

import type Option from '@/types/Option'
import type Category from '@/types/Category'

import { ref, onMounted, watch } from 'vue'

import { useHead } from 'unhead'

import { appTitle } from '@/global'

import getCategories from '@/composables/categories/getCategories'
import useTickets from '@/composables/tickets/useTickets'

import { onBeforeRouteLeave, useRouter } from 'vue-router'

import { useToast } from 'vue-toastification'

import useFaqs from '@/composables/faqs/useFaqs'

useHead({ title: `New Ticket | ${appTitle}` })

const priorities = [
  { name: 'Low', value: 'low' },
  { name: 'Medium', value: 'medium' },
  { name: 'High', value: 'high' }
]

const priority = ref<Option>({} as Option)

const categories = ref<Option[]>([] as Option[])

const category = ref<Option>({} as Option)

const { load, categories: tempCategories } = getCategories()

onMounted(async () => {
  await load()

  categories.value = tempCategories.value.map((category: Category) => ({
    name: category.name,
    value: category.id.toString()
  }))
})

const subject = ref('')

const description = ref('')

const files = ref()

const { create, isLoading, isSuccess, ticket, message, errors } = useTickets()

const router = useRouter()

const toast = useToast()

const onSubmit = async () => {
  if (isLoading.value) return

  await create({
    priority: priority.value?.value,
    category_id: category.value?.value,
    subject: subject.value,
    description: description.value,
    attachments: files.value
  })

  if (isSuccess.value) {
    toast.success(message.value)

    router.push({ name: 'ClientSingleTicket', params: { reference: ticket.value.reference } })
  } else {
    toast.error(message.value)
  }
}

const { getSuggestions, faqs } = useFaqs()

watch(subject, async () => {
  if (subject.value === '') return

  await getSuggestions(subject.value)
})

onBeforeRouteLeave(() => {
  errors.value = {}
})
</script>

<template>
  <form class="divide-y divide-gray-200" @submit.prevent="onSubmit">
    <div class="py-6 px-4 sm:p-6 lg:pb-8">
      <SectionHeader title="New Ticket" class="mb-6" />

      <div class="space-y-6">
        <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
          <ListBox
            @update="(newPriority) => (priority = newPriority)"
            :selected="priority"
            label="Priority"
            :options="priorities"
            :errors="errors?.priority"
            null-text="Select a priority"
          />

          <Autocomplete
            @update="(newCategory) => (category = newCategory)"
            :selected="category"
            label="Category"
            :options="categories"
            :errors="errors?.category_id"
            null-text="Select a category"
          />
        </div>

        <div>
          <FormInput
            @change="(value) => (subject = value)"
            id="subject"
            type="text"
            label="Subject"
            placeholder="Subject"
            :errors="errors?.subject"
          />

          <div class="mt-2 rounded-lg border bg-gray-50 p-4" v-if="faqs.length && subject">
            <h3 class="font-semibold">Related questions</h3>

            <router-link
              v-for="faq in faqs"
              :key="faq.id"
              target="_blank"
              :to="{ name: 'SingleFaq', params: { slug: faq.slug } }"
              class="mt-3 block text-sm text-gray-600 hover:text-gray-900"
            >
              {{ faq.question }}
            </router-link>
          </div>
        </div>

        <RichText
          @change="(value) => (description = value)"
          label="Description"
          placeholder="Describe your issue..."
          :errors="errors?.description"
        />

        <FileUpload
          @change="(value) => (files = value)"
          label="Drop attachments here or click to browse"
          :errors="errors?.attachments"
          :ref="files"
          :multiple="true"
        />
      </div>
    </div>

    <div class="flex justify-end bg-gray-50 py-4 px-4 sm:px-6">
      <PrimaryButton type="submit" text="Create" :loading="isLoading" />
    </div>
  </form>
</template>
