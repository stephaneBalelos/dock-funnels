<template>
  <div class="absolute inset-0 overflow-y-auto px-4" v-if="!editorStore.editorState.value.isLoading">
    <StepItem
      v-for="(step, index) in editorStore.form.form_steps"
      :key="'step-' + index"
      :step-index="index"
      :step="step"
    >
    </StepItem>
  </div>
</template>

<script setup lang="ts">
import { useEditorStore } from '@/dashboard/editor.store';

const editorStore = useEditorStore();

</script>

<style scoped></style>
