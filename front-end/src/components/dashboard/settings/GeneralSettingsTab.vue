<template>
<Form v-slot="$form" :resolver="resolver" :initialValues="initialValues" @submit="$e => onFormSubmit($e as FormSubmitEvent<Schema>)" class="flex justify-center flex-col gap-4">
    <div class="flex flex-col gap-1">
        <label for="form-title" class="font-semibold">Formular Titel</label>

        <InputText id="form-title" name="title" type="text" placeholder="Title des Formulars" />
        <Message v-if="$form.title?.invalid" severity="error" size="small" variant="simple">{{ $form.title.error?.message }}</Message>
    </div>
    <div class="flex flex-col gap-1">
        <label for="form-description" class="font-semibold">Formular Beschreibung</label>
        <InputText id="form-description" name="description" type="text" placeholder="Beschreibung des Formulars" />
        <Message v-if="$form.description?.invalid" severity="error" size="small" variant="simple">{{ $form.description.error?.message }}</Message>
    </div>
    <Button type="submit" severity="secondary" label="Speichern" />
</Form>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { zodResolver } from '@primevue/forms/resolvers/zod';
import { useToast } from "primevue/usetoast";
import { z } from 'zod';
import type { FormSubmitEvent } from "@primevue/forms";

const initialValues = ref({
    title: '',
    description: '',
});

const schema = z.object({
    title: z.string().min(1, { message: 'Geben Sie einen Titel ein.' }),
    description: z.string().optional(),
});

type Schema = z.infer<typeof schema>;

const resolver = ref(zodResolver(schema));
const toast = useToast();

const onFormSubmit = (event: FormSubmitEvent<Schema>) => {
    console.log('Form submitted:', event.values);
    toast.add({
        severity: 'success',
        summary: 'Einstellungen gespeichert',
        life: 3000,
    });
};
</script>

<style scoped>

</style>