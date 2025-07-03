<template>
  <div class="flex w-full">
    <div class="w-full max-w-4xl mx-auto p-4 flex-1">
      <DockFunnelForm
        v-if="form"
        :form="form"
        class="dockfunnel-form-preview"
      />
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
          <AccordionHeader>Header II</AccordionHeader>
          <AccordionContent>
            <p class="m-0">
              Sed ut perspiciatis unde omnis iste natus error sit voluptatem
              accusantium doloremque laudantium, totam rem aperiam, eaque ipsa
              quae ab illo inventore veritatis et quasi architecto beatae vitae
              dicta sunt explicabo. Nemo enim ipsam voluptatem quia voluptas sit
              aspernatur aut odit aut fugit, sed quia consequuntur magni dolores
              eos qui ratione voluptatem sequi nesciunt. Consectetur, adipisci
              velit, sed quia non numquam eius modi.
            </p>
          </AccordionContent>
        </AccordionPanel>
        <AccordionPanel value="2">
          <AccordionHeader>Header III</AccordionHeader>
          <AccordionContent>
            <p class="m-0">
              At vero eos et accusamus et iusto odio dignissimos ducimus qui
              blanditiis praesentium voluptatum deleniti atque corrupti quos
              dolores et quas molestias excepturi sint occaecati cupiditate non
              provident, similique sunt in culpa qui officia deserunt mollitia
              animi, id est laborum et dolorum fuga. Et harum quidem rerum
              facilis est et expedita distinctio. Nam libero tempore, cum soluta
              nobis est eligendi optio cumque nihil impedit quo minus.
            </p>
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
        surface: "#6b7280", // Default surface color
      },
      header: {
        show: true, // Default header visibility
        align: "left", // Default header alignment
      },
    };
  }
});
</script>

<style scoped></style>
