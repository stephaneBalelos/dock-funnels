<template>
  <div
    class="dockfunnelform-container h-full w-full max-w-5xl mx-auto flex flex-col"
  >
    <div class="dockfunnelform-header border-b border-gray-200 p-4">
      <h1>{{ props.form.title }}</h1>
      <p>{{ props.form.description }}</p>
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
      <div class="mt-8">
        <FormFieldsRoot
          v-for="field in submissionStateStore.fieldsForCurrentStep.value"
          :field="field" :key="field.field_name"
        />
      </div>
    </div>
    <div class="dockfunnelform-footer border-t border-gray-200 p-4">
      <div class="flex justify-between">
        <button
          class="inline-flex items-center justify-center rounded-md px-[15px] text-sm leading-none font-medium h-[35px] bg-green4 text-green11 hover:bg-green5 focus:shadow-[0_0_0_2px] focus:shadow-green7 outline-none"
          @click="submissionStateStore.previousStep"
          :disabled="submissionStateStore.currentStepIndex.value === 0"
        >
          Zurück
        </button>
        <button
          class="inline-flex items-center justify-center rounded-md px-[15px] text-sm leading-none font-medium h-[35px] bg-green4 text-green11 hover:bg-green5 focus:shadow-[0_0_0_2px] focus:shadow-green7 outline-none"
          @click="submissionStateStore.nextStep"
        >
          Weiter
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
  return submissionStateStore.form.value.form_steps[submissionStateStore.currentStepIndex.value] || null;
});
</script>

<style scoped></style>
