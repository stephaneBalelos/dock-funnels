<template>
  <div class="flex flex-col">
    <FormField name="field_name" class="flex flex-col gap-1">
      <label class="text-sm font-semibold">
        Welches Feld soll als Abhängigkeit dienen?
      </label>
      <Select
        @change="onChangeDependencyField"
        :options="dependsOnFieldsOptions"
        option-label="label"
        option-value="value"
        placeholder="Feld auswählen"
        size="small"
      />
    </FormField>
    <FormField
      v-if="model?.field_name"
      name="depends_on_value"
      class="flex flex-col gap-1"
    >
      <label class="text-sm font-semibold">
        Welcher Wert des Abhängigkeitsfeldes soll die Bedingung erfüllen?
      </label>
      <Select
        @change="onChangeDependencyValue"
        :options="dependsOnValuesOptions"
        placeholder="Option auswählen"
        size="small"
        option-label="label"
        option-value="value"
      />
    </FormField>
  </div>
</template>

<script setup lang="ts">
import { useEditorStore } from "@/dashboard/editor.store";
import type { FormFieldDependsOn } from "@/types";
import type { SelectChangeEvent } from "primevue";
import { computed } from "vue";

type Props = {
  field_name: string;
};

const props = defineProps<Props>();

const model = defineModel<FormFieldDependsOn>({
    default: () => ({
        field_name: "",
        value: "",
    }),
    type: Object,
});

const editorStore = useEditorStore();

const field = computed(() => {
  return editorStore.form.form_fields.find(
    (field) => field.field_name === props.field_name
  );
});

const dependsOnFieldsOptions = computed(() => {
  // Get all fields that are not the current field and where the step index is lower or equal to the current field's step index
  // These fields can be used as dependencies if they are select or checkbox fields
  return editorStore.form.form_fields
    .filter((f) => {
      if (!field.value) return false;
      return (
        f.field_name !== field.value.field_name &&
        f.step_index <= field.value.step_index
      );
    })
    .filter((f) => f.type === "select" || f.type === "checkboxList")
    .map((f) => ({
      label: f.label,
      value: f.field_name,
      description: f.description,
    }));
});

const dependsOnValuesOptions = computed(() => {
  // Get the currentlly selected field_name's options if it is a select or checkboxList field
  const currentField = editorStore.form.form_fields.find(
    (f) => f.field_name === model.value?.field_name
  );
  if (!currentField) return [];
  if (currentField.type !== "select" && currentField.type !== "checkboxList") {
    return [];
  }
  return currentField.options
    ? currentField.options.map((option) => ({
        label: option.label,
        value: option.value,
      }))
    : [];
});

const onChangeDependencyField = ($event: SelectChangeEvent) => {
  if (model.value) {
    model.value.field_name = $event.value as string;
    model.value.value = ''; // Reset the value when changing the field
  }
};

const onChangeDependencyValue = ($event: SelectChangeEvent) => {
  if (model.value) {
    model.value.value = $event.value as string;
  }
};
</script>

<style scoped></style>
