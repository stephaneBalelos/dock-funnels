<template>
  <div class="h-lvh pb-16 bg-white">
    <ResponsesContainer
        v-if="formState"
        :formState="formState"
    />
 </div>
</template>

<script setup lang="ts">
import { getFormById } from '@/api/wpAjaxApi';
import ResponsesContainer from '@/components/dashboard/responses/ResponsesContainer.vue';
import type { FormState } from '@/types';
import { onMounted, inject, ref } from 'vue';
import { useEditorStore } from './editor.store';

const formId = inject('formId') as number | undefined;
const endpoint = inject('ajaxUrl') as string | undefined;
const nonce = inject('nonce') as string | undefined;

const formState = ref<FormState>()
const editorStore = useEditorStore();



onMounted(async () => {
    if (!formId || !endpoint || !nonce) {
        console.error("Form ID, API endpoint, or nonce not provided");
        return;
    }
    // Fetch form responses using the provided formId
    try {
        const form = await getFormById(endpoint, nonce, formId) as FormState;
        formState.value = form;
        editorStore.initEditor(form);
    } catch (error) {
        console.error("Error fetching form data:", error);
    }
});

</script>

<style scoped>

</style>