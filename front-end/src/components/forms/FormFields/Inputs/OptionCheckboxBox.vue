<template>
  <div :class="classes" @click="handleToggle">
    <div class="w-full flex justify-between items-center">
      <label class="text-primary-900 font-semibold cursor-pointer">{{
        props.label
      }}</label>
      <div
        v-if="model.includes(props.value)"
        class="w-5 h-5 rounded border-2 border-primary-500 flex items-center justify-center"
      >
        <Icon
          icon="heroicons:check-20-solid"
          class="text-primary-500"
        />
      </div>
      <div
        v-else
        class="w-5 h-5 rounded border-2 border-surface-200 flex items-center justify-center"
      ></div>
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
  const base = "option-box";
  const active = model.value.includes(props.value) ? "option-box-active" : "";
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

<style scoped>
.option-box {
  @apply relative flex flex-col items-start gap-1 cursor-pointer p-4 rounded border-2 transition-colors duration-200 ease-in-out hover:border-primary-300 border-surface-50 border-2;
}
.option-box-active {
  @apply bg-primary-50 border-primary-300 border-2 shadow-lg;
}
</style>
