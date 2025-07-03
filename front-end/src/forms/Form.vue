<template>
  <DockFunnelForm
    v-if="submissionStateStore.form.value"
    :form="submissionStateStore.form.value"
  />
  <div class="debug">
    formSubmissionFields:
    <ul>
      <li v-for="(v, key) in submissionStateStore.formSubmissionFields.value.entries()" :key="key">
        {{ v[0] }}: {{ v[1] }}
      </li>
    </ul>
  </div>
</template>

<script setup lang="ts">
import { inject, onMounted } from "vue";
import DockFunnelForm from "@/components/forms/DockFunnelForm.vue";
import { useFormSubmissionStateStore } from "@/forms/stores/submission.store";
import { getFormById } from "@/api/wpAjaxApi.ts";
import type { Form } from "@/types";
import { FormTestData } from "@/utils";


const submissionStateStore = useFormSubmissionStateStore();
const endpoint = inject("ajaxUrl") as string | undefined;
const nonce = inject("nonce") as string | undefined;
const formId = inject("formId") as number | undefined;

onMounted(async () => {
  if (!endpoint || !nonce || !formId) {
    console.error("API endpoint, nonce, or formId is not provided.");
    submissionStateStore.form.value = {
      id: FormTestData.id || 0,
      title: FormTestData.title,
      description: FormTestData.description,
      form_steps: FormTestData.form_steps,
      form_fields: FormTestData.form_fields,
      form_settings: FormTestData.form_settings,
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
      submissionStateStore.form.value = data as Form;
    })
    .catch((error) => {
      console.error("Error loading form:", error);
    });
});
</script>

<style scoped></style>
