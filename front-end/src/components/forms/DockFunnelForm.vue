<template>
  <div
  v-if="submissionStateStore.form.value"
    class="dockfunnelform-container h-full w-full mx-auto flex flex-col relative border border-gray-200 rounded-lg shadow-lg"
  >
    <div class="dockfunnelform-header border-b border-gray-200 p-4">
      <h3 class="text-2xl font-semibold text-surface-900">
        {{ props.form.title }}
      </h3>
    </div>
    <div
      v-if="currentStep"
      class="dockfunnelform-content flex-1 p-4 overflow-y-auto"
    >
      <div class="flex flex-col">
        <span class="text-sm text-surface-700 font-semibold mb-2">
          Schritt {{ submissionStateStore.currentStepIndex.value + 1 }}
        </span>
        <h3 class="text-lg text-surface-800 font-semibold">
          {{ currentStep.title }}
        </h3>
        <p class="text-surface-600 mt-1 mb-4">
          {{ currentStep.description }}
        </p>
      </div>
      <div
        v-if="submissionStateStore.fieldsForCurrentStep.value.length > 0"
        class="flex flex-col gap-8 mt-8"
      >
        <FormFieldsRoot
          v-for="field in submissionStateStore.fieldsForCurrentStep.value"
          :field="field"
          :key="field.field_name"
        />
      </div>
      <div
        v-else
        class="mt-8 flex flex-col items-center py-8 bg-gray-50 rounded-lg"
      >
        <h3 class="text-surface-600 text-lg font-semibold">
          Dieser Schritt können Sie überspringen.
        </h3>
        <Button
          class="mt-4"
          @click="submissionStateStore.nextStep"
          label="Weiter"
          :severity="'primary'"
        />
      </div>
    </div>
    <div
      v-if="submissionStateStore.isFormSubmitted.value"
      class="dockfunnelform-intro absolute inset-0 p-4 min-h-96 flex flex-col items-center justify-center bg-white"
    >
      <h3 class="text-3xl font-semibold text-surface-800 mb-4 text-center">
        Danke für Ihre Einreichung!
      </h3>
      <p class="text-surface-600 mb-6 text-center">
        Ihre Daten wurden erfolgreich gespeichert. Wir werden uns in Kürze bei Ihnen melden.
      </p>
      <button
        class="inline-flex items-center justify-center rounded-md px-[15px] text-sm leading-none font-medium h-[35px] bg-blue-500 text-white hover:bg-blue-600 focus:shadow-[0_0_0_2px] focus:shadow-blue-700 outline-none"
        @click="() => refreshPage()"
      >
        Schließen
      </button>
    </div>
    <div class="dockfunnelform-footer border-t border-gray-200 p-4">
      <div class="flex justify-between">
        <Button
          @click="submissionStateStore.previousStep"
          :disabled="submissionStateStore.currentStepIndex.value === 0"
          v-if="submissionStateStore.currentStepIndex.value > 0"
        >
          Zurück
        </Button>
        <Button
          class="ml-auto"
          @click="submissionStateStore.nextStep"
          v-if="submissionStateStore.currentStepIndex.value < submissionStateStore.form.value?.form_steps.length - 1"
        >
          Weiter
        </Button>
        <Button
          @click="() => submitForm()"
          v-if="submissionStateStore.currentStepIndex.value === submissionStateStore.form.value?.form_steps.length - 1"
          :disabled="isSubmitting"
        >
          Abschließen
        </Button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useFormSubmissionStateStore } from "@/forms/stores/submission.store.ts";
import type { Form } from "../../types/index.ts";
import FormFieldsRoot from "./FormFieldsRoot.vue";
import { computed, ref } from "vue";

type Props = {
  form: Form;
};

const props = defineProps<Props>();
const submissionStateStore = useFormSubmissionStateStore();
const isSubmitting = ref(false);

const currentStep = computed(() => {
  if (!submissionStateStore.form.value) return null;
  // Return the current step object
  return (
    submissionStateStore.form.value.form_steps[
      submissionStateStore.currentStepIndex.value
    ] || null
  );
});

const submitForm = async () => {
  isSubmitting.value = true;
  try {
    // Validate the form before submission
    const res = await submissionStateStore.saveFormSubmission()
    if (res) {
      if (res.redirect_url) {
        // If a redirect URL is provided, navigate to it
        window.location.href = res.redirect_url;
      }
    } else {
      console.error("Form submission failed");
    }
  } catch (error) {
    // Handle validation errors
    console.error("Form validation failed:", error);
    return;
  } finally {
    isSubmitting.value = false;
  }
};

const refreshPage = () => {
  // Refresh the page to reset the form state
  window.location.reload();
};
</script>

<style scoped></style>
