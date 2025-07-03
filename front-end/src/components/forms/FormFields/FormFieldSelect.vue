<template>
  <div class="flex flex-col py-4">
    <label
      v-if="field.field_settings?.hide_label !== true"
      class="text-surface-900 text-lg leading-none mb-3"
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
    <Message
      v-if="
        submissionStateStore.currentStepErrors.value.find(
          (e) => e.joined_path === props.field.field_name
        )
      "
      severity="error"
      variant="simple"
      size="small"
      class="mb-4"
    >
      {{
        submissionStateStore.currentStepErrors.value.find(
          (e) => e.joined_path === props.field.field_name
        )?.message
      }}
    </Message>
    <div class="flex flex-col gap-4">
      <div
        v-for="option in props.field.options.filter(submissionStateStore.shoulShowSelectOption)"
        :key="props.field.field_name + option.value"
        class="flex items-center gap-2"
      >
        <RadioButton
          v-model="selectedValue"
          :inputId="props.field.field_name + option.value"
          :name="option.label"
          :value="option.value"
        />
        <div class="flex flex-col">
          <label class="text-surface-900" :for="props.field.field_name + option.value">{{
            option.label
          }}</label>
          <p class="text-sm text-surface-600">{{ option.description }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { FormFieldSelect } from "@/types";
import { onMounted, ref, watch } from "vue";
import { useFormSubmissionStateStore } from "@/forms/stores/submission.store";
import { RadioButton } from "primevue";

type Props = {
  field: FormFieldSelect;
};

const props = defineProps<Props>();

const selectedValue = ref<string | null>(null);

const submissionStateStore = useFormSubmissionStateStore();

onMounted(() => {
  console.log("FormFieldSelect onMounted", props.field.field_name);
  const field =
    submissionStateStore.formSubmissionFields.value.get(props.field.field_name);
  if (field) {
    submissionStateStore.setFieldValue(props.field.field_name, field.value);
    selectedValue.value = field.value as string | null;
  }
});

watch(selectedValue, (newValue) => {
  submissionStateStore.setFieldValue(props.field.field_name, newValue);
});
</script>

<style scoped></style>
