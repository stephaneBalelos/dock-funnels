<template>
  <div class="flex flex-col gap-4">
    <CheckboxGroupRoot v-model="checkbox" :name="'checkbox-group'" class="flex flex-col gap-4">
        <label v-for="option in props.field.options" class="flex flex-row gap-4 items-center [&>.checkbox]:hover:bg-neutral-100">
          <CheckboxRoot
            :name="option.label"
            :value="option.value"
            class="hover:bg-stone-50 flex h-5 w-5 appearance-none items-center justify-center rounded-md bg-white shadow-sm border outline-none focus-within:shadow-[0_0_0_2px_black]"
          >
            <CheckboxIndicator class="bg-white h-full w-full rounded flex items-center justify-center">
              &#x2713;
            </CheckboxIndicator>
          </CheckboxRoot>
          <span class="select-none text-stone-700 text-sm">{{ option.label }}</span>
        </label>
    </CheckboxGroupRoot>
  </div>
</template>

<script setup lang="ts">
import type { FormFieldCheckboxList } from "@/types";
import { CheckboxGroupRoot, CheckboxIndicator, CheckboxRoot } from 'reka-ui'
import { useFormSubmissionStateStore } from "@/forms/stores/form.ts";
import { ref, watch, } from "vue";

type Props = {
  field: FormFieldCheckboxList;
};

const props = defineProps<Props>();

const checkbox = ref();

const submissionStateStore = useFormSubmissionStateStore();



watch(checkbox, (newValue) => {
  // Ensure the value is an array
  const value = Array.isArray(newValue) ? newValue : [];
  submissionStateStore.setFieldValue(props.field.field_name, value);
}, {
  immediate: true,
  deep: true,
});




</script>

<style scoped></style>