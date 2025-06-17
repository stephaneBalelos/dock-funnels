<script setup lang="ts">
import { ref } from 'vue'

const open = ref(false)

const $emits = defineEmits<{
  (e: 'add-field', fieldType: string): void
}>()

function close(type: string) {
  $emits('add-field', type)
  open.value = false
}

</script>

<template>
  hier kommt das AddFieldModal
</template>