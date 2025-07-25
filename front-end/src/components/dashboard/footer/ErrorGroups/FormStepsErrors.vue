<template>
    <div class="text-red-600">
        <div v-for="[stepIndex, errors] in Object.entries(props.errors)" :key="stepIndex" class="mb-4">
            <b>Schritt {{ parseInt(stepIndex) + 1 }}:</b>
            {{ errors }}
        </div>
    </div>
</template>

<script setup lang="ts">

type Props = {
    errors: Record<number, Record<string, string>>;
};

const props = defineProps<Props>();


</script>

<style scoped>

</style>