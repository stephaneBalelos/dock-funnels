<template>
  <DockFunnelForm
    v-if="submissionStateStore.form.value"
    :form="submissionStateStore.form.value"
  />
  <div class="debug">
    formSubmissionFields: {{ submissionStateStore.formSubmissionFields }}
  </div>
</template>

<script setup lang="ts">
import { inject, onMounted, ref } from "vue";
import DockFunnelForm from "@/components/forms/DockFunnelForm.vue";
import { useFormSubmissionStateStore } from "@/forms/stores/form.ts";
import { getFormById } from "@/api/wpAjaxApi.ts";
import type { Form } from "@/types";

const formTest = ref<Form | null>({
  id: 1,
  title: "Online Terminvereinbarung",
  description:
    "Bitte füllen Sie das Formular aus, um einen Termin zu vereinbaren.",
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
    }
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
      step_index: 2,
      label: "Dokumente",
      field_name: "dokumente",
      description:
        "Bitte stellen Sie sicher, dass Sie alle erforderlichen Dokumente bereit haben.",
      type: "checkboxList",
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
      step_index: 0,
      label: "Zusätzliche Informationen",
      field_name: "zusatzinfo",
      description: "Geben Sie hier zusätzliche Informationen an.",
      type: "text",
      input_type: "text",
      required: false,
      default_value: "",
    }
  ],
});

const submissionStateStore = useFormSubmissionStateStore();
const endpoint = inject("ajaxUrl") as string | undefined;
const nonce = inject("nonce") as string | undefined;
const formId = inject("formId") as number | undefined;

onMounted(async () => {
  if (formTest.value) {
    submissionStateStore.form.value = formTest.value;
    return;
  }
  if (!endpoint || !nonce || !formId) {
    console.error("API endpoint, nonce, or formId is not provided.");
    return;
  }
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
