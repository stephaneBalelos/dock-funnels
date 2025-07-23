<template>
    <div class="flex mb-2">
        <label :for="field_name" class="mr-2 font-semibold">{{ label }}:</label>
        <div class="text-primary-900">
            {{ value_label || 'Kein Wert angegeben' }}
        </div>
    </div>
</template>

<script setup lang="ts">
import type { FormFieldSelect } from '@/types';


type Props = {
    field_name: string;
    label: string;
    step_index: number;
    type: FormFieldSelect['type'];
    value: string
    value_label: string
}

defineProps<Props>();
</script>

<style scoped>

</style>