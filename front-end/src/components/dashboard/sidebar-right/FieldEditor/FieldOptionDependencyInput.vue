<template>
  <div>
    <slot :toggleDialog="toggleDialog" />
      <Dialog
        v-model:visible="visible"
        modal
        header="Options Abhängigkeit"
        :style="{ width: '25rem' }"
        class="dock-funnels-root"
      >
        <div v-if="field && (field.type === 'select' || field.type === 'checkboxList')" class="flex flex-col">
          <FormField name="field_name" class="flex flex-col gap-1">
            <label class="text-sm font-semibold">
              Welches Feld soll als Abhängigkeit dienen?
            </label>
            <Select
              @change="onChangeDependencyField"
              v-model="model.field_name"
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
              v-model="model.value"
              :options="dependsOnValuesOptions"
              placeholder="Option auswählen"
              size="small"
              option-label="label"
              option-value="value"
            />
          </FormField>
        </div>
        <div class="flex justify-end gap-2">
          <Button
            type="button"
            label="Cancel"
            severity="secondary"
            @click="visible = false"
          ></Button>
          <Button type="button" label="Hinzufügen" @click="addDependency" :disabled="!(model && !!model.field_name && !!model.value)"></Button>
        </div>
      </Dialog>
  </div>
</template>

<script setup lang="ts">
import { useEditorStore } from "@/dashboard/editor.store";
import type { FormFieldDependsOn } from "@/types";
import type { SelectChangeEvent } from "primevue";
import { computed, ref } from "vue";

type Props = {
  field_name: string;
  option_value: string;
};

const props = defineProps<Props>();
const visible = ref(false);
const model = ref<FormFieldDependsOn>({
  field_name: "",
  value: "",
});
const editorStore = useEditorStore();

const $emit = defineEmits(["dependencyAdded"]);

const field = computed(() => {
  const field = editorStore.form.form_fields.find(
    (field) => field.field_name === props.field_name
  )
  if(field?.type !== "select" && field?.type !== "checkboxList") {
    console.warn(`Field ${props.field_name} is not a select or checkboxList field.`);
    return null;
  }
  return field;
});

const option = computed(() => {
  if (!field.value) return null;
  return field.value.options.find(
    (option) => option.value === props.option_value
  );
});

const dependsOnFieldsOptions = computed(() => {
  // Get all fields that are not the current field and where the step index is lower or equal to the current field's step index
  // Make sure the fields are not already selected as dependencies
  // These fields can be used as dependencies if they are select or checkbox fields
  return editorStore.form.form_fields
    .filter((f) => {
      if (!field.value) return false;
      return (
        f.field_name !== field.value.field_name &&
        f.step_index < field.value.step_index
      );
    })
    .filter((f) => f.type === "select" || f.type === "checkboxList")
    .filter((f) => {
      if (!option.value) return true;
      return option.value.depends_on.every(
        (d) => d.field_name !== f.field_name
      );
    }).map((f) => ({
      label: f.label,
      value: f.field_name,
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

const toggleDialog = () => {
  visible.value = !visible.value;
};

const onChangeDependencyField = ($event: SelectChangeEvent) => {
  if (model.value) {
    model.value.field_name = $event.value as string;
    model.value.value = ""; // Reset the value when changing the field
  }
};

const onChangeDependencyValue = ($event: SelectChangeEvent) => {
  if (model.value) {
    model.value.value = $event.value as string;
  }
};

const addDependency = () => {
  if (!model.value || !model.value.field_name || !model.value.value) {
    console.warn("Dependency field or value is not set");
    return;
  }
  if (!field.value) {
    console.warn("Field is not set");
    return;
  }
  if (field.value && (field.value.type === "select" || field.value.type === "checkboxList")) {
    editorStore.addOptionDependency(props.field_name, props.option_value, {
      field_name: model.value.field_name,
      value: model.value.value,
    });
  }
  $emit("dependencyAdded", {
    field_name: model.value.field_name,
    value: model.value.value,
  });
  visible.value = false;
  model.value = {
    field_name: "",
    value: "",
  } as FormFieldDependsOn; // Reset the model after adding the dependency
};
</script>

<style scoped></style>
