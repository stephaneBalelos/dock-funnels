<template>
  <div class="flex flex-col gap-4">
    <div :class="classSettings.base">
      <label :class="classSettings.label">
        {{ props.field.label }}
        <span v-if="props.field.required" class="text-red-500">*</span>
        <p
          v-if="props.field.description"
          class="text-primary-700 text-sm font-normal leading-none mt-1"
        >
          {{ props.field.description }}
        </p>
      </label>
      <div
        v-if="
          submissionStateStore.currentStepErrors.value.find(
            (e) => e.joined_path === props.field.field_name
          )
        "
        class="text-red-500 text-sm mt-2"
      >
        {{
          submissionStateStore.currentStepErrors.value.find(
            (e) => e.joined_path === props.field.field_name
          )?.message
        }}
      </div>
      <div :class="classSettings.optionsContainer">
        <OptionCheckboxBox
          v-for="option in props.field.options.filter(
            submissionStateStore.shoulShowChechboxListOption
          )"
          :key="field.field_name + option.value"
          :label="option.label"
          :description="option.description"
          v-model="selectedValues"
          :inputId="field.field_name + option.value"
          :name="option.label"
          :value="option.value"
        />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { FormFieldCheckboxList } from "@/types";
import { useFormSubmissionStateStore } from "@/forms/stores/submission.store";
import { computed, onMounted, ref, watch } from "vue";
import OptionCheckboxBox from "./Inputs/OptionCheckboxBox.vue";

type Props = {
  field: FormFieldCheckboxList;
};

const props = defineProps<Props>();

const selectedValues = ref([] as string[]);
const settings = props.field.field_settings || {};

const submissionStateStore = useFormSubmissionStateStore();

onMounted(() => {
  const initialValue = submissionStateStore.formSubmissionFields.value.get(
    props.field.field_name
  )?.value as string[] | undefined;
  const allowedValues = props.field.options
    .filter(submissionStateStore.shoulShowChechboxListOption)
    .map((option) => option.value);

  if (initialValue) {
    // Filter initial value to only include allowed values
    const filteredInitialValue = initialValue.filter((value) =>
      allowedValues.includes(value)
    );
    submissionStateStore.setFieldValue(
      props.field.field_name,
      filteredInitialValue
    );
    selectedValues.value = filteredInitialValue;
  }
});

watch(
  selectedValues,
  (newValue) => {
    submissionStateStore.setFieldValue(props.field.field_name, newValue);
    console.log(newValue);
  },
  { deep: true }
);

const classSettings = computed(() => {
  let base = `flex flex-col py-4`;
  let label = `text-primary-900 text-lg font-semibold leading-none mb-3`;
  let optionsContainer = `w-full grid grid-cols-1 md:grid-cols-2 gap-4`;
  if (settings.align) {
    base += ` text-${settings.align}`;
    base += ` items-${settings.align}`;
    optionsContainer += ` items-${settings.align}`;
  }
  if (settings.text_align) {
    label += ` text-${settings.text_align}`;
  }

  return {
    base,
    label,
    optionsContainer,
  };
});
</script>

<style scoped></style>
