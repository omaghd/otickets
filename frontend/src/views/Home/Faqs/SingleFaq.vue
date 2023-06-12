<script setup lang="ts">
import Breadcrumbs from '@/components/Layout/Breadcrumbs.vue'
import MoreSkeleton from '@/components/Home/Faqs/MoreSkeleton.vue'
import FaqSkeleton from '@/components/Home/Faqs/FaqSkeleton.vue'
import RelatedQuestions from '@/components/Common/Tickets/RelatedQuestions.vue'
import CategoriesList from '@/components/Common/Tickets/CategoriesList.vue'
import BreadcrumbsSkeleton from '@/components/Layout/BreadcrumbsSkeleton.vue'
import CategoriesListSkeleton from '@/components/Home/Categories/CategoriesListSkeleton.vue'

import CalendarIcon from '@heroicons/vue/24/outline/CalendarIcon'

import { ref, onMounted } from 'vue'

import type Page from '@/types/Page'

import { onBeforeRouteUpdate, useRoute } from 'vue-router'

import getFaq from '@/composables/faqs/getFaq'
import getCategories from '@/composables/categories/getCategories'

import notFound from '@/util/notFound'

import { useHead } from 'unhead'

import { appTitle } from '@/global'

const pages = ref<Page[]>([])

const route = useRoute()

const slug = ref(route.params.slug as string)
const { load, faq, isLoading, error } = getFaq(slug, true)

const categoriesFilters = ref({ paginate: false })
const { load: fetchCategories, categories } = getCategories(categoriesFilters)

onMounted(async () => {
  await load()

  if (error.value) notFound(route)

  pages.value = [
    {
      name: faq.value.category.name,
      to: 'HomeCategory',
      params: { slug: faq.value.category.slug }
    }
  ]

  useHead({ title: `${faq.value.question} | ${appTitle}` })

  await fetchCategories()
})

onBeforeRouteUpdate(async (to, from) => {
  if (to.params.slug !== from.params.slug) {
    slug.value = to.params.slug as string

    await load()
  }
})
</script>

<template>
  <section class="mx-auto max-w-7xl">
    <BreadcrumbsSkeleton v-if="!faq?.id" />

    <Breadcrumbs :pages="pages" v-else />

    <div class="mt-6 flex flex-col gap-8 lg:flex-row lg:gap-12">
      <article class="w-full">
        <FaqSkeleton v-if="isLoading" />

        <template v-else-if="faq">
          <div class="divide-y rounded-lg border bg-white">
            <div class="mb-6 p-4">
              <header class="mb-6">
                <h1 class="text-3xl font-semibold text-teal-600">{{ faq.question }}</h1>

                <div class="mt-2 flex items-center gap-1">
                  <CalendarIcon class="h-4 w-4 text-gray-400" />
                  <p v-tooltip.bottom="'Last updated on'" class="text-xs text-gray-500">
                    {{ faq.updated_at }}
                  </p>
                </div>
              </header>

              <div class="prose max-w-none space-y-0 prose-headings:mb-0" v-html="faq.answer"></div>
            </div>

            <div class="p-4">
              <p>
                <span>Still have questions?</span>
                <router-link
                  :to="{ name: 'ClientNewTicket' }"
                  class="font-semibold text-teal-600 hover:text-teal-700"
                >
                  submit a ticket
                </router-link>
              </p>
            </div>
          </div>
        </template>
      </article>

      <aside class="relative w-full lg:max-w-sm">
        <div class="sticky top-16 space-y-6">
          <template v-if="!categories.length">
            <MoreSkeleton />

            <CategoriesListSkeleton />
          </template>

          <template v-else>
            <RelatedQuestions :faqs="faq?.category.faqs" />

            <CategoriesList :categories="categories" />
          </template>
        </div>
      </aside>
    </div>
  </section>
</template>
