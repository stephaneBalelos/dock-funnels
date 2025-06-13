<template>
  <DockFunnelForm v-if="formTest" :form="formTest" />
</template>

<script setup lang="ts">
import { onMounted, ref } from "vue";
import type { Form, FormSubmissionField } from "../types/index.ts";
import DockFunnelForm from "@/components/forms/DockFunnelForm.vue";
import { useFormSubmissionStateStore } from "@/forms/stores/form.ts";

const formTest = ref<Form | null>({
  id: 1,
  title: "Sample Form",
  description: "This is a sample form description.",
  form_steps: [
    {
      id: 1,
      title: "Step 1",
      description: "Wählen Sie bitte eine Fachrichtung aus.",
      order: 1,
      fields: [
        {
          id: 1,
          step_id: 1,
          name: "Fachrichtung",
          description: "Bitte wählen Sie eine Fachrichtung aus.",
          field_name: "fachrichtung",
          default_value: "fachrichtung1",
          type: "select",
          options: [
            {
              id: "fachrichtung1",
              value: "fachrichtung1",
              label: "Fachrichtung 1",
              description: "Beschreibung für Fachrichtung 1",
            },
            {
              id: "fachrichtung2",
              value: "fachrichtung2",
              label: "Fachrichtung 2",
            },
            {
              id: "fachrichtung3",
              value: "fachrichtung3",
              label: "Fachrichtung 3",
            },
          ],
          required: true,
        },
      ],
    },
    {
      id: 2,
      title: "Step 2",
      description: "Wählen Sie bitte ihre Beschwerde aus.",
      order: 2,
      fields: [
        {
          id: 1,
          step_id: 2,
          name: "Beschwerde",
          field_name: "beschwerde",
          description: "Bitte wählen Sie eine Fachrichtung aus.",
          type: "select",
          options: [
            {
              id: "fachrichtung1",
              value: "fachrichtung1",
              label: "Fachrichtung 1",
              description: "Beschreibung für Fachrichtung 1",
            },
            {
              id: "fachrichtung2",
              value: "fachrichtung2",
              label: "Fachrichtung 2",
            },
            {
              id: "fachrichtung3",
              value: "fachrichtung3",
              label: "Fachrichtung 3",
            },
          ],
          required: true,
        },
      ],
    },
  ],
});

const submissionStateStore = useFormSubmissionStateStore();

const initFormSubmission = (form: Form) => {
  submissionStateStore.formSubmission.value = {
    form_id: form.id,
    fields: {},
  };

  form.form_steps.forEach((step) => {
    step.fields.forEach((field) => {
      // Initialize each field in the submission with its default value
      const f: FormSubmissionField = {
        field_id: field.id,
        field_name: field.field_name,
        field_label: field.label || "",
        value: field.default_value || "",
        step_id: field.step_id,
        step_title: step.title,
        step_order: step.order,
      };
      // Add the field to the submission
      submissionStateStore.formSubmission.value!.fields[field.id] = f;
    });
  });
};

onMounted(() => {
  console.log(window.DockFunnelsFormData);
  initFormSubmission(formTest.value!);
});
</script>

<style scoped></style>
