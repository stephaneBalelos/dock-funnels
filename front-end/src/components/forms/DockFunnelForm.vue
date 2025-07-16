<template>
  <div
    v-if="submissionStateStore.form.value"
    class="dockfunnelform-container relative w-full mx-auto flex flex-col relative border border-surface-100 rounded-lg shadow-lg"
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
    </div>
    <DockFunnelFormContent />
    <div
      v-if="submissionStateStore.isFormSubmitted.value"
      class="outro absolute inset-0 p-4 min-h-96 flex flex-col items-center justify-center bg-white"
    >
      <h3 class="text-3xl font-semibold text-surface-800 mb-4 text-center">
        Danke für Ihre Einreichung!
      </h3>
      <p class="text-surface-600 mb-6 text-center">
        Ihre Daten wurden erfolgreich gespeichert. Wir werden uns in Kürze bei
        Ihnen melden.
      </p>
      <DockButton
        class="inline-flex items-center justify-center rounded-md px-[15px] text-sm leading-none font-medium h-[35px] bg-blue-500 text-white hover:bg-blue-600 focus:shadow-[0_0_0_2px] focus:shadow-blue-700 outline-none"
        @click="() => refreshPage()"
      >
        Schließen
      </DockButton>
    </div>
    <div class="dockfunnelform-footer border-t border-surface-100 p-4">
      <div
        v-if="!submissionStateStore.isFormSubmitted.value"
        class="flex justify-between items-center"
      >
        <DockButton
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

const refreshPage = () => {
  // Refresh the page to reset the form state
  window.location.reload();
};
</script>

<style scoped>

</style>
