<template>
  <div class="flex items-center">
    <p class="text-stone-900 font-semibold text-lg">
      <Icon icon="heroicons:document-text" class="inline mr-2" />
      {{ editorStore.form.title }}
    </p>
  </div>
</template>

<script setup lang="ts">
import { useEditorStore } from "@/dashboard/editor.store";
import { Icon } from "@iconify/vue";

const editorStore = useEditorStore();

</script>

<style scoped></style>
