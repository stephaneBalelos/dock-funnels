<template>
  <Card
    class="flex flex-col border rounded-lg shadow-sm mb-4 w-[300px] min-w-[300px]"
  >
    <template #title>
      <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ step.title }}</h3>
      <p v-if="step.description" class="text-gray-600 mb-4 text-sm">{{ step.description }}</p>
    </template>
    <template #content>
      <div v-if="fields.length > 0" class="grid grid-cols-1 gap-4">
        <div
          v-for="field in fields"
          :key="'field-' + field.field_name"
          class="p-2 border rounded bg-gray-50 cursor-pointer hover:bg-gray-100 transition-colors duration-200"
          @click="editorStore.setSelectedFieldName(field.field_name)"
        >
          <div class="text-sm font-medium text-gray-700">
            {{ field.label }} <span>{{ field.field_name }}</span>
          </div>
          <p v-if="field.description" class="text-xs text-gray-500">{{ field.description }}</p>
          <div class="">
            <span class="text-xs text-gray-400">Type: {{ field.type }}</span>
          </div>
        </div>
      </div>
      <div
        v-else
        class="bg-orange-50 dark:bg-orange-400 dark:bg-opacity-10 text-orange-500 dark:text-orange-400 my-4 p-2"
      >
        <p class="text-sm">
          Keine Felder in diesem Schritt. Klicken Sie auf "Feld hinzufügen", um
          ein neues Feld zu erstellen.
        </p>
      </div>
    </template>
    <template #footer>
      <div class="flex">
        <Button size="small" severity="secondary" @click="addFormField('text')">
          <Icon icon="heroicons:plus" />
          Feld hinzufügen
        </Button>
      </div>
    </template>
  </Card>
</template>

<script setup lang="ts">
import { computed } from "vue";
import { useEditorStore } from "@/dashboard/editor.store";
import { Icon } from "@iconify/vue";

type Props = {
  step_index: number;
};
const props = defineProps<Props>();
const editorStore = useEditorStore();

const step = computed(() => {
  return editorStore.formSteps.value[props.step_index];
});

const fields = computed(() => {
  return editorStore.form.fields.filter(
    (fields) => fields.step_index === props.step_index
  );
});

const addFormField = (field: string) => {
  const newFieldName = editorStore.addField(props.step_index, field);
  if (newFieldName) {
    editorStore.setSelectedFieldName(newFieldName);
  }
};
</script>

<style scoped></style>
