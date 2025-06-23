<template>
  <DockFunnelForm
    v-if="submissionStateStore.form.value"
    :form="submissionStateStore.form.value"
  />
  <!-- <div class="debug">
    formSubmissionFields: {{ submissionStateStore.formSubmissionFields }}
  </div> -->
</template>

<script setup lang="ts">
import { inject, onMounted, ref } from "vue";
import DockFunnelForm from "@/components/forms/DockFunnelForm.vue";
import { useFormSubmissionStateStore } from "@/forms/stores/form.ts";
import { getFormById } from "@/api/wpAjaxApi.ts";
import type { Form } from "@/types";

const formTest = ref<Form | null>({
  id: 1,
  title: "Online Terminvereinbarung [Test]",
  description:
    "Bitte füllen Sie das Formular aus, um einen Termin zu vereinbaren.",
  intro_step: {
    enabled: false,
    title: "Willkommen zur Online Terminvereinbarung [Test]",
    description:
      "Bitte folgen Sie den Schritten, um einen Termin zu vereinbaren.",
    start_button_text: "Starten",
  },
  form_steps: [
    {
      title: "Wählen Sie Ihre Fachrichtung",
      description: "Wählen Sie bitte eine Fachrichtung aus.",
    },
    {
      title: "Wählen Sie Ihre Beschwerde",
      description:
        "Bitte wählen Sie eine Beschwerde in der Fachrichtung Orthopädie aus.",
    },
    {
      title: "Haben sie alle Erforderlichen Dokumente bereit?",
      description:
        "Bitte stellen Sie sicher, dass Sie alle erforderlichen Dokumente bereit haben.",
    },
    {
      title: "Zusammenfassung und Abschluss",
      description:
        "Überprüfen Sie Ihre Angaben und schließen Sie die Terminvereinbarung ab.",
    },
  ],
  fields: [
    {
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
      step_index: 1,
      label: "Dependent on Fahrrichtung Orthopädie",
      field_name: "orthopedie_beschwerde",
      description: "Bitte wählen Sie eine spezifische Beschwerde aus.",
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
      ],
      required: true,
      depends_on: [
        {
          field_name: "fachrichtung",
          value: "orthopedie",
        },
      ],
    },
    {
      step_index: 1,
      label: "Dependent on Fahrrichtung Viszeralchirurgie",
      field_name: "viszeralchirurgie_beschwerde",
      description: "Bitte wählen Sie eine spezifische Beschwerde aus.",
      type: "select",
      options: [
        {
          value: "tumor",
          label: "Tumor",
          description: "Tumorbehandlung und -diagnose",
        },
        {
          value: "schilddruesenprobleme",
          label: "Schilddrüsenprobleme",
          description: "Schilddrüsenprobleme und -behandlungen",
        },
      ],
      required: true,
      depends_on: [
        {
          field_name: "fachrichtung",
          value: "viszeralchirurgie",
        },
      ],
    },
    {
      step_index: 2,
      label: "Dokumente",
      field_name: "dokumente",
      description:
        "Bitte stellen Sie sicher, dass Sie alle erforderlichen Dokumente bereit haben.",
      type: "checkboxList",
      min: 2,
      max: 2,
      options: [
        {
          value: "roentgenbilder",
          label: "Röntgenbilder",
          description: "Röntgenbilder der betroffenen Gelenke",
          depends_on: [
            {
              field_name: "fachrichtung",
              value: "orthopedie",
            },
          ],
        },
        {
          value: "arztberichte",
          label: "Arztberichte",
          description: "Arztberichte und medizinische Gutachten",
        },
        {
          value: "vorherige behandlungen",
          label: "Vorherige Behandlungen",
          description:
            "Informationen zu vorherigen Behandlungen oder Operationen",
        },
      ],
      required: true,
    },
    {
      step_index: 2,
      label: "Zusätzliche Informationen",
      field_name: "zusatzinfo",
      description: "Geben Sie hier zusätzliche Informationen an.",
      type: "text",
      input_type: "date",
      placeholder: "TT.MM.JJJJ",
      required: true,
      default_value: "",
    },
    {
      step_index: 2,
      label: "Zustimmung zur Datenverarbeitung",
      field_name: "datenschutz",
      description: "Bitte stimmen Sie der Datenverarbeitung zu.",
      type: "checkboxList",
      options: [
        {
          value: "datenschutz",
          label: "Ich stimme der Verarbeitung meiner Daten zu.",
          description:
            "Ich habe die Datenschutzerklärung gelesen und akzeptiere sie.",
        },
      ],
      required: true,
      min: 1,
      max: 1,
    },
    {
      step_index: 3,
      label: "Zusammenfassung",
      field_name: "zusammenfassung",
      description:
        "Überprüfen Sie Ihre Angaben und schließen Sie die Terminvereinbarung ab.",
      type: "submissionSummary",
      required: false,
      show_full_summary: true,
    },
  ],
});

const submissionStateStore = useFormSubmissionStateStore();
const endpoint = inject("ajaxUrl") as string | undefined;
const nonce = inject("nonce") as string | undefined;
const formId = inject("formId") as number | undefined;

onMounted(async () => {
  if (!endpoint || !nonce || !formId) {
    console.error("API endpoint, nonce, or formId is not provided.");
    if (formTest.value) {
      submissionStateStore.form.value = formTest.value;
      return;
    }
    return;
  }
  submissionStateStore.submissionSettings.value = {
    endpoint,
    nonce,
    formId,
  };
  await getFormById(endpoint, nonce, formId)
    .then(({ data }) => {
      console.log("Form loaded:", data.form_data);
      submissionStateStore.form.value = JSON.parse(data.form_data) as Form;
    })
    .catch((error) => {
      console.error("Error loading form:", error);
    });
});
</script>

<style scoped></style>
