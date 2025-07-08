<template>
  <div
    v-if="field"
    :class="
      'p-2 border rounded bg-gray-50 cursor-pointer hover:bg-gray-100 transition-colors duration-200 ' +
      (editorStore.selectedFieldName.value === field.field_name
        ? 'border-blue-500 bg-blue-50'
        : 'border-gray-200')
    "
    @click="editorStore.setSelectedFieldName(field.field_name)"
  >
    <div class="text-md font-medium text-gray-700">
      {{ field.label }}
      <span v-if="field.required" class="text-red-500">*</span>
    </div>
    <p v-if="field.description" class="text-sm text-gray-500">
      {{ field.description }}
    </p>
    <div class="flex flex-col py-2 gap-4">
      <span class="text-xs text-surface-700 font-semibold">
        Feldtyp: {{ field.type }}
        <span v-if="field.type === 'text'">[{{ field.input_type }}]</span></span
      >
      <span v-if="field.default_value" class="text-xs text-gray-400">
        Standardwert: {{ field.default_value }}
      </span>
      <div class="flex flex-col items-start p-2 border border-gray-200 rounded">
        <div
          v-if="field.depends_on && field.depends_on.length > 0"
          class="flex flex-col"
        >
          <p class="text-xs text-surface-700 font-semibold">Wird angezeigt wenn:</p>
          <DependencyBadge
            v-for="(dep, dep_idx) in field.depends_on"
            :key="dep_idx"
            :field_name="dep.field_name"
            :field_value="dep.value"
            @onRemoveDependency="
              editorStore.removeFieldDependency(field.field_name, dep_idx)
            "
          />
        </div>
        <Button
          label="Bedingung Hinzufügen"
          @click="openAddDepModal"
          size="small"
        />
      </div>
    </div>
    <Dialog
      v-model:visible="showEditDependencyDialog"
      modal
      header="Abhängigkeit hinzufügen"
    >
      <FieldDependencyInput
        v-model="dependencyState"
        :field_name="field.field_name"
      ></FieldDependencyInput>
      <div class="flex justify-end gap-2 mt-4">
        <Button
          type="button"
          label="Abbrechen"
          severity="secondary"
          @click="showEditDependencyDialog = false"
        ></Button>
        <Button
          type="button"
          label="Hinzufügen"
          @click="addDependency(dependencyState, field.field_name)"
        ></Button>
      </div>
    </Dialog>
  </div>
</template>

<script setup lang="ts">
import { useEditorStore } from "@/dashboard/editor.store";
import type { FormFieldDependsOn } from "@/types";
import { computed, ref } from "vue";
import DependencyBadge from "../sidebar-right/FieldEditor/DependencyBadge.vue";

type Props = {
  fieldName: string;
  step_index: number;
};

const props = defineProps<Props>();
const editorStore = useEditorStore();
const showEditDependencyDialog = ref(false);

const dependencyState = ref<FormFieldDependsOn>({
  field_name: "",
  value: "",
});

const field = computed(() => {
  return editorStore.form.form_fields.find(
    (field) => field.field_name === props.fieldName
  );
});

const openAddDepModal = () => {
  dependencyState.value = {
    field_name: "",
    value: "",
  };
  showEditDependencyDialog.value = true;
};

const addDependency = (dependency: FormFieldDependsOn, fieldName: string) => {
  editorStore.addFieldDependency(fieldName, dependency);
  showEditDependencyDialog.value = false;
};
</script>

<style scoped></style>
