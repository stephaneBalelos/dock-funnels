<template>
  <DockFunnelForm v-if="formTest" :form="formTest" />

  <div class="debug">
    formSubmissionFields: {{ submissionStateStore.formSubmissionFields }}
  </div>
</template>

<script setup lang="ts">
import { onMounted, ref } from "vue";
import type { Form } from "../types/index.ts";
import DockFunnelForm from "@/components/forms/DockFunnelForm.vue";
import { useFormSubmissionStateStore } from "@/forms/stores/form.ts";

const formTest = ref<Form | null>({
  id: 1,
  title: "Online Terminvereinbarung",
  description:
    "Bitte füllen Sie das Formular aus, um einen Termin zu vereinbaren.",
  form_steps: [
    {
      id: 1,
      title: "Wählen Sie Ihre Fachrichtung",
      description: "Wählen Sie bitte eine Fachrichtung aus.",
    },
    {
      id: 2,
      title: "Wählen Sie Ihre Beschwerde",
      description: "Bitte wählen Sie eine Beschwerde in der Fachrichtung Orthopädie aus.",
    },
    {
      id: 3,
      title: "Haben sie alle Erforderlichen Dokumente bereit?",
      description: "Bitte stellen Sie sicher, dass Sie alle erforderlichen Dokumente bereit haben.",
    },
  ],
  fields: [
    {
      id: 1,
      step_index: 0,
      label: "Fahrichtung",
      description: "Fahrrichtung auswählen",
      field_name: "fachrichtung",
      default_value: "orthopedie",
      type: "select",
      options: [
        {
          value: "orthopedie",
          label: "Orthopädie",
          description: "Beschreibung für Orthopädie",
        },
        {
          value: "viszeralchirurgie",
          label: "Viszeralchirurgie",
          description: "Beschreibung für Viszeralchirurgie",
        },
        {
          value: "handchirurgie",
          label: "Handchirurgie",
          description: "Beschreibung für Handchirurgie",
        },
      ],
      required: true,
    },
    {
      id: 2,
      step_index: 1,
      label: "Beschwerde",
      field_name: "beschwerde",
      description: "Bitte wählen Sie eine Beschwerde aus.",
      type: "select",
      options: [
        {
          value: "schulter",
          label: "Schulter",
          description: "Schulterbeschwerden und -verletzungen",
        },
        {
          value: "huefte",
          label: "Hüfte",
          description: "Hüftbeschwerden und -verletzungen",
        },
        {
          value: "arthrose",
          label: "Arthrose",
          description: "Arthrose und degenerative Gelenkerkrankungen",
          depends_on: {
            field_name: "fachrichtung",
            value: "orthopedie",
          },
        },
      ],
      required: true,
    },
    {
      id: 3,
      step_index: 2,
      label: "Dokumente",
      field_name: "dokumente",
      description: "Bitte stellen Sie sicher, dass Sie alle erforderlichen Dokumente bereit haben.",
      type: "checkboxList",
      options: [
        {
          value: "röntgenbilder",
          label: "Röntgenbilder",
          description: "Röntgenbilder der betroffenen Gelenke",
        },
        {
          value: "arztberichte",
          label: "Arztberichte",
          description: "Arztberichte und medizinische Gutachten",
        },
        {
          value: "vorherige behandlungen",
          label: "Vorherige Behandlungen",
          description: "Informationen zu vorherigen Behandlungen oder Operationen",
        },
      ],
      required: true,
    },
  ],
});

const submissionStateStore = useFormSubmissionStateStore();

onMounted(() => {
  console.log(window.DockFunnelsForm);
  submissionStateStore.form.value = formTest.value;
});
</script>

<style scoped></style>
