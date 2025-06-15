<template>
  <div class="flex flex-col py-4">
    <label
      class="text-stone-700 text-lg leading-none mb-3"
      :class="{ 'text-red-500': props.field.required }"
    >
      {{ props.field.label }}
      <span v-if="props.field.required" class="text-red-500">*</span>
      <p v-if="props.field.description" class="text-stone-500 text-sm leading-none mt-1">
        {{ props.field.description }}
      </p>
    </label>
    <RadioGroupRoot
      v-model="radioStateSingle"
      class="flex flex-col gap-4"
      default-value="default"
      aria-label="View density"
    >
      <div v-for="option in props.field.options.filter(shoulShowOption)" class="flex items-center" :key="option.value">
        <RadioGroupItem
          class="bg-white w-[1.125rem] h-[1.125rem] rounded-full border data-[active=true]:border-stone-700 data-[active=true]:bg-stone-700 dark:data-[active=true]:bg-white shadow-sm focus:shadow-[0_0_0_2px] focus:shadow-stone-700 outline-none cursor-default"
          :value="option.value"
          :id="option.value"
        >
          <RadioGroupIndicator
            class="flex items-center justify-center w-full h-full relative after:content-[''] after:block after:w-2 after:h-2 after:rounded-[50%] after:bg-white dark:after:bg-stone-700"
          />
        </RadioGroupItem>
        <div class="flex flex-col pl-2">
          <label
            class="text-stone-700 leading-none"
            :for="option.value"
          >
            {{ option.label }}
            <span v-if="props.field.required" class="text-red-500">*</span>
          </label>
          <p v-if="option.description" class="text-stone-500 text-sm leading-none mt-1">
            {{ option.description }}
          </p>
        </div>
      </div>
    </RadioGroupRoot>
  </div>
</template>

<script setup lang="ts">
import type { FormFieldSelect, FormFieldSelectOption } from "@/types";
import { RadioGroupRoot, RadioGroupItem, RadioGroupIndicator } from "reka-ui";
import { onUpdated, ref, watch, } from "vue";
import { useFormSubmissionStateStore } from "@/forms/stores/form.ts";

type Props = {
  field: FormFieldSelect;
};

const props = defineProps<Props>();

const radioStateSingle = ref(props.field.default_value);

const submissionStateStore = useFormSubmissionStateStore();


watch(radioStateSingle, (newValue) => {
  submissionStateStore.setFieldValue(props.field.field_name, newValue ?? null);
}, {
  immediate: true
});

onUpdated(() => {
  const currentState = submissionStateStore.formSubmissionFields.value[props.field.field_name];
  if (currentState) {
    radioStateSingle.value = currentState.value ?? props.field.default_value;
  } else {
    radioStateSingle.value = props.field.default_value;
  }
});

function shoulShowOption(option: FormFieldSelectOption): boolean {
  if (!option.depends_on) {
    return true;
  }
  const field_name = option.depends_on.field_name;
  const dependsOnField = submissionStateStore.formSubmissionFields.value[field_name];
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
