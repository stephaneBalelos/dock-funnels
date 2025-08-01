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

.custom-html-field .ql-size-huge {
    @apply text-4xl;
}

.custom-html-field .ql-size-large {
    @apply text-2xl;
}

.custom-html-field .ql-size-small {
    @apply text-sm;
}

.custom-html-field .ql-align {
    @apply text-left;
}

.custom-html-field .ql-align-center {
    @apply text-center;
}

.custom-html-field .ql-align-right {
    @apply text-right;
}

.custom-html-field .ql-align-justify {
    @apply text-justify;
}

.custom-html-field a {
    @apply text-primary-500;
}
</style>