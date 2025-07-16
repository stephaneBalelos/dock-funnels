<template>
    <div :class="classes.wrapper">
        <input 
            :type="props.type"
            :placeholder="props.placeholder"
            v-model="model"
            :class="classes.input" />
    </div>
</template>

<script setup lang="ts">
import { computed } from 'vue';


type Props = {
    type: "text" | "number" | "email" | "tel" | "date" | "url"
    placeholder?: string;
}

const props = defineProps<Props>();

const model = defineModel<string | null>({
    type: String,
    default: null
});

const classes = computed(() => {
    const baseClasses = "dock-text-input block w-full disabled:cursor-not-allowed disabled:opacity-75";
    const size = "text-sm px-2.5 py-1.5";
    const rounded = "rounded-md focus:outline-none border-0 shadow-sm";
    const bg = "bg-white text-surface-900";
    const ring = "ring-1 ring-inset ring-surface-300 focus:ring-2 focus:ring-primary-400";
    return {
        wrapper: "relative",
        input: `${baseClasses} ${size} ${rounded} ${bg} ${ring}`
    };
})

</script>

<style scoped>

</style>