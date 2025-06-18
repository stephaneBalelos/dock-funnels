<template>
  <div class="flex justify-between items-center mb-4 p-4">
    <div class="text-lg leading-[18px] font-semibold">Formular Feld</div>
  </div>
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
        <div v-else class="text-gray-500 text-center">
          Dieses Feld ist kein Auswahlfeld. Bitte wählen Sie ein anderes Feld
          aus.
        </div>
      </div>
    </div>
    <div v-else class="text-gray-500 text-center">
      Bitte wählen Sie ein Feld aus, um es zu bearbeiten.
    </div>
  </div>
</template>

<script setup lang="ts">
import { computed } from "vue";
import { useEditorStore } from "@/dashboard/editor.store";
import FieldSelectEditor from "./FieldEditor/FieldSelectEditor.vue";
import FieldTextEditor from "./FieldEditor/FieldTextEditor.vue";

const editorStore = useEditorStore();

const field = computed(() => {
  return editorStore.form.fields.find(
    (f) => f.field_name === editorStore.selectedFieldName.value
  );
});
</script>

<style scoped></style>
