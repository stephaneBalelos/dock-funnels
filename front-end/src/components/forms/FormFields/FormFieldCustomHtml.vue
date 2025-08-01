<template>
    <div v-html="props.field.html_content" class="custom-html-field"></div>
</template>

<script setup lang="ts">
import type { FormFieldCustomHtml } from '@/types';


type Props = {
    field: FormFieldCustomHtml;
}

const props = defineProps<Props>();
</script>

<style>
.custom-html-field {
    position: relative;
}

.custom-html-field a {
    @apply text-primary-500;
}
</style>