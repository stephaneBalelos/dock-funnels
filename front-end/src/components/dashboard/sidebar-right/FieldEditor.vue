<template>
    <div v-if="field">
        <FieldSelectEditor
            v-if="field.type === 'select'"
            :field-name="field.field_name"
        />
        <FieldTextEditor
            v-else-if="field.type === 'text'"
            :field-name="field.field_name"
        />
        <div v-else class="text-gray-500 text-center">
            Dieses Feld ist kein Auswahlfeld. Bitte wählen Sie ein anderes Feld aus.
        </div>
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';
import { useEditorStore } from '@/dashboard/editor.store';
import FieldSelectEditor from './FieldEditor/FieldSelectEditor.vue';
import FieldTextEditor from './FieldEditor/FieldTextEditor.vue';


type Props = {
    fieldName: string;
}
const props = defineProps<Props>();
const editorStore = useEditorStore();

const field = computed(() => {
    return editorStore.form.fields.find(f => f.field_name === props.fieldName);
});
</script>

<style scoped>

</style>