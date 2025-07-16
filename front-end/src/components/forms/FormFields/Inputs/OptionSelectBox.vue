<template>
  <div :class="classes" @click="model = props.value">
    <div class="w-full flex justify-between items-center">
      <label class="text-primary-900 font-semibold cursor-pointer">{{
        props.label
      }}</label>
      <Icon :width="20" icon="heroicons:check-circle-solid" v-if="model === props.value" class="text-primary-500" />
      <div v-else class="w-5 h-5"></div>
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
  value: string | null;
};

const props = defineProps<Props>();

const model = defineModel<string | null>({
  type: String,
  default: null,
});

const classes = computed(() => {
  const base =
    "option-box";
  const active =
    model.value === props.value
      ? "option-box-active"
      : "";
  return `${base} ${active}`;
});
</script>

<style scoped>

.dock-funnels-root .option-box {
  @apply relative flex flex-col items-start gap-1 cursor-pointer p-4 rounded border-2 transition-colors duration-200 ease-in-out hover:border-primary-300 border-surface-50 border-2;
}

.dock-funnels-root .option-box.option-box-active {
  @apply bg-primary-50 border-primary-300 border-2 shadow-lg;
}

</style>
