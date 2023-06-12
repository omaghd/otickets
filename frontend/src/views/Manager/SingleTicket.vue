<script setup lang="ts">
import SingleTicket from '@/components/Common/Tickets/SingleTicket.vue'

import { useDashboard } from '@/stores/dashboard'

const { setTitle } = useDashboard()

setTitle('Ticket Details')
</script>

<template>
  <SingleTicket />
</template>
