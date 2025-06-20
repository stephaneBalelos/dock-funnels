<template>
  <li v-if="field">
    <strong>{{ field?.label }}:</strong>

    <div v-if="value === null" class="text-gray-500">
      <em>Nicht ausgefüllt</em>
    </div>
    <div v-else-if="field.type === 'select'" class="text-gray-700">
      <em>{{ field.options.find(o => o.value === props.value)?.label || value }}</em>
    </div>
    <div v-else-if="field.type === 'text'" class="text-gray-700">
      <em v-if="field.input_type === 'date' && typeof value === 'string'">{{ new Date(value).toLocaleDateString('de-DE') }}</em>
        <em v-else>{{ value }}</em>
    </div>
    <div v-else-if="field.type === 'checkboxList' && Array.isArray(value) " class="text-gray-700">
      <em>
        {{ field.options
          .filter(o => (value ? value : []).includes(o.value))
          .map(o => o.label)
          .join(", ") || "Keine Auswahl" }}
      </em>
    </div>
  </li>
</template>

<script setup lang="ts">
import { useFormSubmissionStateStore } from "@/forms/stores/form";
import type { FormSubmissionField } from "@/types";
import { computed } from "vue";

type Props = {
  field_name: string;
  value: FormSubmissionField['value']
};

const props = defineProps<Props>();
const submissionStateStore = useFormSubmissionStateStore();

const field = computed(() => {
  return submissionStateStore.form.value?.fields.find(
    (f) => f.field_name === props.field_name
  );
});

</script>

<style scoped></style>
