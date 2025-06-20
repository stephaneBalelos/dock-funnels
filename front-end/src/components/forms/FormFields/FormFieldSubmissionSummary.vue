<template>
  <div class="flex flex-col gap-6 p-4 px-6 border rounded-lg bg-white shadow">
    <label
      v-if="field.field_settings?.hide_label !== true"
      class="text-stone-700 text-lg leading-none mb-2"
      :class="{ 'text-red-500': props.field.required }"
    >
      {{ props.field.label }}
      <p
        v-if="props.field.description"
        class="text-stone-500 text-sm leading-none mt-1"
      >
        {{ props.field.description }}
      </p>
    </label>
    <div
      v-for="([_, fields], stepIndex) in Object.entries(summary)"
      :key="stepIndex"
      class="step-summary"
    >
      <h3 class="text-md font-semibold mb-2">
        #{{ stepIndex + 1 }} {{ getFieldStepNameByIndex(stepIndex) }}
      </h3>
      <ul class="list-disc pl-5">
        <FormFieldSubmissionSummaryItem
          v-for="field in fields"
          :key="field.fieldName"
          :field_name="field.fieldName"
          :value="field.value"
        />
      </ul>
    </div>
    <div v-if="Object.keys(summary).length === 0" class="text-gray-500">
      No fields submitted yet.
    </div>
  </div>
</template>

<script setup lang="ts">
import { useFormSubmissionStateStore } from "@/forms/stores/form";
import type { FormFieldSubmissionSummary } from "@/types";
import { computed } from "vue";

type Props = {
  field: FormFieldSubmissionSummary;
};

const props = defineProps<Props>();
const submissionStateStore = useFormSubmissionStateStore();

const summary = computed(() => {
  const submittedFields = Object.entries(
    submissionStateStore.formSubmissionFields.value
  )
    .filter(([_, field]) => field.value !== null)
    .filter(([_, field]) => {
      return props.field.show_full_summary
        ? true
        : field.step_index < props.field.step_index;
    })
    .sort(([_, fieldA], [__, fieldB]) => fieldA.step_index - fieldB.step_index); // Sort by step_index
  // Group by step_index
  const groupedFields = submittedFields.reduce((acc, [fieldName, field]) => {
    if (!acc[field.step_index]) {
      acc[field.step_index] = [];
    }
    acc[field.step_index].push({ fieldName, ...field });
    return acc;
  }, {} as Record<number, Array<{ fieldName: string; value: any }>>);

  return groupedFields;
});

const getFieldStepNameByIndex = (stepIndex: number): string => {
  const step = submissionStateStore.form.value?.form_steps[stepIndex];
  return step ? step.title : `Step ${stepIndex + 1}`;
};
</script>

<style scoped></style>
