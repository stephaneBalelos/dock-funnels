<template>
  <div
  v-if="submissionStateStore.form.value"
    class="dockfunnelform-container h-full w-full max-w-5xl mx-auto flex flex-col relative border border-gray-200 rounded-lg shadow-lg"
  >
    <div class="dockfunnelform-header border-b border-gray-200 p-4">
      <h3>{{ props.form.title }}</h3>
    </div>
    <div
      v-if="currentStep"
      class="dockfunnelform-content flex-1 p-4 overflow-y-auto"
    >
      <div class="flex flex-col">
        <span class="text-sm text-gray-500 mb-2">
          Schritt {{ submissionStateStore.currentStepIndex.value + 1 }}
        </span>
        <h3 class="text-lg font-semibold text-gray-800">
          {{ currentStep.title }}
        </h3>
        <p class="text-gray-600">
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
        <h3 class="text-gray-600 text-lg font-semibold">
          Dieser Schritt können Sie überspringen.
        </h3>
        <Button
          class="mt-4"
          @click="submissionStateStore.nextStep"
          label="Weiter"
          variant="primary"
        />
      </div>
    </div>
    <div
      v-if="submissionStateStore.showIntroStep.value && submissionStateStore.form.value.intro_step"
      class="dockfunnelform-intro absolute inset-0 p-4 min-h-96 flex flex-col items-center justify-center bg-white"
    >
      <h3 class="text-3xl font-semibold text-gray-800 mb-4 text-center">
        {{ submissionStateStore.form.value.intro_step.title }}
      </h3>
      <p class="text-gray-600 mb-6 text-center">
        {{ submissionStateStore.form.value.intro_step.description }}
      </p>
      <button
        class="inline-flex items-center justify-center rounded-md px-[15px] text-sm leading-none font-medium h-[35px] bg-blue-500 text-white hover:bg-blue-600 focus:shadow-[0_0_0_2px] focus:shadow-blue-700 outline-none"
        @click="submissionStateStore.showIntroStep.value = false"
      >
        {{ submissionStateStore.form.value.intro_step.start_button_text || 'Loslegen' }}
      </button>
    </div>
    <div class="dockfunnelform-footer border-t border-gray-200 p-4">
      <div class="flex justify-between">
        <button
          class="inline-flex items-center justify-center rounded-md px-[15px] text-sm leading-none font-medium h-[35px] bg-gray-500 text-white hover:bg-gray-600 focus:shadow-[0_0_0_2px] focus:shadow-blue-700 outline-none"
          @click="submissionStateStore.previousStep"
          :disabled="submissionStateStore.currentStepIndex.value === 0"
          v-if="submissionStateStore.currentStepIndex.value > 0"
        >
          Zurück
        </button>
        <button
          class="inline-flex items-center justify-center rounded-md px-[15px] text-sm leading-none font-medium h-[35px] bg-blue-500 text-white hover:bg-blue-600 focus:shadow-[0_0_0_2px] focus:shadow-blue-700 outline-none"
          @click="submissionStateStore.nextStep"
          v-if="submissionStateStore.currentStepIndex.value < submissionStateStore.form.value?.form_steps.length - 1"
        >
          Weiter
        </button>
        <button
          class="inline-flex items-center justify-center rounded-md px-[15px] text-sm leading-none font-medium h-[35px] bg-blue-500 text-white hover:bg-blue-600 focus:shadow-[0_0_0_2px] focus:shadow-blue-700 outline-none"
          @click=""
          v-if="submissionStateStore.currentStepIndex.value === submissionStateStore.form.value?.form_steps.length - 1"
        >
          Abschließen
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useFormSubmissionStateStore } from "@/forms/stores/form.ts";
import type { Form } from "../../types/index.ts";
import FormFieldsRoot from "./FormFieldsRoot.vue";
import { computed } from "vue";

type Props = {
  form: Form;
};

const props = defineProps<Props>();
const submissionStateStore = useFormSubmissionStateStore();

const currentStep = computed(() => {
  if (!submissionStateStore.form.value) return null;
  // Return the current step object
  return (
    submissionStateStore.form.value.form_steps[
      submissionStateStore.currentStepIndex.value
    ] || null
  );
});
</script>

<style scoped></style>
