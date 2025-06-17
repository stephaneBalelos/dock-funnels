<template>
  <div class="ring-1 ring-gray-400 rounded-lg p-4 mb-4 mx-4 mt-2">
    <div v-if="isEditing" class="flex flex-col gap-2">
      <fieldset class="flex flex-col">
        <label class="text-sm mb-2" for="step-title"> Schritt Titel </label>
        <input
          id="step-title"
          class="w-full text-sm shadow-sm bg-white ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-primary-500 px-2.5 py-1.5"
          v-model="state.title"
        />
      </fieldset>
      <fieldset class="flex flex-col">
        <label class="text-sm mb-2" for="step-description">
          Schritt Beschreibung
        </label>
        <textarea
          id="step-description"
          class="w-full text-sm shadow-sm bg-white ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-primary-500 px-2.5 py-1.5"
          v-model="state.description"
          >{{ state.description }}</textarea
        >
      </fieldset>
      <div class="flex gap-2 justify-end pt-2">
        <button @click="updateStep">
            speichern
        </button>
        <button @click="cancelEdit" class="ml-2">
          abbrechen
        </button>
      </div>
    </div>
    <div v-else class="flex flex-col gap-2">
      <div class="flex justify-between w-full">
        <div class="flex flex-col">
          <span class="text-stone-900 font-semibold text-sm">
            {{ state.title }}
          </span>
          <span class="text-stone-500 text-xs"> #{{ props.stepIndex }} </span>
        </div>
        <button
          @click="toggleEdit"
          class="p-1.5 hover:text-green-600 transition-colors duration-200 shadow-sm ring-1 ring-inset ring-gray-300 text-gray-900 bg-white hover:bg-gray-50 disabled:bg-white aria-disabled:bg-white focus-visible:ring-2 focus-visible:ring-primary-500"
        >
          <Icon icon="heroicons:pencil-square" class="w-4 h-4" />
        </button>
      </div>
          <div class="text-xs text-stone-600">
      {{ state.description }}
    </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { ref } from "vue";
import type { FormStep } from "@/types";
import { useEditorStore } from "@/dashboard/editor.store";
import { defineProps } from "vue";
import { Icon } from "@iconify/vue";

type Props = {
  stepIndex: number;
  step: FormStep;
};
const props = defineProps<Props>();
const isEditing = ref(false);
const editorStore = useEditorStore();

const toggleEdit = () => {
  isEditing.value = !isEditing.value;
  if (isEditing.value) {
    editorStore.setSelectedStepIndex(props.stepIndex);
  }
};

const state = ref({
  title: props.step.title,
  description: props.step.description,
});

const updateStep = () => {
  editorStore.updateStep(props.stepIndex, {
    title: state.value.title,
    description: state.value.description,
  });
  toggleEdit();
};
const cancelEdit = () => {
  state.value.title = props.step.title;
  state.value.description = props.step.description;
  toggleEdit();
};
</script>

<style scoped></style>
