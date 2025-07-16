<template>
    <button :class="classes.button" :disabled="props.disabled">
        <slot />
    </button>
</template>

<script setup lang="ts">
import { computed } from 'vue';


type Props = {
    disabled?: boolean;
};
const props = defineProps<Props>();


const classes = computed(() => {
    const baseClasses = "dock-button inline-flex items-center justify-center";
    const roundedClasses = "rounded-md"
    const paddingClasses = "px-4 py-2";
    const textClasses = "text-sm font-medium";
    const colorClasses = "shadow-sm text-white bg-primary-500 hover:bg-primary-600 disabled:bg-primary-200 aria-disabled:bg-primary-50 focus-visible:ring-2 focus-visible:ring-primary-500"
    return {
        button: `${baseClasses} ${roundedClasses} ${paddingClasses} ${textClasses} ${colorClasses}`
    };
})
</script>

<style scoped>

</style>