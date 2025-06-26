<template>
  <div class="flex flex-col gap-4">
    <div class="flex flex-col gap-2">
      <label
        class="text-stone-700 text-lg leading-none mb-3"
        :class="{ 'text-red-500': props.field.required }"
      >
        {{ props.field.label }}
        <span v-if="props.field.required" class="text-red-500">*</span>
        <p
          v-if="props.field.description"
          class="text-stone-500 text-sm leading-none mt-1"
        >
          {{ props.field.description }}
        </p>
      </label>
      <Message
        v-if="submissionStateStore.currentStepErrors.value.find(e => e.joined_path === props.field.field_name)"
        severity="error"
        variant="simple"
        size="small"
        class="mb-4"
      >
        {{ submissionStateStore.currentStepErrors.value.find(e => e.joined_path === props.field.field_name)?.message }}
      </Message>
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
        <div class="flex flex-col">
          <label :for="field.field_name + option.value">{{ option.label }}</label>
          <p class="text-sm text-gray-500">{{ option.description }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type {
  FormFieldCheckboxList,
  FormFieldCheckboxListOption,
} from "@/types";
import { useFormSubmissionStateStore } from "@/forms/stores/submission.store";
import { onMounted, ref, watch } from "vue";
import { Checkbox } from "primevue";

type Props = {
  field: FormFieldCheckboxList;
};

const props = defineProps<Props>();

const selectedValues = ref();

const submissionStateStore = useFormSubmissionStateStore();

onMounted(() => {
  const initialValue = submissionStateStore.formSubmissionFields.value.get(props.field.field_name)?.value as string[] | undefined;
  const allowedValues = props.field.options.filter(shoulShowOption).map((option) => option.value);

  if (initialValue) {
    // Filter initial value to only include allowed values
    const filteredInitialValue = initialValue.filter(value => allowedValues.includes(value));
    submissionStateStore.setFieldValue(props.field.field_name, filteredInitialValue);
    selectedValues.value = filteredInitialValue;
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
      submissionStateStore.formSubmissionFields.value.get(field_name);
    if (!dependsOnField) {
      return false;
    }
    const dependsOnValue = dependsOnField.value;
    if (Array.isArray(dependsOnValue)) {
      return dependsOnValue.includes(dep.value as string);
    }
    return dependsOnValue === dep.value;
  });

  return dependencies.every((dep) => dep);
}

watch(
  selectedValues,
  (newValue) => {
    submissionStateStore.setFieldValue(props.field.field_name, newValue);
    console.log(newValue);
  },
  { deep: true }
);
</script>

<style scoped></style>
