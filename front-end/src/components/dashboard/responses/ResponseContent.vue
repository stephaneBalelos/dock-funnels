<template>
  <div class="flex flex-col gap-6 p-4 px-6 border border-primary-100 rounded-lg shadow">
    <div
      v-for="(_, stepIndex) in stepCount"
      :key="stepIndex"
      class="step-summary"
    >
      <h3 class="text-md text-primary-900 font-semibold mb-2">
        #{{ stepIndex + 1 }}
      </h3>
      <ul
        v-if="summary.filter(([_, f]) => f.step_index === stepIndex).length > 0"
        class="list-disc pl-5"
      >
        <li
          v-for="[_, field] in summary.filter(
            ([_, f]) => f.step_index === stepIndex
          )"
        >
          {{ field.field_name }}: {{ field.value }}
        </li>
      </ul>
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
import { computed } from "vue";

type Props = {
  response: Record<string, any>;
};

const props = defineProps<Props>();

console.log("Response Content Props:", props.response);

const summary = computed(() => {
  const submittedFields = [...Object.entries(props.response)]
    .filter(([_, field]) => field.value !== null)
    .sort(([_, fieldA], [__, fieldB]) => fieldA.step_index - fieldB.step_index); // Sort by step_index

  return submittedFields;
});

const stepCount = computed(() => {
  return summary.value.reduce((acc, [_, field]) => {
    return Math.max(acc, field.step_index + 1);
  }, 0);
});
</script>

<style scoped></style>
