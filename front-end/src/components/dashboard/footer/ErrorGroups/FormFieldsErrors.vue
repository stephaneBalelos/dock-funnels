<template>
    <div v-for="[key, errors] in Object.entries(props.errors)" :key="key">
        <FormFieldErrorItem :errors="errors" :field-name="key" />
    </div>
</template>

<script setup lang="ts">
type Props = {
    errors: Record<string, Record<string, string>>;
}
const props = defineProps<Props>();



</script>

<style scoped>

</style>