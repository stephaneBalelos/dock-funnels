<script setup lang="ts">
import { inject, onMounted } from "vue";
import { useEditorStore } from "./editor.store";
import FormTitle from "@/components/dashboard/header/FormTitle.vue";
import { Icon } from "@iconify/vue";
import FormFlowPreview from "@/components/dashboard/preview/FormFlowPreview.vue";
import FieldEditor from "@/components/dashboard/sidebar-right/FieldEditor.vue";
import Button from "primevue/button";
import { getFormById } from "@/api/wpAjaxApi";
import type { FormState } from "@/types";
import { FormTestData } from "@/utils";
import SettingsDialog from "@/components/dashboard/settings/SettingsDialog.vue";
import ImportExportDialog from "@/components/dashboard/header/ImportExportDialog.vue";
import SubmissionsActions from "@/components/dashboard/submissions-actions/SubmissionsActions.vue";
import FormDesignPreview from "@/components/dashboard/preview/FormDesignPreview.vue";

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
  <div class="h-lvh app-container pb-16">
    <div class="header flex justify-between items-center p-4 shadow">
      <div class="flex items-center gap-4">
        <FormTitle v-if="editorStore.form.title" />
      </div>
      <div class="flex gap-2">
        <ImportExportDialog v-if="editorStore.form" v-slot="{ openDialog }">
          <Button
            aria-label="Import/Export Form"
            @click="openDialog"
            size="small"
            severity="secondary"
            class="flex items-center"
          >
            <Icon icon="heroicons:arrow-up-tray" class="mr-2" />
            Import/Export
          </Button>
        </ImportExportDialog>
        <div
          v-if="editorStore.editorState.value.isSaving"
          class="flex items-center"
        >
          <Badge severity="secondary"> Wird gespeichert </Badge>
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
    <div class="toolbar">
      <Toolbar style="border-radius: 0; border: none; box-shadow: none;">
        <template #start>
          <Button class="mr-2" size="small" :severity="editorStore.editorState.value.editorMode === 'edit' ? 'primary' : 'secondary'" @click="editorStore.editorState.value.editorMode = 'edit'">
            Aufbau
          </Button>
          <Button class="mr-2" size="small" :severity="editorStore.editorState.value.editorMode === 'preview' ? 'primary' : 'secondary'" @click="editorStore.editorState.value.editorMode = 'preview'">
            Vorschau
          </Button>
          <Button class="mr-2" size="small" :severity="editorStore.editorState.value.editorMode === 'submission-actions' ? 'primary' : 'secondary'" @click="editorStore.editorState.value.editorMode = 'submission-actions'">
            Flows
          </Button>
        </template>

        <template #end>
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
        </template>
      </Toolbar>
    </div>
    <div v-if="editorStore.editorState.value.editorMode == 'edit'" class="main-content">
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
          <StepList />
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
    </div>
    <div v-else-if="editorStore.editorState.value.editorMode == 'preview'" class="main-content">
      <FormDesignPreview />
    </div>
    <div v-else-if="editorStore.editorState.value.editorMode == 'submission-actions'" class="main-content">
      <SubmissionsActions />
    </div>
    <Toast position="top-center" />
  </div>
</template>

<style scoped></style>
