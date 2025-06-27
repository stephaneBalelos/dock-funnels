<script setup lang="ts">
import { inject, onMounted } from "vue";
import { useEditorStore } from "./editor.store";
import FormTitle from "@/components/dashboard/header/FormTitle.vue";
import StepItem from "@/components/dashboard/sidebar-left/StepItem.vue";
import { Icon } from "@iconify/vue";
import FormFlowPreview from "@/components/dashboard/preview/FormFlowPreview.vue";
import FieldEditor from "@/components/dashboard/sidebar-right/FieldEditor.vue";
import Button from "primevue/button";
import {
  createForm,
  deleteForm,
  getFormById,
  updateForm,
} from "@/api/wpAjaxApi";
import type { Form } from "@/types";
import FormExporter from "@/components/dashboard/header/FormExporter.vue";
import FormImporter from "@/components/dashboard/header/FormImporter.vue";
import { useToast } from "primevue/usetoast";
import { FormTestData } from "@/utils";
import SettingsDialog from "@/components/dashboard/settings/SettingsDialog.vue";

// const ajaxUrl = window.DockFunnelsAdmin?.ajaxUrl || '/wp-admin/admin-ajax.php';

const editorStore = useEditorStore();
const endpoint = inject("ajaxUrl") as string | undefined;
const nonce = inject("nonce") as string | undefined;
const editFormId = inject("editFormId") as number | undefined;
const toast = useToast();

const saveForm = async () => {
  if (!editorStore.form) {
    console.error("No form to save");
    return;
  }

  const formdata: Form = {
    id: editorStore.form.id,
    title: editorStore.form.title,
    intro_step: editorStore.form.intro_step,
    description: editorStore.form.description,
    form_steps: editorStore.form.form_steps,
    fields: editorStore.form.fields,
  };

  if (!endpoint || !nonce) {
    console.error("API endpoint or nonce not provided");
    toast.add({
      severity: "error",
      summary: "Fehler",
      detail: "API-Endpunkt oder Nonce nicht verfügbar.",
    });
    return;
  }

  try {
    // If editFormId is provided, we update the existing form
    if (editFormId) {
      formdata.id = editFormId;
      const response = await updateForm(
        endpoint,
        nonce,
        editFormId,
        JSON.stringify(formdata)
      );
      if (!response.success) {
        console.error("Failed to update form:", response);
        toast.add({
          severity: "error",
          summary: "Fehler",
          detail: "Formular konnte nicht aktualisiert werden.",
        });
        return;
      }
      toast.add({
        severity: "success",
        summary: "Erfolg",
        detail: "Formular erfolgreich aktualisiert.",
      });
    } else {
      // If no editFormId, we create a new form
      const response = await createForm(
        endpoint,
        nonce,
        JSON.stringify(formdata)
      );
      if (!response.success) {
        console.error("Failed to create form:", response);
        toast.add({
          severity: "error",
          summary: "Fehler",
          detail: "Formular konnte nicht erstellt werden.",
        });
        return;
      }
      // Update the editor store with the new form ID
      editorStore.form.id = response.data.id;
      toast.add({
        severity: "success",
        summary: "Erfolg",
        detail: "Formular erfolgreich erstellt.",
      });
      // Reload the page or redirect to the form list
      window.location.href = `/wp-admin/admin.php?page=dock-funnels&form_id=${response.data.form_id}`;
    }
  } catch (error) {
    console.error("Error saving form:", error);
    toast.add({
      severity: "error",
      summary: "Fehler",
      detail: "Ein Fehler ist beim Speichern des Formulars aufgetreten.",
    });
  }
};

const formDelete = async () => {
  if (!endpoint || !nonce) {
    console.error("API endpoint or nonce not provided");
    return;
  }

  if (!editFormId) {
    console.error("No form ID provided for deletion");
    return;
  }

  if (!confirm("Sind Sie sicher, dass Sie dieses Formular löschen möchten?")) {
    return;
  }

  try {
    const response = await deleteForm(endpoint, nonce, editFormId);
    if (response.success) {
      console.log("Form deleted successfully");
      // Redirect the user to the forms list or another page
      window.location.href = "/wp-admin/admin.php?page=dock-funnels";
    } else {
      console.error("Failed to delete form:", response);
      toast.add({
        severity: "error",
        summary: "Fehler",
        detail: "Formular konnte nicht gelöscht werden.",
      });
    }
  } catch (error) {
    console.error("Error deleting form:", error);
    toast.add({
      severity: "error",
      summary: "Fehler",
      detail: "Ein Fehler ist beim Löschen des Formulars aufgetreten.",
    });
  }
};

onMounted(() => {
  // This is a good place to initialize any global state or perform side effects
  console.log(window.DockFunnelsAdmin);
  if (editFormId) {
    if (!endpoint || !nonce) {
      console.error("API endpoint or nonce not provided");
      return;
    }
    getFormById(endpoint, nonce, editFormId)
      .then(({ data }) => {
        editorStore.initEditor(JSON.parse(data.form_data) as Form);
        console.log("Form loaded:", editorStore.form);
      })
      .catch((error) => {
        console.error("Error loading form:", error);
      });
  } else {
    // Initialize with a new form if no editFormId is provided
    editorStore.initEditor(FormTestData);
  }
});
</script>

<template>
  <div class="h-lvh app-container">
    <div class="header flex justify-between items-center p-4 shadow">
      <div class="flex items-center gap-4">
        <FormTitle v-if="editorStore.form.title" />
        <SettingsDialog v-if="editorStore.form" />
      </div>
      <div class="flex gap-2">
        <FormImporter />
        <FormExporter />
        <Button
          v-if="editorStore.form"
          @click="saveForm"
          size="small"
          severity="secondary"
          class="flex items-center"
        >
          <Icon icon="heroicons:arrow-down-tray" class="mr-2" />
          Speichern
        </Button>
        <Button
          v-if="editorStore.form"
          @click="formDelete"
          size="small"
          severity="danger"
          class="flex items-center"
        >
          <Icon icon="heroicons:trash" class="mr-2" />
          Löschen
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
    <Toast />
  </div>
</template>

<style scoped></style>
