<template>
    <div class="h-full flex flex-col items-center p-4 gap-4">
        <FormStepPreview v-if="editorStore.form.form_steps.length > 0" :key="'preview-step-' + editorStore.selectedStepIndex.value" :step_index="editorStore.selectedStepIndex.value ?? 0" />
        <div v-else class="text-gray-500 text-center">
            <p>Bitte wählen Sie einen Schritt aus, um eine Vorschau anzuzeigen.</p>
        </div>
    </div>
</template>

<script setup lang="ts">
import { useEditorStore } from "@/dashboard/editor.store";
import FormStepPreview from "./FormStepPreview.vue";

const editorStore = useEditorStore();

</script>

<style scoped>

</style>