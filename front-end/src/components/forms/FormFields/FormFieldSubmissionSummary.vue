<template>
  <div class="flex flex-col gap-6 p-4 px-6 border border-surface-200 rounded-lg shadow bg-surface-50">
    <label
      v-if="field.field_settings?.hide_label !== true"
      class="text-surface-900 text-lg leading-none mb-2"
    >
      {{ props.field.label }}
      <p
        v-if="props.field.description"
        class="text-surface-600 text-sm leading-none mt-1"
      >
        {{ props.field.description }}
      </p>
    </label>
    <div
      v-for="(step, stepIndex) in submissionStateStore.form.value?.form_steps"
      :key="stepIndex"
      class="step-summary"
    >
      <h3 class="text-md text-surface-900 font-semibold mb-2">
        #{{ stepIndex + 1 }} {{ step.title }}
      </h3>
      <ul v-if="summary.filter(
            ([_, f]) => f.step_index === stepIndex
          ).length > 0" class="list-disc pl-5">
        <FormFieldSubmissionSummaryItem
          v-for="([_, field]) in summary.filter(
            ([_, f]) => f.step_index === stepIndex
          )"
          :key="field.field_name"
          :field_name="field.field_name"
          :value="field.value"
        />
      </ul>
      <div v-else class="text-surface-500">
        Keine Felder in diesem Schritt ausgefüllt.
      </div>
    </div>
    <div v-if="summary.length === 0" class="text-surface-500">
      Keine Felder ausgefüllt.
    </div>
  </div>
</template>

<script setup lang="ts">
import { useFormSubmissionStateStore } from "@/forms/stores/submission.store";
import type { FormFieldSubmissionSummary } from "@/types";
import { computed } from "vue";

type Props = {
  field: FormFieldSubmissionSummary;
};

const props = defineProps<Props>();
const submissionStateStore = useFormSubmissionStateStore();

const summary = computed(() => {
  const submittedFields = [...submissionStateStore.formSubmissionFields.value.entries()]
    .filter(([_, field]) => field.value !== null)
    .filter(([_, field]) => {
      return props.field.show_full_summary
        ? true
        : field.step_index < props.field.step_index;
    })
    .sort(([_, fieldA], [__, fieldB]) => fieldA.step_index - fieldB.step_index); // Sort by step_index

    return submittedFields
});
</script>

<style scoped></style>
