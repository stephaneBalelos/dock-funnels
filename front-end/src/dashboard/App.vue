<script setup lang="ts">
import { inject, onMounted } from "vue";
import { useEditorStore } from "./editor.store";
import FormTitle from "@/components/dashboard/header/FormTitle.vue";
import StepItem from "@/components/dashboard/sidebar-left/StepItem.vue";
import { Icon } from "@iconify/vue";
import FormFlowPreview from "@/components/dashboard/preview/FormFlowPreview.vue";
import FieldEditor from "@/components/dashboard/sidebar-right/FieldEditor.vue";
import Button from "primevue/button";
import { getFormById } from "@/api/wpAjaxApi";
import type { FormState } from "@/types";
import FormExporter from "@/components/dashboard/header/FormExporter.vue";
import FormImporter from "@/components/dashboard/header/FormImporter.vue";
import { FormTestData } from "@/utils";
import SettingsDialog from "@/components/dashboard/settings/SettingsDialog.vue";

// const ajaxUrl = window.DockFunnelsAdmin?.ajaxUrl || '/wp-admin/admin-ajax.php';

const editorStore = useEditorStore();
const endpoint = inject("ajaxUrl") as string | undefined;
const nonce = inject("nonce") as string | undefined;
const editFormId = inject("editFormId") as number | undefined;

onMounted(() => {
  // This is a good place to initialize any global state or perform side effects
  if (!endpoint || !nonce) {
    console.error("API endpoint or nonce not provided");
    editorStore.initEditor(FormTestData); // Initialize with test data if endpoint or nonce is missing
    return;
  }
  editorStore.apiSettings.value.endpoint = endpoint;
  editorStore.apiSettings.value.nonce = nonce;
  if (editFormId) {
    editorStore.apiSettings.value.editFormId = editFormId;
    getFormById(endpoint, nonce, editFormId)
      .then(({ data }) => {
        editorStore.initEditor(data as FormState);
      })
      .catch((error) => {
        console.error("Error loading form:", error);
      });
  } else {
    // Initialize with a new form if no editFormId is provided
    editorStore.initEditor();
  }
});
</script>

<template>
  <div class="h-lvh app-container">
    <div class="header flex justify-between items-center p-4 shadow">
      <div class="flex items-center gap-4">
        <FormTitle v-if="editorStore.form.title" />
        <SettingsDialog v-if="editorStore.form" v-slot="{ openDialog }">
          <Button
            aria-label="Form Settings"
            @click="openDialog"
            size="small"
            severity="secondary"
            class="flex items-center"
          >
            <Icon icon="heroicons:adjustments-horizontal" class="mr-2" />
            Einstellungen
          </Button>
        </SettingsDialog>
      </div>
      <div class="flex gap-2">
        <FormImporter />
        <FormExporter />
        <div v-if="editorStore.editorState.value.isSaving" class="flex items-center">
          <Badge severity="secondary">
            Wird gespeichert
          </Badge>
        </div>
        <Button
          v-if="editorStore.form"
          @click="editorStore.saveFormState"
          size="small"
          severity="secondary"
          class="flex items-center"
          :loading="editorStore.editorState.value.isSaving"
        >
          <Icon icon="heroicons:arrow-down-tray" class="mr-2" />
          Speichern
        </Button>
      </div>
    </div>
    <div class="sidebar-left flex flex-col">
      <div class="flex justify-between items-center mb-4 p-4">
        <div class="text-lg leading-[18px] font-semibold">
          Formular Schritte
        </div>
        <Button
          aria-label="Add Step"
          @click="editorStore.addStep()"
          size="small"
          severity="secondary"
        >
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
      <FieldEditor />
    </div>
    <Toast position="top-center" />
  </div>
</template>

<style scoped></style>
