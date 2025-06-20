<template>
<FileUpload @select="handleSelect" ref="fileupload" mode="basic" name="import[]" accept=".json" :auto="true" :maxFileSize="1000000"></FileUpload>
</template>

<script setup lang="ts">
import { useEditorStore } from '@/dashboard/editor.store';
import { ref } from 'vue';


const fileupload = ref();
const editorStore = useEditorStore();

const handleSelect = (event: any) => {
    console.log("Selected files:", event.files);
    const file = event.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = (e) => {
            try {
                const formData = JSON.parse(e.target?.result as string);
                console.log("Parsed form data:", formData);
                // Here you can handle the form data, e.g., save it to a store or
                // update the editor state.
                // For example:
                editorStore.initEditor(formData);
            } catch (error) {
                console.error("Error parsing JSON:", error);
            } 
        };
        reader.readAsText(file);
    } else {
        console.error("No file selected or file is empty.");
    }   
};

</script>

<style scoped>

</style>