<template>
  <div
    class="dock-progress-bar relative w-full h-1 bg-surface-200 rounded-full overflow-hidden"
  >
    <div
      class="dock-progress-bar-fill absolute top-0 left-0 h-full bg-primary-500 transition-all duration-300 ease-in-out"
      :style="{ width: `${props.progress}%` }"
    ></div>
  </div>
</template>

<script setup lang="ts">
type Props = {
  progress: number; // Progress value between 0 and 100
};
const props = defineProps<Props>();
</script>

<style scoped></style>
