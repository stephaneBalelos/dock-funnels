<template>
  <div class="flex flex-col flex-wrap py-4">
    <label class="text-primary-900 text-lg font-semibold leading-none mb-3">
      {{ props.field.label }}
      <span v-if="props.field.required" class="text-red-500">*</span>
      <p
        v-if="props.field.description"
        class="text-primary-700 text-sm font-normal leading-none mt-1"
      >
        {{ props.field.description }}
      </p>
    </label>
    <DockFunnelTextInput
      v-model="textValue"
      @update:model-value="onChange"
      :placeholder="props.field.placeholder"
      :type="props.field.input_type ?? 'text'"
    />
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
  </div>
</template>

<script setup lang="ts">
import { useFormSubmissionStateStore } from "@/forms/stores/submission.store";
import type { FormFieldText } from "@/types";
import { onMounted, ref } from "vue";
import DockFunnelTextInput from "./Inputs/DockFunnelTextInput.vue";

type Props = {
  field: FormFieldText;
};

const textValue = ref<string | null>(null);

const props = defineProps<Props>();
const submissionStateStore = useFormSubmissionStateStore();

onMounted(() => {
  const initialValue = submissionStateStore.formSubmissionFields.value.get(
    props.field.field_name
  )?.value;
  if (initialValue) {
    textValue.value = initialValue as string | null;
  } else {
    textValue.value = props.field.default_value || null;
  }
});

const onChange = () => {
  submissionStateStore.setFieldValue(props.field.field_name, textValue.value);
};
</script>

<style scoped></style>
