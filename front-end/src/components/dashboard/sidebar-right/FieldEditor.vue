<template>
  <div class="relative flex-1 overflow-y-auto">
    <div
      v-if="editorStore.selectedFieldName.value"
      class="absolute inset-0 overflow-y-auto px-4"
    >
      <div v-if="field" class="w-full">
        <FieldSelectEditor
          v-if="field.type === 'select'"
          :field-name="field.field_name"
        />
        <FieldTextEditor
          v-else-if="field.type === 'text'"
          :field-name="field.field_name"
        />
        <FieldChecklistEditor
          v-else-if="field.type === 'checkboxList'"
          :field-name="field.field_name"
        />
        <FieldSummaryEditor
          v-else-if="field.type === 'submissionSummary'"
          :field-name="field.field_name"
        />
        <div v-else class="text-gray-500 text-center">
          Dieses Feld ist nicht verfügbar oder wird nicht unterstützt.
        </div>
      </div>
    </div>
    <div v-else class="text-gray-500 text-center p-8">
      Bitte wählen Sie ein Feld aus, um es zu bearbeiten.
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from "vue";
import { useEditorStore } from "@/dashboard/editor.store";
import FieldSelectEditor from "./FieldEditor/FieldSelectEditor.vue";
import FieldTextEditor from "./FieldEditor/FieldTextEditor.vue";
import FieldChecklistEditor from "./FieldEditor/FieldChecklistEditor.vue";
import FieldSummaryEditor from "./FieldEditor/FieldSummaryEditor.vue";

const editorStore = useEditorStore();

const field = computed(() => {
  return editorStore.form.fields.find(
    (f) => f.field_name === editorStore.selectedFieldName.value
  );
});
</script>

<style scoped></style>
