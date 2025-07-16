<template>
  <div
    class="content-container relative overflow-hidden"
    :class="`${slideDirection === 1 ? 'slide-left' : 'slide-right'}`"
  >
    <Transition
        :name="settings.step_transition"
    >
      <div
        v-if="currentStep"
        :id="`step-${submissionStateStore.currentStepIndex.value}`"
        class="dockfunnelform-content flex-1 p-4 inset-0"
        :key="`step-${submissionStateStore.currentStepIndex.value}`"
      >
        <div v-if="!settings.hide_step_header" :class="`flex flex-col mb-8 ${settings.text_align} ${settings.items_align}`">
          <span class="text-sm text-surface-500 font-semibold mb-2">
            Schritt {{ submissionStateStore.currentStepIndex.value + 1 }}
          </span>
          <h3 class="text-xl text-surface-800 font-semibold">
            {{ currentStep.title }}
          </h3>
          <p class="text-surface-600 mt-1 mb-4">
            {{ currentStep.description }}
          </p>
        </div>
        <div
          v-if="submissionStateStore.fieldsForCurrentStep.value.length > 0"
          class="flex flex-col gap-8"
        >
          <FormFieldsRoot
            v-for="field in submissionStateStore.fieldsForCurrentStep.value"
            :field="field"
            :key="field.field_name"
          />
        </div>
        <div
          v-else
          class="mt-8 flex flex-col items-center py-8 bg-surface-50 rounded-lg"
        >
          <h3 class="text-surface-600 text-lg font-semibold">
            Dieser Schritt können Sie überspringen.
          </h3>
          <DockButton
            class="mt-4"
            @click="submissionStateStore.nextStep"
          >Weiter</DockButton>
        </div>
      </div>
    </Transition>
  </div>
</template>

<script setup lang="ts">
import { useFormSubmissionStateStore } from "@/forms/stores/submission.store";
import { computed, ref, watch } from "vue";
import FormFieldsRoot from "./FormFieldsRoot.vue";
import DockButton from "./UI/DockButton.vue";

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

const slideDirection = ref(1); // 1 for forward, -1 for backward

const settings = computed(() => {
  return submissionStateStore.form.value?.form_settings.design_settings.steps || {
    hide_step_header: false,
    text_align: "text-left",
    items_align: "items-start",
    step_transition: "slide",
  };
});

watch(
  () => submissionStateStore.currentStepIndex.value,
  (newIndex, oldIndex) => {
    // Determine the slide direction based on the index change

    slideDirection.value = newIndex > oldIndex ? 1 : -1;
  }
);

</script>

<style scoped>
/* Default Transition */
.default-enter-active,
.default-leave-active {
  transition: opacity 0.5s ease;
}
.default-enter-from {
    opacity: 0;
}
.default-enter-to {
    opacity: 1;
}
.default-leave-active {
    position: absolute;
}
.default-leave-from {
    opacity: 1;
}
.default-leave-to {
    opacity: 0;
}

/* Slide Transition */
.slide-enter-active,
.slide-leave-active {
  transition: transform 0.5s ease;
}
.slide-leave-active {
  position: absolute;
}

.slide-left .slide-enter-from {
  transform: translateX(100%);
}
.slide-left .slide-enter-to {
  transform: translateX(0);
}
.slide-left .slide-leave-from {
  transform: translateX(0);
}
.slide-left .slide-leave-to {
  transform: translateX(-100%);
}

.slide-right .slide-enter-from {
  transform: translateX(-100%);
}
.slide-right .slide-enter-to {
  transform: translateX(0);
}
.slide-right .slide-leave-from {
    transform: translateX(0);
}
.slide-right .slide-leave-to {
    transform: translateX(100%);
}
</style>
