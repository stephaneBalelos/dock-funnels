<template>
    <div class="w-full max-w-4xl mx-auto p-4">
        <DockFunnelForm
            v-if="form"
            :form="form"
            class="dockfunnel-form-preview"
        />
    </div>
</template>

<script setup lang="ts">
import { useEditorStore } from "@/dashboard/editor.store.ts";
import { useFormSubmissionStateStore } from "@/forms/stores/submission.store";
import type { Form } from "@/types";
import { computed, onMounted, provide } from "vue";

provide('isFormDesignPreview', true);

const editorStore = useEditorStore();
const submissionStateStore = useFormSubmissionStateStore();

const form = computed(() => {
    const f: Form = {
        id: editorStore.form.id || 0,
        title: editorStore.form.title,
        description: editorStore.form.description,
        form_steps: editorStore.form.form_steps,
        form_fields: editorStore.form.form_fields,
    }
    return f;
});

onMounted(() => {
    submissionStateStore.form.value = form.value;
})


</script>

<style scoped>

</style>