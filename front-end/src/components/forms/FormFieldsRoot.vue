<template>
<div class="">
    <div v-if="props.field.type === 'text'" class="form-field-text">
        <FormFieldText :field="props.field" />
    </div>
    <div v-else-if="props.field.type === 'textarea'" class="form-field-textarea">
        Not Implemented
    </div>
    <div v-else-if="props.field.type === 'select'" class="form-field-select">
        <FormFieldSelect :field="props.field" />
    </div>
    <div v-else-if="props.field.type === 'checkboxList'" class="form-field-checkbox">
        <FormFieldCheckboxList :field="props.field" />
    </div>
    <div v-else>
        <p class="text-red-500">Field type is not implemented.</p>
    </div>
</div>
</template>

<script setup lang="ts">
import type { Form } from "@/types";
import FormFieldSelect from "./FormFields/FormFieldSelect.vue";
import FormFieldText from "./FormFields/FormFieldText.vue";
import FormFieldCheckboxList from "./FormFields/FormFieldCheckboxList.vue";

type Props = {
    field: Form["fields"][number];
}

const props = defineProps<Props>();

</script>

<style scoped>

</style>