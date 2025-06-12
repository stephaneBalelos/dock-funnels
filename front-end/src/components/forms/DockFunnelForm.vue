<template>
  <div
    class="dockfunnelform-container h-full w-full max-w-5xl mx-auto flex flex-col"
  >
    <div class="dockfunnelform-header border-b border-gray-200 p-4">
      <h1>{{ props.form.title }}</h1>
      <p>{{ props.form.description }}</p>
    </div>
    <div class="dockfunnelform-content flex-1 p-4 overflow-y-auto">
      <div class="flex flex-col">
        <span class="text-sm text-gray-500 mb-2">
          Schritt {{ currentStep }} von {{ props.form.form_steps.length }}
        </span>
        <h3 class="text-lg font-semibold text-gray-800">
          {{ selectedStep ? selectedStep.title : "Lade Schritt..." }}
        </h3>
        <p class="text-gray-600">
          {{ selectedStep ? selectedStep.description : "Lade Beschreibung..." }}
        </p>
      </div>
      <div class="mt-8">
        <div v-if="selectedStep">
          <FormFieldsRoot v-for="field in selectedStep.fields" :field="field" />
        </div>
      </div>
    </div>
    <div class="dockfunnelform-footer border-t border-gray-200 p-4">
      <div class="flex justify-between">
        <button
          class="inline-flex items-center justify-center rounded-md px-[15px] text-sm leading-none font-medium h-[35px] bg-green4 text-green11 hover:bg-green5 focus:shadow-[0_0_0_2px] focus:shadow-green7 outline-none"
          @click="prevStep"
          :disabled="currentStep === 1"
        >
          Zurück
        </button>
        <button
          class="inline-flex items-center justify-center rounded-md px-[15px] text-sm leading-none font-medium h-[35px] bg-green4 text-green11 hover:bg-green5 focus:shadow-[0_0_0_2px] focus:shadow-green7 outline-none"
          @click="nextStep"
          :disabled="currentStep === props.form.form_steps.length"
        >
          Weiter
        </button>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { Form } from "../../types/index.ts";
import { computed, onMounted, ref } from "vue";
import FormFieldsRoot from "./FormFieldsRoot.vue";

type Props = {
  form: Form;
};

const props = defineProps<Props>();
const currentStep = ref(1);

const selectedStep = computed(() => {
  return props.form.form_steps.find(step => step.order === currentStep.value);
});

onMounted(() => {
  // Initialize or fetch form data if needed
  console.log("Form component mounted with form:", props.form);
});

const handleStepChange = (step: number) => {
  currentStep.value = step;
  console.log("Current step changed to:", currentStep.value);
};

const nextStep = () => {
  if (currentStep.value < props.form.form_steps.length) {
    currentStep.value++;
    handleStepChange(currentStep.value);
  }
};
const prevStep = () => {
  if (currentStep.value > 1) {
    currentStep.value--;
    handleStepChange(currentStep.value);
  }
};



</script>

<style scoped></style>
