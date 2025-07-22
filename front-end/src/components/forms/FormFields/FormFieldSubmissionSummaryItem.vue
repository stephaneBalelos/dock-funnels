<template>
  <li v-if="field" class="text-primary-900">
    <strong>{{ field?.label }}:</strong>

    <div v-if="value === null" class="text-primary-500">
      <em>Nicht ausgefüllt</em>
    </div>
    <div v-else-if="field.type === 'select'" class="text-primary-700">
      <em>{{
        field.options.find((o) => o.value === props.value)?.label || value
      }}</em>
    </div>
    <div v-else-if="field.type === 'text'" class="text-primary-700">
      <em v-if="field.input_type === 'date' && typeof value === 'string'">{{
        new Date(value).toLocaleDateString("de-DE")
      }}</em>
      <em v-else>{{ value }}</em>
    </div>
    <ul
      v-else-if="field.type === 'checkboxList' && Array.isArray(value)"
      class="text-primary-700 list-disc"
    >
      <li
        v-for="(val, index) in field.options
          .filter((o) => (value ? value : []).includes(o.value))
          .map((o) => o.label)"
          class="ml-4"
      >
        {{ val }}
      </li>
    </ul>
  </li>
</template>

<script setup lang="ts">
import { useFormSubmissionStateStore } from "@/forms/stores/submission.store";
import type { FormSubmissionField } from "@/types";
import { computed } from "vue";

type Props = {
  field_name: string;
  value: FormSubmissionField["value"];
};

const props = defineProps<Props>();
const submissionStateStore = useFormSubmissionStateStore();

const field = computed(() => {
  return submissionStateStore.form.value?.form_fields.find(
    (f) => f.field_name === props.field_name
  );
});
</script>

<style scoped></style>
