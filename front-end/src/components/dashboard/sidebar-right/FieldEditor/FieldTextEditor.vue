<template>
    <div class="">
        {{ state }}
    </div>
</template>

<script setup lang="ts">
import { reactive } from 'vue';
import { computed } from 'vue';
import { useEditorStore } from '@/dashboard/editor.store';
import type { FormFieldText } from '@/types';

type Props = {
    fieldName: string;
}

const props = defineProps<Props>();
const editorStore = useEditorStore();

const field = computed(() => {
    return editorStore.form.fields.find(
        (field) => field.field_name === props.fieldName
    ) as FormFieldText;
});

const state = reactive<FormFieldText>({
    step_index: field.value.step_index,
    type: field.value.type,
    required: field.value.required,
    field_name: field.value.field_name,
    label: field.value.label,
    description: field.value.description,
});
</script>

<style scoped>

</style>