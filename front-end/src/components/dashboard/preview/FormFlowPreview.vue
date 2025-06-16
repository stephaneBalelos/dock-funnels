<template>
    <div class="h-full flex items-center p-4 gap-4">
        <div v-for="step in editorStore.form.value.form_steps" :key="'preview-step-' + step.id" class="flex flex-col p-4 border rounded-lg shadow-sm mb-4 min-w-[300px]">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ step.title }}</h3>
            <p class="text-gray-600 mb-4">{{ step.description }}</p>
            <div class="grid grid-cols-1 gap-4">
            
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { useEditorStore } from "@/dashboard/editor.store";

const editorStore = useEditorStore();
</script>

<style scoped>

</style>