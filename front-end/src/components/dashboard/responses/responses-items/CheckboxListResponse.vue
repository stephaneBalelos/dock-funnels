<template>
    <div class="flex mb-2">
        <label :for="field_name" class="mr-2 font-semibold">{{ label }}:</label>
        <div class="text-primary-900">
            <span v-for="(value, key) in value_labels" :key="key" class="block">
                {{ value }}
            </span>
        </div>

    </div>
</template>

<script setup lang="ts">
import type { FormFieldCheckboxList } from '@/types';

type Props = {
    field_name: string;
    label: string;
    step_index: number;
    type: FormFieldCheckboxList['type'];
    value: string
    value_labels: Record<string, string>;
}

defineProps<Props>();
</script>

<style scoped>

</style>