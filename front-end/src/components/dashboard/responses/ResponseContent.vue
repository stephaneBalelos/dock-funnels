<template>
  <div
    class="flex flex-col gap-6 p-4 border border-primary-100 rounded-lg shadow"
  >
    <div
      v-for="(step, stepIndex) in props.formSteps"
      :key="stepIndex"
      class="step-summary"
    >
      <h3 class="text-md text-primary-900 font-semibold mb-2">
        #{{ stepIndex + 1 }} {{ step.title }}
      </h3>
      <div 
        v-if="summary.filter(([_, f]) => f.step_index === stepIndex).length > 0"
        class="list-disc pl-5"
      >
        <div
          v-for="[_, field] in summary.filter(
            ([_, f]) => f.step_index === stepIndex
          )"
        >
          <TextResponse
            v-if="field.type === 'text'"
            :field_name="field.field_name"
            :input_type="field.input_type"
            :label="field.label"
            :step_index="field.step_index"
            :type="field.type"
            :value="field.value"
          />
          <SelectResponse
            v-else-if="field.type === 'select'"
            :field_name="field.field_name"
            :label="field.label"
            :step_index="field.step_index"
            :type="field.type"
            :value="field.value"
            :value_label="field.value_label"
          />
          <CheckboxListResponse
            v-else-if="field.type === 'checkboxList'"
            :field_name="field.field_name"
            :label="field.label"
            :step_index="field.step_index"
            :type="field.type"
            :value="field.value"
            :value_labels="field.value_labels"
          />
          <div v-else class="text-primary-900 mb-2">
            <label :for="field.field_name" class="mr-2 font-semibold">
              {{ field.label }}:
            </label>
            <span class="text-primary-900">
              {{ field.value || 'Kein Wert angegeben' }}
            </span>
          </div>
        </div>
      </div>
      <div v-else class="text-primary-600">
        Keine Felder in diesem Schritt ausgefüllt.
      </div>
    </div>
    <div v-if="summary.length === 0" class="text-primary-500">
      Keine Felder ausgefüllt.
    </div>
  </div>
</template>

<script setup lang="ts">
import type { FormStep } from "@/types";
import { computed } from "vue";

type Props = {
  formSteps: FormStep[];
  response: Record<string, any>;
};

const props = defineProps<Props>();

const summary = computed(() => {
  const submittedFields = [...Object.entries(props.response)]
    .filter(([_, field]) => field.value !== null)
    .sort(([_, fieldA], [__, fieldB]) => fieldA.step_index - fieldB.step_index); // Sort by step_index

  return submittedFields;
});

console.log("Response Summary:", summary.value);
</script>

<style scoped></style>
