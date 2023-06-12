<script setup lang="ts">
import Hero from '@/components/Home/Hero.vue'
import Category from '@/components/Home/Categories/Category.vue'
import CategorySkeleton from '@/components/Home/Categories/CategorySkeleton.vue'
import Newsletter from '@/components/Home/Newsletter.vue'

import { onMounted } from 'vue'

import { useHead } from 'unhead'

import { appTitle } from '@/global'

import getCategories from '@/composables/categories/getCategories'

const { load, categories, isLoading } = getCategories()

onMounted(async () => await load())

useHead({ title: `${appTitle}` })
</script>

<template>
  <div class="mx-auto max-w-7xl">
    <Hero />

    <section class="my-16">
      <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
        <template v-if="isLoading">
          <CategorySkeleton v-for="i in 8" :key="i" />
        </template>

        <template v-else>
          <Category v-for="category in categories" :key="category.slug" :category="category" />
        </template>
      </div>
    </section>

    <Newsletter />
  </div>
</template>
