<template>
    <button :class="classes.button" :disabled="props.disabled">
        <slot />
    </button>
</template>

<script setup lang="ts">
import { computed } from 'vue';


type Props = {
    color?: "primary" | "surface";
    disabled?: boolean;
};
const props = defineProps<Props>();


const classes = computed(() => {
    const baseClasses = `dock-button dock-button-${props.color || 'primary'}`;
    const roundedClasses = "rounded-md"
    const paddingClasses = "px-4 py-2";
    const textClasses = "text-sm font-medium";
    return {
        button: `${baseClasses} ${roundedClasses} ${paddingClasses} ${textClasses}`
    };
})
</script>

<style scoped>
    .dock-button {
        @apply inline-flex items-center justify-center
    }

    .dock-button.dock-button-primary {
        @apply shadow-sm text-white bg-primary-500 hover:bg-primary-600 disabled:bg-primary-200 aria-disabled:bg-primary-50 focus-visible:ring-2 focus-visible:ring-primary-500;
    }

    .dock-button.dock-button-surface {
        @apply shadow-sm text-white bg-surface-500 hover:bg-surface-600 disabled:bg-surface-200 aria-disabled:bg-surface-50 focus-visible:ring-2 focus-visible:ring-surface-500;
    }
</style>