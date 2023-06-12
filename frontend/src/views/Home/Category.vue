<script setup lang="ts">
import Breadcrumbs from '@/components/Layout/Breadcrumbs.vue'
import FaqsSkeleton from '@/components/Home/Categories/CategoryFaqsSkeleton.vue'
import CategoriesList from '@/components/Common/Tickets/CategoriesList.vue'
import BreadcrumbsSkeleton from '@/components/Layout/BreadcrumbsSkeleton.vue'
import CategoriesListSkeleton from '@/components/Home/Categories/CategoriesListSkeleton.vue'

import CalendarIcon from '@heroicons/vue/24/outline/CalendarIcon'

import { useHead } from 'unhead'

import { appTitle } from '@/global'

import { ref, onMounted } from 'vue'

import type Page from '@/types/Page'

import { onBeforeRouteUpdate, useRoute } from 'vue-router'

import getCategory from '@/composables/categories/getCategory'
import getCategories from '@/composables/categories/getCategories'

import notFound from '@/util/notFound'

const pages = ref<Page[]>([])

const route = useRoute()

const slug = ref((route.params.slug as string) ?? '')

const { load, category, isLoading, error } = getCategory(slug)

const categoriesFilters = ref({ paginate: false })
const { load: fetchCategories, categories } = getCategories(categoriesFilters)

onMounted(async () => {
  await load()

  if (error.value) notFound(route)

  pages.value = [
    {
      name: category.value.name,
      to: 'HomeCategory',
      params: { slug: category.value.slug }
    }
  ]

  useHead({ title: `${category.value.name} | ${appTitle}` })

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
    <BreadcrumbsSkeleton class="mb-6" v-if="isLoading" />

    <Breadcrumbs :pages="pages" class="mb-6" v-else />

    <div class="flex flex-col gap-8 lg:flex-row lg:gap-12">
      <div class="w-full space-y-5">
        <FaqsSkeleton v-if="isLoading" />

        <template v-else>
          <div v-for="faq in category.faqs" :key="faq.id">
            <router-link
              :to="{ name: 'SingleFaq', params: { slug: faq.slug } }"
              class="group block rounded-lg border bg-white p-4 hover:bg-gray-50 active:shadow-none"
            >
              <header class="text-xl font-semibold text-teal-600 group-hover:text-teal-700">
                <p>{{ faq.question }}</p>
              </header>

              <div class="mt-1 flex items-center gap-1">
                <CalendarIcon class="h-4 w-4 text-gray-400" />
                <p v-tooltip.bottom="'Last updated on'" class="text-xs text-gray-500">
                  {{ faq.updated_at }}
                </p>
              </div>

              <article class="mt-3 text-gray-600">{{ faq.excerpt }}</article>
            </router-link>
          </div>
        </template>
      </div>

      <aside class="relative w-full lg:max-w-sm">
        <CategoriesListSkeleton v-if="!categories.length" />

        <CategoriesList :categories="categories" class="sticky top-16" v-else />
      </aside>
    </div>
  </section>
</template>
