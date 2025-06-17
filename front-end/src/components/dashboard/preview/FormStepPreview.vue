<template>
  <div class="flex flex-col p-4 border rounded-lg shadow-sm mb-4 w-[300px] min-w-[300px]">
    <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ step.title }}</h3>
    <p class="text-gray-600 mb-4">-- {{ step.description }}</p>
    <div v-if="fields.length > 0" class="grid grid-cols-1 gap-4">
        <div v-for="field in fields" :key="'field-' + field.id" class="p-2 border rounded bg-gray-50">
            <div class="text-sm font-medium text-gray-700">{{ field.label }} <span>{{ field.field_name }}</span></div>
            <p class="text-xs text-gray-500">{{ field.description }}</p>
            <div class="mt-2">
                <span class="text-xs text-gray-400">Type: {{ field.type }}</span>
            </div>
        </div>
    </div>
    <div v-else class="bg-orange-50 dark:bg-orange-400 dark:bg-opacity-10 text-orange-500 dark:text-orange-400 my-4 p-2">
        <p class="text-sm">Keine Felder in diesem Schritt. Klicken Sie auf "Feld hinzufügen", um ein neues Feld zu erstellen.</p>
    </div>
    <div class="flex">
        <AddFieldModal @add-field="addFormField" />
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useEditorStore } from '@/dashboard/editor.store';
import AddFieldModal from './AddFieldModal.vue';

type Props = {
    step_index: number;
}
const props = defineProps<Props>();
const editorStore = useEditorStore();

const step = computed(() => {
    return editorStore.formSteps.value[props.step_index];
})

const fields = computed(() => {
    return editorStore.form.fields.filter(fields => fields.step_index === props.step_index);
})

const addFormField = (field: string) => {
    const newFieldName = editorStore.addField(props.step_index, field);
    if (newFieldName) {
        editorStore.setSelectedFieldName(newFieldName);
    }
}
</script>

<style scoped></style>
