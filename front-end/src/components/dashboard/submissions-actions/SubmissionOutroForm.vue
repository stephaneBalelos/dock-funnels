<template>
  <div class="flex flex-col">
    <Form
      v-if="initialValues"
      v-slot="$form"
      :resolver="resolver"
      :initialValues="initialValues"
      :validateOnSubmit="true"
      @submit="$e => onFormSubmit($e as FormSubmitEvent<Schema>)"
      class="flex justify-center flex-col gap-4"
    >
      <div class="flex flex-col gap-1">
        <label for="outro-title" class="font-semibold"> Abschluss Titel </label>
        <InputText
          id="outro-title"
          name="title"
          type="text"
          placeholder="Danke für Ihre anfrage!"
        />
        <Message
          v-if="$form.title?.invalid"
          severity="error"
          size="small"
          variant="simple"
          >{{ $form.title.error?.message }}</Message
        >
      </div>
      <div class="flex flex-col gap-1">
        <label for="outro-description" class="font-semibold"
          >Abschluss Beschreibung</label
        >
        <InputText
          id="outro-description"
          name="description"
          type="text"
          placeholder="Wir haben Ihre Informationen erhalten und werden uns in Kürze bei Ihnen melden."
        />
        <Message
          v-if="$form.description?.invalid"
          severity="error"
          size="small"
          variant="simple"
          >{{ $form.description.error?.message }}</Message
        >
      </div>
      <div class="flex flex-col gap-1">
        <label for="outro-button-text" class="font-semibold">Button Text</label>
        <InputText
          id="outro-button-text"
          name="button_text"
          type="text"
          placeholder="Zurück zur Startseite"
        />
        <Message
          v-if="$form.button_text?.invalid"
          severity="error"
          size="small"
          variant="simple"
          >{{ $form.button_text.error?.message }}</Message
        >
      </div>
      <div class="flex flex-col gap-1">
        <label for="outro-button-url" class="font-semibold">Button URL</label>
        <InputText
          id="outro-button-url"
          name="button_url"
          type="text"
          placeholder="https://example.com"
        />
        <Message
          v-if="$form.button_url?.invalid"
          severity="error"
          size="small"
          variant="simple"
          >{{ $form.button_url.error?.message }}</Message
        >
      </div>
      <Button
        :loading="editorStore.editorState.value.isSaving"
        :disabled="editorStore.editorState.value.isSaving"
        type="submit"
        severity="secondary"
        label="Speichern"
      />
    </Form>
  </div>
</template>

<script setup lang="ts">
import { useEditorStore } from "@/dashboard/editor.store";
import type { FormSubmitEvent } from "@primevue/forms";
import { zodResolver } from "@primevue/forms/resolvers/zod";
import { useToast } from "primevue";
import { onMounted, ref } from "vue";
import z from "zod";

const initialValues = ref<{
  title: string;
  description?: string;
  button_text: string;
  button_url: string;
}>();

const schema = z.object({
  title: z.string().min(1, "Titel ist erforderlich"),
  description: z.string().optional(),
  button_text: z.string().optional(),
  button_url: z.string().url("Ungültige URL").optional(),
});
type Schema = z.infer<typeof schema>;

const resolver = ref(zodResolver(schema));
const toast = useToast();
const editorStore = useEditorStore();

onMounted(() => {
  if (!editorStore.form.outro_settings) {
    editorStore.form.outro_settings = {
      title: "Dein Formular wurde erfolgreich gesendet!",
      description: "Wir haben Ihre Informationen erhalten und werden uns in Kürze bei Ihnen melden.",
      button_text: "Zurück zur Startseite",
      button_url: "/",
    };
  }
  initialValues.value = {
    title: editorStore.form.outro_settings.title || "",
    description: editorStore.form.outro_settings.description || "",
    button_text: editorStore.form.outro_settings.button_text || "",
    button_url: editorStore.form.outro_settings.button_url || "",
  };
});

const onFormSubmit = async (event: FormSubmitEvent<Schema>) => {
  if (!event.valid) {
    return;
  }
  const formData = {
    title: event.values.title,
    description: event.values.description || "",
    button_text: event.values.button_text || "",
    button_url: event.values.button_url || "",
  };
  editorStore.form.outro_settings = formData;
  try {
    await editorStore.saveFormState();
    toast.add({
      severity: "success",
      summary: "Erfolg",
      detail: "Einstellungen gespeichert",
    });
  } catch (error) {
    if (error instanceof z.ZodError) {
      error.errors.forEach((err) => {
        toast.add({
          severity: "error",
          summary: "Fehler",
          detail: err.message,
        });
      });
    }
  }
};
</script>

<style scoped></style>
