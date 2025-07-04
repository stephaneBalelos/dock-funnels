<template>
  <div class="flex w-full">
    <div class="relative w-full max-w-4xl mx-auto p-4 flex-1">
      <div class="absolute inset-0 overflow-auto m-4">
        <DockFunnelForm
          v-if="form"
          :form="form"
          class="dockfunnel-form-preview w-full max-w-4xl mx-auto"
        />
      </div>
    </div>
    <div class="bg-surface-50 w-80 p-4 hidden md:block">
      <Accordion value="0">
        <AccordionPanel value="0">
          <AccordionHeader>
            <div class="flex items-center text-surface-700 hover:text-surface-900">
                <Icon icon="heroicons:paint-brush" class="mr-2" />
                <span>Farben</span></div>
        </AccordionHeader>
          <AccordionContent>
            <FormDesignSettingsColors v-if="editorStore.form.form_settings.design_settings" :colors="editorStore.form.form_settings.design_settings.colors"/>
          </AccordionContent>
        </AccordionPanel>
        <AccordionPanel value="1">
          <AccordionHeader>Header Settings</AccordionHeader>
          <AccordionContent>
            <FormDesignSettingsHeader
              v-if="editorStore.form.form_settings.design_settings"
              :header="editorStore.form.form_settings.design_settings.header"
            />
          </AccordionContent>
        </AccordionPanel>
        <AccordionPanel value="2">
          <AccordionHeader>Footer Einstellungen</AccordionHeader>
          <AccordionContent>
            <FormDesignSettingsFooter
              v-if="editorStore.form.form_settings.design_settings"
              :footer="editorStore.form.form_settings.design_settings.footer"
            />
          </AccordionContent>
        </AccordionPanel>
      </Accordion>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useEditorStore } from "@/dashboard/editor.store.ts";
import { useFormSubmissionStateStore } from "@/forms/stores/submission.store";
import type { Form } from "@/types";
import { computed, onMounted, provide } from "vue";
import FormDesignSettingsColors from "./FormDesignSettingsColors.vue";
import { Icon } from "@iconify/vue";

provide("isFormDesignPreview", true);

const editorStore = useEditorStore();
const submissionStateStore = useFormSubmissionStateStore()

const form = computed(() => {
  const f: Form = {
    id: editorStore.form.id || 0,
    title: editorStore.form.title,
    description: editorStore.form.description,
    form_steps: editorStore.form.form_steps,
    form_fields: editorStore.form.form_fields,
    form_settings: {
      design_settings: editorStore.form.form_settings.design_settings
    }
  };
  return f;
});

onMounted(() => {
  submissionStateStore.form.value = form.value;
  const designSettings = editorStore.form.form_settings.design_settings;
  if (!designSettings) {
    editorStore.form.form_settings.design_settings = {
      colors: {
        primary: "#4CAF50", // Default primary color
        surface: "#64748B", // Default surface color
      },
      header: {
        show: true, // Default header visibility
        align: "left", // Default header alignment
      },
      footer: {
        show_progress_bar: true, // Default footer visibility
      },
    };
  }
});
</script>

<style scoped></style>
