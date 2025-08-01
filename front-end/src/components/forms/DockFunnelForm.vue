<template>
  <div
    v-if="submissionStateStore.form.value"
    class="dockfunnelform-container relative w-full mx-auto flex flex-col relative border border-surface-100 rounded-lg shadow-lg bg-white min-h-[400px]"
  >
    <div
      v-if="headerSettings.show"
      class="dockfunnelform-header border-b border-surface-100 p-4"
    >
      <h3
        :class="`text-2xl font-semibold text-surface-900 text-${headerSettings.align}`"
      >
        {{ props.form.title }}
      </h3>
      <p v-if="props.form.description" :class="`text-surface-600 mt-2 text-xs text-${headerSettings.align}`">
        {{ props.form.description }}
      </p>
    </div>
    <DockFunnelFormContent v-if="!submissionStateStore.isFormSubmitted.value" />
    <div v-if="isSubmitting" class="submitting absolute inset-0 flex items-center justify-center bg-neutral-300/50">
      <div class="text-primary-700 text-lg flex flex-col items-center justify-center p-8 bg-white rounded-lg shadow-lg">
        <svg class="animate-spin" xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24"><!-- Icon from HeroIcons by Refactoring UI Inc - https://github.com/tailwindlabs/heroicons/blob/master/LICENSE --><path fill="none" stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
        <p class="mt-4">Formular wird gesendet...</p>
      </div>
    </div>
    <div
      v-if="submissionStateStore.isFormSubmitted.value"
      class="outro absolute inset-0 p-4 min-h-96 flex flex-col items-center justify-center bg-white"
    >
      <h3 class="text-3xl font-semibold text-primary-800 mb-4 text-center">
        {{ submissionStateStore.form.value?.outro_settings?.title || "Vielen Dank!" }}
      </h3>
      <p class="text-primary-700 mb-6 text-center">
        {{ submissionStateStore.form.value?.outro_settings?.description || "Ihre Daten wurden erfolgreich gespeichert. Wir werden uns in Kürze bei Ihnen melden." }}
      </p>
      <DockButton
        class="inline-flex items-center justify-center rounded-md px-[15px] text-sm leading-none font-medium h-[35px] bg-blue-500 text-white hover:bg-blue-600 focus:shadow-[0_0_0_2px] focus:shadow-blue-700 outline-none"
        @click="() => refreshPage(submissionStateStore.form.value?.outro_settings?.button_url)"
      >
        {{ submissionStateStore.form.value?.outro_settings?.button_text || "Zurück zur Startseite" }}
      </DockButton>
    </div>
    <div class="dockfunnelform-footer border-t border-surface-100 p-4">
      <div
        v-if="!submissionStateStore.isFormSubmitted.value"
        class="flex justify-between items-center"
      >
        <DockButton
        color="surface"
          @click="submissionStateStore.previousStep"
          :disabled="submissionStateStore.currentStepIndex.value === 0"
        >
          Zurück
        </DockButton>
        <div class="flex-1 px-8" v-if="footerSettings.show_progress_bar">
          <DockProgressBar :progress="submissionStateStore.progressPercentage.value" />
        </div>
        <DockButton
          class="ml-auto"
          @click="submissionStateStore.nextStep"
          v-if="
            submissionStateStore.currentStepIndex.value <
            submissionStateStore.form.value?.form_steps.length - 1
          "
        >
          Weiter
        </DockButton>
        <DockButton
          @click="() => submitForm()"
          v-if="
            submissionStateStore.currentStepIndex.value ===
            submissionStateStore.form.value?.form_steps.length - 1
          "
          :disabled="isSubmitting"
        >
          Abschließen
        </DockButton>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useFormSubmissionStateStore } from "@/forms/stores/submission.store.ts";
import type { Form } from "../../types/index.ts";
import { computed, ref } from "vue";
import DockFunnelFormContent from "./DockFunnelFormContent.vue";
import DockProgressBar from "./UI/DockProgressBar.vue";
import DockButton from "./UI/DockButton.vue";

type Props = {
  form: Form;
};

const props = defineProps<Props>();
const submissionStateStore = useFormSubmissionStateStore();
const isSubmitting = ref(false);

const submitForm = async () => {
  isSubmitting.value = true;
  try {
    // Validate the form before submission
    const res = await submissionStateStore.saveFormSubmission();
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

const headerSettings = computed(() => {
  return (
    submissionStateStore.form.value?.form_settings.design_settings.header || {
      show: true,
      align: "left",
    }
  );
});

const footerSettings = computed(() => {
  return (
    submissionStateStore.form.value?.form_settings.design_settings.footer || {
      show_progress_bar: true,
    }
  );
});

const refreshPage = (url?: string) => {
  // Refresh the page to reset the form state
  if (url) {
    console.error("Redirecting to:", url);
    window.location.href = url;
  } else {
    window.location.reload();
  }
};
</script>

<style scoped>

</style>
