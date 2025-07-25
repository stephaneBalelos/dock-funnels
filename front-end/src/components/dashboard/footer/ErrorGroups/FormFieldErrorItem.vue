<template>
  <div v-if="field" class="flex gap-2 text-red-600">
    <b>(Schritt {{ field.step_index + 1 }}){{ field.label }}:</b>
    <ul>
      <li v-for="(error, index) in props.errors" :key="index">{{ error }}</li>
    </ul>
  </div>
</template>

<script setup lang="ts">
import { useEditorStore } from "@/dashboard/editor.store";
import { computed } from "vue";

type Props = {
  errors: Record<string, string>;
  fieldName: string;
};
const props = defineProps<Props>();

const editorStore = useEditorStore();

const field = computed(() => {
  return editorStore.form.form_fields.find(
    (field) => field.field_name === props.fieldName
  );
});

console.log(editorStore.form.form_fields);
</script>

<style scoped></style>
