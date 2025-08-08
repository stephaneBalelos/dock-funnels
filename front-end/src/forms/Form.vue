<template>
  <DockFunnelForm
    v-if="submissionStateStore.form.value"
    :form="submissionStateStore.form.value"
  />
  <div v-else class="flex items-center justify-center py-16">
    <p v-if="!hasError" class="text-center text-gray-500">Formular wird geladen...</p>
    <p v-if="hasError" class="text-center text-red-500">
      Formular konnte nicht geladen werden.
    </p>
  </div>
</template>

<script setup lang="ts">
import { inject, onMounted, ref } from "vue";
import DockFunnelForm from "@/components/forms/DockFunnelForm.vue";
import { useFormSubmissionStateStore } from "@/forms/stores/submission.store";
import { getFormById } from "@/api/wpAjaxApi.ts";
import type { Form } from "@/types";
import { FormTestData } from "@/utils";


const submissionStateStore = useFormSubmissionStateStore();
const endpoint = inject("ajaxUrl") as string | undefined;
const nonce = inject("nonce") as string | undefined;
const formId = inject("formId") as number | undefined;
const hasError = ref(false);

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
      outro_settings: FormTestData.outro_settings,
      status: FormTestData.status || "draft",
      should_save_responses: FormTestData.should_save_responses || true,
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
      hasError.value = true;
    });
});
</script>

<style scoped></style>
