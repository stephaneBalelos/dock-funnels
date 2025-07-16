<template>
  <div :class="classSettings.base">
    <label
      v-if="settings?.hide_label !== true"
      :class="classSettings.label"
    >
      {{ props.field.label }}
      <span v-if="props.field.required" class="text-red-500">*</span>
      <p
        v-if="props.field.description"
        class="text-surface-600 text-sm leading-none mt-1"
      >
        {{ props.field.description }}
      </p>
    </label>
    <div
      v-if="
        submissionStateStore.currentStepErrors.value.find(
          (e) => e.joined_path === props.field.field_name
        )
      "
class="text-red-500 text-sm mt-2"
    >
      {{
        submissionStateStore.currentStepErrors.value.find(
          (e) => e.joined_path === props.field.field_name
        )?.message
      }}
    </div>
    <div :class="classSettings.optionsContainer">
      <OptionBox
        v-for="option in props.field.options.filter(
          submissionStateStore.shoulShowSelectOption
        )"
        :key="props.field.field_name + option.value"
        :label="option.label"
        :description="option.description"
        v-model="selectedValue"
        :inputId="props.field.field_name + option.value"
        :name="option.label"
        :value="option.value"
      />
    </div>
  </div>
</template>

<script setup lang="ts">
import type { FormFieldSelect } from "@/types";
import { computed, onMounted, ref, watch } from "vue";
import { useFormSubmissionStateStore } from "@/forms/stores/submission.store";
import OptionBox from "./Inputs/OptionSelectBox.vue";

type Props = {
  field: FormFieldSelect;
};

const props = defineProps<Props>();
const settings = props.field.field_settings || {};

const selectedValue = ref<string | null>(null);

const submissionStateStore = useFormSubmissionStateStore();

onMounted(() => {
  console.log("FormFieldSelect onMounted", props.field.field_name);
  const field = submissionStateStore.formSubmissionFields.value.get(
    props.field.field_name
  );
  if (field) {
    submissionStateStore.setFieldValue(props.field.field_name, field.value);
    selectedValue.value = field.value as string | null;
  }
});

watch(selectedValue, (newValue) => {
  submissionStateStore.setFieldValue(props.field.field_name, newValue);
});


const classSettings = computed(() => {
  let base = `flex flex-col py-4`;
  let label = `text-surface-900 text-lg font-semibold leading-none mb-3`;
  let optionsContainer = `w-full grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4`;
  if(settings.align) {
    base += ` text-${settings.align}`;
    base += ` items-${settings.align}`;
    optionsContainer += ` items-${settings.align}`;
  }
  if (settings.text_align) {
    label += ` text-${settings.text_align}`;
  }

  return {
    base,
    label,
    optionsContainer
  }
})
</script>

<style scoped></style>
