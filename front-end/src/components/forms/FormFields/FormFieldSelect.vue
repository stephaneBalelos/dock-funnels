<template>
  <div class="flex flex-col py-4">
    <label
      class="text-stone-700 text-lg leading-none mb-3"
      :class="{ 'text-red-500': props.field.required }"
    >
      {{ props.field.name }}
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
      <div v-for="option in props.field.options" class="flex items-center" :key="option.id">
        <RadioGroupItem
          :id="option.id"
          class="bg-white w-[1.125rem] h-[1.125rem] rounded-full border data-[active=true]:border-stone-700 data-[active=true]:bg-stone-700 dark:data-[active=true]:bg-white shadow-sm focus:shadow-[0_0_0_2px] focus:shadow-stone-700 outline-none cursor-default"
          :value="option.value"
        >
          <RadioGroupIndicator
            class="flex items-center justify-center w-full h-full relative after:content-[''] after:block after:w-2 after:h-2 after:rounded-[50%] after:bg-white dark:after:bg-stone-700"
          />
        </RadioGroupItem>
        <div class="flex flex-col pl-2">
          <label
            class="text-stone-700 leading-none"
            :for="option.id"
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
import type { FormFieldSelect } from "@/types";
import { RadioGroupRoot, RadioGroupItem, RadioGroupIndicator } from "reka-ui";
import { ref, } from "vue";

type Props = {
  field: FormFieldSelect;
};

const props = defineProps<Props>();

const radioStateSingle = ref("default");
</script>

<style scoped></style>
