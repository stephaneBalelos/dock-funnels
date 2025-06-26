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
import { inject, onMounted, ref } from "vue";
import DockFunnelForm from "@/components/forms/DockFunnelForm.vue";
import { useFormSubmissionStateStore } from "@/forms/stores/submission.store";
import { getFormById } from "@/api/wpAjaxApi.ts";
import type { Form } from "@/types";
import { FormTestData } from "@/utils";

const formTest = ref<Form | null>(FormTestData);

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
