<template>
    <div class="">
        <input 
            :type="props.type"
            :placeholder="props.placeholder"
            v-model="model"
            class="dock-text-input" />
    </div>
</template>

<script setup lang="ts">

type Props = {
    type: "text" | "number" | "email" | "tel" | "date";
    placeholder?: string;
}

const props = defineProps<Props>();

const model = defineModel<string | null>({
    type: String,
    default: null
});

</script>

<style scoped>

</style>