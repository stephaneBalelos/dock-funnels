<template>
<FileUpload @select="handleSelect" ref="fileupload" mode="basic" name="import[]" accept=".json" :auto="true" :maxFileSize="1000000"></FileUpload>
</template>

<script setup lang="ts">
import { useEditorStore } from '@/dashboard/editor.store';
import type { FormExportData } from '@/types';
import { useToast } from 'primevue';
import { ref } from 'vue';

const fileupload = ref();
const editorStore = useEditorStore();
const toast = useToast();

const handleSelect = (event: any) => {
    const file = event.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            try {
                const formData = JSON.parse(e.target?.result as string) as FormExportData;
                if (!formData.form_fields || !formData.form_steps) {
                    throw new Error("Invalid JSON format: Missing form_fields or form_steps.");
                }
                // Initialize the editor store with the imported form data
                loadImport(formData);
            } catch (error) {
                console.error("Error parsing JSON:", error);
                toast.add({ severity: 'error', summary: 'Error', detail: 'Failed to import form data.' });
            } 
        };
        reader.readAsText(file);
    } else {
        console.error("No file selected or file is empty.");
    }   
};

const loadImport = (data: FormExportData) => {
    // Load the steps
    editorStore.form.form_steps = data.form_steps;
    // Load the fields
    editorStore.form.form_fields = data.form_fields;
};

</script>

<style scoped>

</style>