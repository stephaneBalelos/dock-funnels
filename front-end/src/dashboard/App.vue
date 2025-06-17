<script setup lang="ts">
import { onMounted } from "vue";
import { useEditorStore } from "./editor.store";
import FormTitle from "@/components/dashboard/header/FormTitle.vue";
import StepItem from "@/components/dashboard/sidebar-left/StepItem.vue";
import { Icon } from "@iconify/vue";
import FormFlowPreview from "@/components/dashboard/preview/FormFlowPreview.vue";
import FieldEditor from "@/components/dashboard/sidebar-right/FieldEditor.vue";
import Button from 'primevue/button';

// const ajaxUrl = window.DockFunnelsAdmin?.ajaxUrl || '/wp-admin/admin-ajax.php';

const editorStore = useEditorStore();

onMounted(() => {
  // This is a good place to initialize any global state or perform side effects
  console.log("App mounted");
  console.log(window.DockFunnelsAdmin);
  editorStore.initEditor();
});
</script>

<template>
  <div class="h-lvh app-container">
    <div class="header flex items-center">
      <div>
        <FormTitle />
      </div>
    </div>
    <div class="sidebar-left flex flex-col">
      <div class="flex justify-between items-center mb-4 p-4">
        <div class="text-lg leading-[18px] font-semibold">
          Formular Schritte
        </div>
        <Button aria-label="Add Step" @click="editorStore.addStep()" size="small" severity="secondary">
          <Icon icon="heroicons:plus" />
        </Button>
      </div>
      <div class="relative flex-1 overflow-y-auto">
        <div class="absolute inset-0 overflow-y-auto px-4">
          <StepItem
            v-for="(step, index) in editorStore.form.form_steps"
            :key="'step-' + index"
            :step-index="index"
            :step="step"
          >
          </StepItem>
        </div>
      </div>
    </div>
    <div class="main relative">
      <div class="toolbar absolute">{{ editorStore.selectedStepIndex }}</div>
      <div class="absolute inset-0 overflow-auto">
        <FormFlowPreview />
      </div>
    </div>
    <div class="sidebar-right flex flex-col">
      <div class="flex justify-between items-center mb-4 p-4">
        <div class="text-lg leading-[18px] font-semibold">
          Formular Feld
        </div>
      </div>
      <div class="flex-1 overflow-y-auto p-4">
        <FieldEditor v-if="editorStore.selectedFieldName.value" :field-name="editorStore.selectedFieldName.value" />
        <div v-else class="text-gray-500 text-center">
          Bitte wählen Sie ein Feld aus, um es zu bearbeiten.
        </div>
      </div>
    </div>
  </div>
</template>

<style scoped></style>
