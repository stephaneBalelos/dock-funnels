<template>
  <div class="flex items-center mb-2">
    <div v-if="fieldName && fieldValue"
      class="mr-2 bg-primary-100 text-xs font-semibold text-surface-900 px-2 py-1 rounded-md flex-1"
    >
        Wenn "{{ fieldName }}" ist gleich "{{ fieldValue}}"
    </div>
    <div v-else class="text-xs font-semibold text-red-500 flex-1">
      Diese Abhängigkeit ist ungültig weil dieses Feld nicht existiert oder der Wert nicht gesetzt ist.
    </div>

    <Button severity="danger" size="small" @click="$emit('onRemoveDependency', dep_idx)">
      <Icon icon="heroicons:trash-16-solid" />
    </Button>
  </div>
</template>

<script setup lang="ts">
import { useEditorStore } from "@/dashboard/editor.store";
import { Icon } from "@iconify/vue";
import { computed } from "vue";

type Props = {
  dep_idx: number;
  field_name: string;
  field_value?: string;
  option_value: string;
};

const props = defineProps<Props>();
const $emit = defineEmits(["onRemoveDependency"]);
const editorStore = useEditorStore();

const fieldName = computed(
  () =>
    editorStore.form.form_fields.find(
      (field) => field.field_name === props.field_name
    )?.label || props.field_name
);


const fieldValue = computed(() => {
  const field = editorStore.form.form_fields.find(
    (field) => field.field_name === props.field_name
  );
  if (field?.type === "select") {
    return field.options.find(
      (option) => option.value === props.field_value
    )?.label || null
  }
  if (field?.type === "checkboxList") {
    return field.options.find(
      (option) => option.value === props.field_value
    )?.label || null
  }
    return props.field_value || "";
});
</script>

<style scoped></style>
