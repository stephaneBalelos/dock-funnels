<template>
    <div v-if="editorStore.editorState.value.isLoading" class="absolute inset-0 flex items-center justify-center bg-white/75">
      <div class="flex flex-col items-center justify-center">
        <Icon icon="heroicons:arrow-path" class="text-4xl mb-4 animate-spin" />
        <p class="text-lg font-semibold">Formular wird geladen...</p>
      </div>
    </div>
</template>

<script setup lang="ts">
import { useEditorStore } from '@/dashboard/editor.store';
import { Icon } from '@iconify/vue';

const editorStore = useEditorStore();

</script>

<style scoped>

</style>