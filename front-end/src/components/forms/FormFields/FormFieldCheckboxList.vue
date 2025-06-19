<template>
  <div class="flex flex-col gap-4">
    Selected Values: {{ selectedValues }}
    <div class="flex flex-col gap-2">
      <div
        v-for="option in props.field.options.filter(shoulShowOption)"
        :key="field.field_name + option.value"
        class="flex items-center gap-2"
      >
        <Checkbox
          v-model="selectedValues"
          :inputId="field.field_name + option.value"
          :name="option.label"
          :value="option.value"
        />
        <label :for="field.field_name + option.value">{{ option.label }}</label>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type {
  FormFieldCheckboxList,
  FormFieldCheckboxListOption,
} from "@/types";
import { useFormSubmissionStateStore } from "@/forms/stores/form.ts";
import { onMounted, ref, watch } from "vue";
import { Checkbox } from "primevue";

type Props = {
  field: FormFieldCheckboxList;
};

const props = defineProps<Props>();

const selectedValues = ref();

const submissionStateStore = useFormSubmissionStateStore();

onMounted(() => {
  const initialValue = submissionStateStore.formSubmissionFields.value[
    props.field.field_name
  ]?.value as string[] | undefined;
  if (initialValue) {
    submissionStateStore.setFieldValue(props.field.field_name, initialValue);
    selectedValues.value = initialValue;
  }
});

function shoulShowOption(option: FormFieldCheckboxListOption) {
  if (!option.depends_on) {
    return true;
  }
  // Check dependencies
  const dependencies: boolean[] = option.depends_on.map((dep) => {
    const field_name = dep.field_name;
    const dependsOnField =
      submissionStateStore.formSubmissionFields.value[field_name];
    if (!dependsOnField) {
      return false;
    }
    const dependsOnValue = dependsOnField.value;
    if (Array.isArray(dependsOnValue)) {
      return dependsOnValue.includes(dep.value as string);
    }
    return dependsOnValue === dep.value;
  });

  console.log(
    `Option ${option.label} dependencies: ${dependencies.join(", ")}`
  );
  return dependencies.every((dep) => dep);
}

watch(
  selectedValues,
  (newValue) => {
    submissionStateStore.setFieldValue(props.field.field_name, newValue);
  },
  { deep: true }
);
</script>

<style scoped></style>
