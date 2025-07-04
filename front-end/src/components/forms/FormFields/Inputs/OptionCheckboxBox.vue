<template>
  <div :class="classes" @click="handleToggle">
    <div class="w-full flex justify-between items-center">
      <label class="text-primary-900 font-semibold cursor-pointer">{{
        props.label
      }}</label>
      <Icon
        v-if="model.includes(props.value)"
        :width="20"
        icon="heroicons:check-circle-solid"
        class="text-primary-500"
      />
      <Icon
        v-else
        :width="20"
        icon="heroicons:circle-solid"
        class="text-surface-500"
      />
    </div>
    <p v-if="props.description" class="text-sm text-primary-700">
      {{ props.description }}
    </p>
  </div>
</template>

<script setup lang="ts">
import { computed } from "vue";
import { Icon } from "@iconify/vue";

type Props = {
  label: string;
  description?: string;
  value: string;
};

const props = defineProps<Props>();

const model = defineModel<string[]>({
  type: Array,
  default: [],
});

const classes = computed(() => {
  const base =
    "option-box relative flex flex-col items-start gap-1 cursor-pointer p-4 rounded border-2 transition-colors duration-200 ease-in-out hover:border-primary-300";
  const active = model.value.includes(props.value)
    ? "bg-primary-50 border-primary-300 border-2 shadow-lg"
    : "bg-surface-0 border-surface-50 border-2";
  return `${base} ${active}`;
});

const handleToggle = () => {
  if (model.value.includes(props.value)) {
    model.value = model.value.filter((v) => v !== props.value);
  } else {
    model.value.push(props.value);
  }
};
</script>

<style scoped></style>
