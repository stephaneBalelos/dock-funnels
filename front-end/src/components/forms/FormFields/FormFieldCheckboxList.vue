<template>
  <div class="flex flex-col gap-4">
    <div class="flex flex-col gap-2">
      <label class="text-surface-900 text-lg leading-none mb-3">
        {{ props.field.label }}
        <span v-if="props.field.required" class="text-red-500">*</span>
        <p
          v-if="props.field.description"
          class="text-surface-500 text-sm leading-none mt-1"
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
      <div class="flex items-center gap-2">
        <OptionCheckboxBox
          v-for="option in props.field.options.filter(
            submissionStateStore.shoulShowChechboxListOption
          )"
          :key="field.field_name + option.value"
          :label="option.label"
          :description="option.description"
          v-model="selectedValues"
          :inputId="field.field_name + option.value"
          :name="option.label"
          :value="option.value"
        />
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { FormFieldCheckboxList } from "@/types";
import { useFormSubmissionStateStore } from "@/forms/stores/submission.store";
import { onMounted, ref, watch } from "vue";
import OptionCheckboxBox from "./Inputs/OptionCheckboxBox.vue";

type Props = {
  field: FormFieldCheckboxList;
};

const props = defineProps<Props>();

const selectedValues = ref([] as string[]);

const submissionStateStore = useFormSubmissionStateStore();

onMounted(() => {
  const initialValue = submissionStateStore.formSubmissionFields.value.get(
    props.field.field_name
  )?.value as string[] | undefined;
  const allowedValues = props.field.options
    .filter(submissionStateStore.shoulShowChechboxListOption)
    .map((option) => option.value);

  if (initialValue) {
    // Filter initial value to only include allowed values
    const filteredInitialValue = initialValue.filter((value) =>
      allowedValues.includes(value)
    );
    submissionStateStore.setFieldValue(
      props.field.field_name,
      filteredInitialValue
    );
    selectedValues.value = filteredInitialValue;
  }
});

watch(
  selectedValues,
  (newValue) => {
    submissionStateStore.setFieldValue(props.field.field_name, newValue);
    console.log(newValue);
  },
  { deep: true }
);
</script>

<style scoped></style>
