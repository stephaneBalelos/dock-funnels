<template>
  <Card
    class="flex flex-col border rounded-lg shadow-sm mb-4 w-[300px] min-w-[300px]"
  >
    <template #title>
      <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ step.title }}</h3>
      <p v-if="step.description" class="text-gray-600 mb-4 text-sm">
        {{ step.description }}
      </p>
    </template>
    <template #content>
      <div v-if="fields.length > 0" class="grid grid-cols-1 gap-4">
        <div
          v-for="field in fields"
          :key="'field-' + field.field_name"
          :class="
            'p-2 border rounded bg-gray-50 cursor-pointer hover:bg-gray-100 transition-colors duration-200 ' +
            (editorStore.selectedFieldName.value === field.field_name
              ? 'border-blue-500 bg-blue-50'
              : 'border-gray-200')
          "
          @click="editorStore.setSelectedFieldName(field.field_name)"
        >
          <div class="text-md font-medium text-gray-700">
            {{ field.label }}
            <span v-if="field.required" class="text-red-500">*</span>
          </div>
          <p v-if="field.description" class="text-sm text-gray-500">
            {{ field.description }}
          </p>
          <div class="flex flex-col py-2 gap-1">
            <span class="text-xs text-gray-400"
              >Feldname: {{ field.field_name }}</span
            >
            <span class="text-xs text-gray-400">Type: {{ field.type }}</span>
            <span v-if="field.depends_on" class="text-xs text-gray-400">
              Abhängigkeit(en):
              {{ field.depends_on.map((d) => d.field_name).join(", ") }}
            </span>
            <span v-if="field.default_value" class="text-xs text-gray-400">
              Standardwert: {{ field.default_value }}
            </span>
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
      <div class="flex relative py-4">
        <SpeedDial
          :model="items"
          direction="down"
          :transitionDelay="80"
          :style="{ position: 'absolute', top: '0' }"
          pt:menuitem="m-2"
        >
          <template #button="{ toggleCallback }">
            <Button size="small" severity="secondary" @click="toggleCallback">
              <Icon icon="heroicons:plus" />
              Feld hinzufügen
            </Button>
          </template>
          <template #item="{ item, toggleCallback }">
            <div
              class="flex flex items-center justify-between gap-2 p-2 border rounded border-surface-200 cursor-pointer w-full bg-white hover:bg-white-200 transition-colors duration-200"
              @click="toggleCallback"
            >
              <Icon v-if="item.icon" :icon="item.icon" />
              <span>
                {{ item.label }}
              </span>
            </div>
          </template>
        </SpeedDial>
      </div>
    </template>
  </Card>
</template>

<script setup lang="ts">
import { computed, ref } from "vue";
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

const items = ref([
  {
    label: "Text Field",
    icon: "heroicons:document-text",
    command: () => {
      addFormField("text");
    },
  },
  {
    label: "Select Field",
    icon: "heroicons:list-bullet",
    command: () => {
      addFormField("select");
    },
  },
  {
    label: "Checkbox Field",
    icon: "heroicons:check-circle",
    command: () => {
      addFormField("checkboxList");
    },
  },
  {
    label: "Textarea Field",
    icon: "heroicons:rectangle-group",
    command: () => {
      addFormField("textarea");
    },
  },
]);

const addFormField = (field: string) => {
  const newFieldName = editorStore.addField(props.step_index, field);
  if (newFieldName) {
    editorStore.setSelectedFieldName(newFieldName);
  }
};
</script>

<style scoped></style>
