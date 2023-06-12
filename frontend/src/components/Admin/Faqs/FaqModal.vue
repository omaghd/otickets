<script setup lang="ts">
import Modal from '@/components/Common/Modal.vue'
import FormInput from '@/components/Forms/FormInput.vue'
import Autocomplete from '@/components/Forms/Autocomplete.vue'
import PrimaryButton from '@/components/Forms/PrimaryButton.vue'
import FormTextarea from '@/components/Forms/FormTextarea.vue'
import RichText from '@/components/Forms/RichText.vue'

import type Option from '@/types/Option'
import type Faq from '@/types/Faq'

import useFaqs from '@/composables/faqs/useFaqs'

import { ref, computed, watch } from 'vue'

import { useToast } from 'vue-toastification'
import Switch from '@/components/Forms/Switch.vue'

const props = defineProps<{
  open: boolean
  categories: Option[]
  faqToEdit: Faq
}>()

const emit = defineEmits<{
  (e: 'close'): void
  (e: 'success', keepQuery: boolean): void
}>()

const question = ref('')
const answer = ref('')
const excerpt = ref('')
const slug = ref('')
const isPublished = ref(false)
const category = ref({} as Option)

const method = computed(() => (props.faqToEdit.id ? 'put' : 'post'))

const resetInput = ref(false)

const title = computed(() => {
  if (props.faqToEdit.id) return 'Edit FAQ'
  else return 'New FAQ'
})

const buttonText = computed(() => {
  if (props.faqToEdit.id) return 'Update'
  else return 'Create'
})

const toast = useToast()

const { save, isLoading, isSuccess, errors, message } = useFaqs()

const reset = () => {
  resetInput.value = true

  question.value = ''
  answer.value = ''
  excerpt.value = ''
  slug.value = ''
  isPublished.value = false
  category.value = {} as Option
}

const onSubmit = async () => {
  const categoryId = isNaN(Number(category.value?.value)) ? null : Number(category.value?.value)

  await save(
    {
      question: question.value,
      answer: answer.value,
      excerpt: excerpt.value,
      slug: slug.value,
      is_published: isPublished.value,
      category_id: categoryId
    },
    method.value,
    props.faqToEdit.id ?? null
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
  () => props.faqToEdit,
  () => {
    if (props.faqToEdit.id) {
      question.value = props.faqToEdit.question
      answer.value = props.faqToEdit.answer
      excerpt.value = props.faqToEdit.excerpt
      slug.value = props.faqToEdit.slug
      isPublished.value = props.faqToEdit.is_published
    } else {
      reset()
    }
  }
)

watch(
  () => props.categories && props.faqToEdit,
  () => {
    if (props.faqToEdit.id) {
      category.value =
        props.categories.find(
          (category) => category.value === props.faqToEdit.category?.id.toString()
        ) ?? ({} as Option)
    }
  }
)
</script>

<template>
  <Modal :open="open" @close="$emit('close')" :title="title" width-class="max-w-2xl">
    <form @submit.prevent="onSubmit">
      <div class="space-y-4 p-6">
        <FormInput
          class="w-full"
          id="question"
          label="Question"
          placeholder="Question"
          type="text"
          :value="question"
          @change="(value) => (question = value)"
          :errors="errors.question"
          :reset="resetInput"
          @reset="() => (resetInput = false)"
        />

        <FormInput
          class="w-full"
          id="slug"
          label="Slug"
          placeholder="Slug"
          type="text"
          :value="slug"
          @change="(value) => (slug = value)"
          :errors="errors.slug"
          :reset="resetInput"
          @reset="() => (resetInput = false)"
        />

        <FormTextarea
          class="w-full"
          id="excerpt"
          label="Excerpt"
          placeholder="Excerpt"
          :value="excerpt"
          @change="(value) => (excerpt = value)"
          :errors="errors.excerpt"
          :reset="resetInput"
          @reset="() => (resetInput = false)"
        />

        <RichText
          label="Answer"
          placeholder="Answer"
          :headings="true"
          :value="answer"
          :errors="errors.answer"
          @change="(value) => (answer = value)"
        />

        <Autocomplete
          null-text="Select a category"
          class="w-full"
          label="Category"
          :selected="category"
          :options="categories"
          @update="(value) => (category = value)"
          :errors="errors.category_id"
          :reset="resetInput"
          @reset="() => (resetInput = false)"
        />

        <Switch label="Published" :value="isPublished" @change="(value) => (isPublished = value)" />
      </div>

      <div class="mt-3 flex justify-end border-t bg-gray-50 px-6 py-3">
        <PrimaryButton type="submit" :text="buttonText" :loading="isLoading" />
      </div>
    </form>
  </Modal>
</template>
