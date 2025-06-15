<template>
  <div class="flex flex-col gap-4">
    <CheckboxGroupRoot
      v-model="checkbox"
      :name="'checkbox-group'"
      class="flex flex-col gap-4"
      @update:model-value="(value) => handleUpdate(value as string[])"
    >
      <label
        v-for="option in props.field.options.filter(shoulShowOption)"
        class="flex flex-row gap-4 items-center [&>.checkbox]:hover:bg-neutral-100"
      >
        <CheckboxRoot
          :name="option.label"
          :value="option.value"
          class="hover:bg-stone-50 flex h-5 w-5 appearance-none items-center justify-center rounded-md bg-white shadow-sm border outline-none focus-within:shadow-[0_0_0_2px_black]"
        >
          <CheckboxIndicator
            class="bg-white h-full w-full rounded flex items-center justify-center"
          >
            &#x2713;
          </CheckboxIndicator>
        </CheckboxRoot>
        <span class="select-none text-stone-700 text-sm">{{
          option.label
        }}</span>
      </label>
    </CheckboxGroupRoot>
  </div>
</template>

<script setup lang="ts">
import type {
  FormFieldCheckboxList,
  FormFieldCheckboxListOption,
} from "@/types";
import { CheckboxGroupRoot, CheckboxIndicator, CheckboxRoot } from "reka-ui";
import { useFormSubmissionStateStore } from "@/forms/stores/form.ts";
import { onMounted, ref } from "vue";

type Props = {
  field: FormFieldCheckboxList;
};

const props = defineProps<Props>();

const checkbox = ref();

const submissionStateStore = useFormSubmissionStateStore();

const handleUpdate = (value: string[]) => {
  console.log("Checkbox values updated:", value);
  checkbox.value = value;
  submissionStateStore.setFieldValue(props.field.field_name, value);
};

onMounted(() => {
  console.log("FormFieldCheckboxList onMounted", props.field.field_name);
  const initialValue = submissionStateStore.formSubmissionFields.value[
    props.field.field_name
  ]?.value as string[] | undefined;
  if (initialValue) {
    submissionStateStore.setFieldValue(props.field.field_name, initialValue);
    checkbox.value = initialValue;
  }
});

function shoulShowOption(option: FormFieldCheckboxListOption) {
  if (!option.depends_on) {
    return true;
  }
  const field_name = option.depends_on.field_name;
  const dependsOnField =
    submissionStateStore.formSubmissionFields.value[field_name];
  if (!dependsOnField) {
    return false;
  }
  const dependsOnValue = dependsOnField.value;
  if (Array.isArray(dependsOnValue)) {
    return dependsOnValue.includes(option.depends_on.value as string);
  }
  return dependsOnValue === option.depends_on.value;
}
</script>

<style scoped></style>
