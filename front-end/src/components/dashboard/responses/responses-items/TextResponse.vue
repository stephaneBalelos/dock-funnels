<template>
    <div class="flex items-center mb-2">
        <label :for="field_name" class="mr-2 font-semibold">{{ label }}:</label>
        <div v-if="input_type === 'email'" class="text-primary-900">
            <a :href="'mailto:' + value" class="text-blue-600 hover:underline">{{ value || 'Kein Wert angegeben' }}</a>
        </div>
        <div v-else-if="input_type === 'url'" class="text-primary-900">
            <a :href="value ?? undefined" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:underline">{{ value || 'Kein Wert angegeben' }}</a>
        </div>
        <div v-else-if="input_type === 'tel'" class="text-primary-900">
            <a :href="'tel:' + value" class="text-blue-600 hover:underline">{{ value || 'Kein Wert angegeben' }}</a>
        </div>
        <div v-else-if="input_type === 'number'" class="text-primary-900">
            {{ value || 'Kein Wert angegeben' }}
        </div>
        <div v-else-if="input_type === 'date'" class="text-primary-900">
            {{ value ? new Date(value).toLocaleDateString("de") : 'Kein Wert angegeben' }}
        </div>
        <div v-else class="text-primary-900">
            {{ value || 'Kein Wert angegeben' }}
        </div>
    </div>
</template>

<script setup lang="ts">
import type { FormFieldText } from '@/types';


type Props = {
    field_name: string;
    input_type: FormFieldText['input_type'];
    label: string;
    step_index: number;
    type: FormFieldText['type'];
    value: string | null;
}

defineProps<Props>();

</script>

<style scoped>

</style>