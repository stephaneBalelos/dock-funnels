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
      :name="props.field.field_name"
      class="flex flex-col gap-4"
      default-value="default"
      aria-label="View density"
      @update:model-value="handleUpdate"
    >
      <div v-for="option in props.field.options.filter(shoulShowOption)" class="flex items-center" :key="props.field.field_name + option.value">
        <RadioGroupItem
          class="bg-white w-[1.125rem] h-[1.125rem] rounded-full border data-[active=true]:border-stone-700 data-[active=true]:bg-stone-700 dark:data-[active=true]:bg-white shadow-sm focus:shadow-[0_0_0_2px] focus:shadow-stone-700 outline-none cursor-default"
          :value="option.value"
          :id="props.field.field_name + option.value"
        >
          <RadioGroupIndicator
            class="flex items-center justify-center w-full h-full relative after:content-[''] after:block after:w-2 after:h-2 after:rounded-[50%] after:bg-white dark:after:bg-stone-700"
          />
        </RadioGroupItem>
        <div class="flex flex-col pl-2">
          <label
            class="text-stone-700 leading-none"
            :for="props.field.field_name + option.value"
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
import { onMounted, ref } from "vue";
import { useFormSubmissionStateStore } from "@/forms/stores/form.ts";

type Props = {
  field: FormFieldSelect;
};

const props = defineProps<Props>();

const radioStateSingle = ref();

const submissionStateStore = useFormSubmissionStateStore();

const handleUpdate = async (value: string) => {
  submissionStateStore.setFieldValue(props.field.field_name, value);
  radioStateSingle.value = value;
};

onMounted(() => {
  console.log("FormFieldSelect onMounted", props.field.field_name);
  const field = submissionStateStore.formSubmissionFields.value[props.field.field_name];
  if (field) {
    submissionStateStore.setFieldValue(props.field.field_name, field.value);
    radioStateSingle.value = field.value;
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
