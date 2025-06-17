<template>
    <div class="h-full flex items-center p-4 gap-4">
        <FormStepPreview v-for="_step, index in editorStore.formSteps.value" :key="'preview-step-' + index" :step_index="index" />
    </div>
</template>

<script setup lang="ts">
import { useEditorStore } from "@/dashboard/editor.store";
import FormStepPreview from "./FormStepPreview.vue";

const editorStore = useEditorStore();

</script>

<style scoped>

</style>