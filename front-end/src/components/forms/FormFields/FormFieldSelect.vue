<template>
  <div class="flex flex-col py-4">
    <label
      v-if="field.field_settings?.hide_label !== true"
      class="text-stone-700 text-lg leading-none mb-3"
      :class="{ 'text-red-500': props.field.required }"
    >
      {{ props.field.label }}
      <span v-if="props.field.required" class="text-red-500">*</span>
      <p
        v-if="props.field.description"
        class="text-stone-500 text-sm leading-none mt-1"
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
        v-for="option in props.field.options.filter(shoulShowOption)"
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
          <label :for="props.field.field_name + option.value">{{
            option.label
          }}</label>
          <p class="text-sm text-gray-500">{{ option.description }}</p>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import type { FormFieldSelect, FormFieldSelectOption } from "@/types";
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

function shoulShowOption(option: FormFieldSelectOption): boolean {
  if (!option.depends_on) {
    return true;
  }
  // Check dependencies
  const dependencies: boolean[] = option.depends_on.map((dep) => {
    const field_name = dep.field_name;
    const dependsOnField =
      submissionStateStore.formSubmissionFields.value.get(field_name);
    if (!dependsOnField) {
      return false;
    }
    const dependsOnValue = dependsOnField.value;
    if (Array.isArray(dependsOnValue)) {
      return dependsOnValue.includes(dep.value as string);
    }
    return dependsOnValue === dep.value;
  });

  return dependencies.every((dep) => dep);
}

watch(selectedValue, (newValue) => {
  submissionStateStore.setFieldValue(props.field.field_name, newValue);
});
</script>

<style scoped></style>
