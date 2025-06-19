<template>
  <div class="flex flex-col py-4">
    Selected Value: {{ selectedValue }}
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
    <div class="flex flex-col gap-4">
      <div
        v-for="option in props.field.options.filter(shoulShowOption)"
        :key="props.field.field_name + option.value"
        class="flex items-center gap-2"
      >
        <RadioButton
          v-model="selectedValue"
          :inputId="props.field.field_name + option.value"
          :name="option.label"
          :value="option.value"
        />
        <label :for="props.field.field_name + option.value">{{ option.label }}</label>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { FormFieldSelect, FormFieldSelectOption } from "@/types";
import { onMounted, ref, watch } from "vue";
import { useFormSubmissionStateStore } from "@/forms/stores/form.ts";
import { RadioButton } from "primevue";

type Props = {
  field: FormFieldSelect;
};

const props = defineProps<Props>();

const selectedValue = ref<string | null>(null);

const submissionStateStore = useFormSubmissionStateStore();



onMounted(() => {
  console.log("FormFieldSelect onMounted", props.field.field_name);
  const field =
    submissionStateStore.formSubmissionFields.value[props.field.field_name];
  if (field) {
    submissionStateStore.setFieldValue(props.field.field_name, field.value);
    selectedValue.value = field.value as string | null;
  }
});

function shoulShowOption(option: FormFieldSelectOption): boolean {
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

  return dependencies.every((dep) => dep);
}

watch(selectedValue, (newValue) => {
  console.log("Selected value changed:", newValue);
  submissionStateStore.setFieldValue(props.field.field_name, newValue);
});
</script>

<style scoped></style>
