<template>
    <div ref="customHtmlContent" v-html="props.field.html_content" class="custom-html-field"></div>
</template>

<script setup lang="ts">
import { useFormSubmissionStateStore } from '@/forms/stores/submission.store';
import type { FormFieldCustomHtml } from '@/types';
import { onMounted, ref } from 'vue';


type Props = {
    field: FormFieldCustomHtml;
}

const props = defineProps<Props>();
const submissionStateStore = useFormSubmissionStateStore();
const customHtmlContent = ref<HTMLDivElement | null>(null);

onMounted(() => {
    // Parse Mention Blots in the custom HTML content
    if (customHtmlContent.value) {
        const mentions = customHtmlContent.value.querySelectorAll('.mention');
        mentions.forEach((mention) => {
            const id = mention.getAttribute('data-id');
            const value = mention.getAttribute('data-value');
            if (id && value) {
                const field = submissionStateStore.formSubmissionFields.value.get(id);
                console.log('Field:', field);
                if (field && field.value) {
                    mention.innerHTML = `${field.value}`;
                } else {
                    mention.innerHTML = `{${value}}`;
                }
            }
        });
    }
});



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