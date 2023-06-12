<script setup lang="ts">
import { useHead } from 'unhead'

import { appTitle } from '@/global'

useHead({ title: `Not Found | ${appTitle}` })
</script>

<template>
  <div>Not Found</div>
</template>
