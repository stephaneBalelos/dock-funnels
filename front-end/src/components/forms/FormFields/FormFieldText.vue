<template>
  <div class="flex flex-col flex-wrap py-4">
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

    <input
      :id="props.field.id.toString()"
      class="bg-white border inline-flex h-[35px] px-2 appearance-none justify-center rounded-lg text-sm leading-none shadow-sm outline-none focus:shadow-[0_0_0_2px_black] selection:color-white selection:bg-blackA9"
      type="text"
        :placeholder="props.field.placeholder"
    >
  </div>
</template>

<script setup lang="ts">
import type { FormFieldText } from '@/types';

type Props = {
  field: FormFieldText;
};

const props = defineProps<Props>();
</script>

<style scoped>

</style>