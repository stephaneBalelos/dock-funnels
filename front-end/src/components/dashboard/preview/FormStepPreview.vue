<template>
  <Card
    v-if="step"
    class="flex flex-col border rounded-lg shadow-sm mb-4 w-full min-w-[450px] max-w-[600px] mx-auto"
  >
    <template #title>
      <h3 class="text-lg font-semibold text-gray-800 mb-2">{{ step.title }}</h3>
      <p v-if="step.description" class="text-gray-600 mb-4 text-sm">
        {{ step.description }}
      </p>
    </template>
    <template #content>
      <div v-if="fields.length > 0" class="grid grid-cols-1 gap-4 mb-4">
        <FormStepFieldPreview
          v-for="(field, idx) in fields"
          :key="field.field_name"
          :field-name="field.field_name"
          :step_index="props.step_index"
          :isFirstInStep="idx === 0"
          :isLastInStep="idx === fields.length - 1"
        />
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
          class="w-full"
        >
          <template #button="{ toggleCallback }">
            <Button size="small" severity="secondary" @click="toggleCallback">
              <Icon icon="heroicons:plus" />
              Feld hinzufügen
            </Button>
          </template>
          <template #item="{ item, toggleCallback }">
            <div
              class="flex items-center justify-between gap-2 p-2 border rounded border-surface-200 cursor-pointer w-full bg-white hover:bg-white-200 transition-colors duration-200"
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
  return editorStore.form.form_fields.filter(
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
  {
    label: "Zusammenfassung Feld",
    icon: "heroicons:document-text",
    command: () => {
      addFormField("submissionSummary");
    },
  },
  {
    label: "Custom HTML",
    icon: "heroicons:document-text",
    command: () => {
      addFormField("customHtml");
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
