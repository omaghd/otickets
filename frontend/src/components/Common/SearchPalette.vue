<script setup lang="ts">
import { ref, watch } from 'vue'

import MagnifyingGlassIcon from '@heroicons/vue/24/solid/MagnifyingGlassIcon'
import QuestionMarkCircleIcon from '@heroicons/vue/24/outline/QuestionMarkCircleIcon'

import {
  Combobox,
  ComboboxInput,
  ComboboxOptions,
  ComboboxOption,
  Dialog,
  DialogOverlay,
  TransitionChild,
  TransitionRoot,
  DialogPanel
} from '@headlessui/vue'

import useFaqs from '@/composables/faqs/useFaqs'

import { debounce } from 'lodash'

import SearchLottie from '@/assets/search.json'
import EmptyResultsLottie from '@/assets/emptyResults.json'

import { useSearchPalette } from '@/stores/searchPalette'
import { storeToRefs } from 'pinia'

import { useRouter } from 'vue-router'

const query = ref('')

const { getSuggestions, isLoading, faqs } = useFaqs()

watch(
  query,
  debounce(async function (value: string) {
    if (value === '') return

    await getSuggestions(value)
    isLoading.value = false
  }, 300)
)

const store = useSearchPalette()
const { isOpen } = storeToRefs(store)

const router = useRouter()

const onSelect = (slug: string) => {
  router.push({ name: 'SingleFaq', params: { slug } })
  store.close()
}
</script>

<template>
  <TransitionRoot :show="isOpen" as="template" @after-leave="query = ''">
    <Dialog
      as="div"
      class="fixed inset-0 z-50 overflow-y-auto p-4 sm:p-6 md:p-20"
      @close="store.close"
    >
      <TransitionChild
        as="template"
        enter="ease-out duration-300"
        enter-from="opacity-0"
        enter-to="opacity-100"
        leave="ease-in duration-200"
        leave-from="opacity-100"
        leave-to="opacity-0"
      >
        <DialogOverlay class="fixed inset-0 bg-gray-500 bg-opacity-60 transition-opacity" />
      </TransitionChild>

      <TransitionChild
        as="template"
        enter="ease-out duration-300"
        enter-from="opacity-0 scale-95"
        enter-to="opacity-100 scale-100"
        leave="ease-in duration-200"
        leave-from="opacity-100 scale-100"
        leave-to="opacity-0 scale-95"
      >
        <DialogPanel>
          <Combobox
            as="div"
            class="mx-auto max-w-2xl transform divide-y divide-gray-500 divide-opacity-10 overflow-hidden rounded-xl bg-white bg-opacity-80 shadow-2xl ring-1 ring-black ring-opacity-5 backdrop-blur backdrop-filter transition-all"
            @update:modelValue="onSelect"
          >
            <div class="relative">
              <MagnifyingGlassIcon
                class="pointer-events-none absolute top-3.5 left-4 h-5 w-5 text-gray-900 text-opacity-40"
              />
              <ComboboxInput
                class="h-12 w-full border-0 bg-transparent pl-11 pr-4 text-gray-900 placeholder-gray-500 focus:ring-0 sm:text-sm"
                placeholder="Search..."
                @change="query = $event.target.value"
              />
            </div>

            <LottieAnimation
              v-if="isLoading"
              :animationData="SearchLottie"
              class="w-96 max-w-full"
              :autoplay="true"
              :loop="true"
            />

            <ComboboxOptions
              v-else-if="faqs.length"
              static
              class="max-h-80 scroll-py-2 divide-y divide-gray-500 divide-opacity-10 overflow-y-auto"
            >
              <li class="p-2">
                <ul class="text-sm text-gray-700">
                  <router-link
                    :to="{ name: 'SingleFaq', params: { slug: faq.slug } }"
                    v-for="faq in faqs"
                    :key="faq.id"
                  >
                    <ComboboxOption :value="faq.slug" as="template" v-slot="{ active }">
                      <li
                        @click="store.close"
                        :class="[
                          'flex select-none items-center rounded-md px-3 py-2',
                          active && 'bg-gray-900 bg-opacity-5 text-gray-900'
                        ]"
                      >
                        <QuestionMarkCircleIcon
                          :class="[
                            'h-6 w-6 flex-none text-gray-900 text-opacity-40',
                            active && 'text-opacity-100'
                          ]"
                        />
                        <span class="ml-3 flex-auto truncate">{{ faq.question }}</span>
                      </li>
                    </ComboboxOption>
                  </router-link>
                </ul>
              </li>
            </ComboboxOptions>

            <div v-else-if="!faqs.length && query" class="py-14 px-6 text-center sm:px-14">
              <LottieAnimation
                :animationData="EmptyResultsLottie"
                class="-mt-16 w-72 max-w-full"
                :loop="true"
              />
              <p class="-mt-6 text-xl text-gray-900">No Results</p>
            </div>
          </Combobox>
        </DialogPanel>
      </TransitionChild>
    </Dialog>
  </TransitionRoot>
</template>
