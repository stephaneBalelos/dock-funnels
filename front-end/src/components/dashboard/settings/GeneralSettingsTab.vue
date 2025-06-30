<template>
  <div v-if="editorStore.form" class="flex flex-col">
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
        <label for="form-title" class="font-semibold">Formular Titel</label>

        <InputText
          id="form-title"
          name="title"
          type="text"
          placeholder="Title des Formulars"
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
        <label for="form-description" class="font-semibold"
          >Formular Beschreibung</label
        >
        <InputText
          id="form-description"
          name="description"
          type="text"
          placeholder="Beschreibung des Formulars"
        />
        <Message
          v-if="$form.description?.invalid"
          severity="error"
          size="small"
          variant="simple"
          >{{ $form.description.error?.message }}</Message
        >
      </div>
      <Button :loading="editorStore.editorState.value.isSaving" :disabled="editorStore.editorState.value.isSaving" type="submit" severity="secondary" label="Speichern" />
    </Form>
    <div class="flex flex-col items-start gap-4 mt-10 pt-4 border-t">
      <p>
        Wenn sie diesen Formular löschen, werden alle Daten, die mit diesem
        Formular verbunden sind, gelöscht. Dies kann nicht rückgängig gemacht
        werden.
      </p>
      <Button
        v-if="editorStore.form"
        @click="deleteForm"
        size="small"
        severity="danger"
        class="flex items-center"
      >
        <Icon icon="heroicons:trash" class="mr-2" />
        Löschen
      </Button>
    </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted, ref } from "vue";
import { zodResolver } from "@primevue/forms/resolvers/zod";
import { useToast } from "primevue/usetoast";
import { z } from "zod";
import type { FormSubmitEvent } from "@primevue/forms";
import { useEditorStore } from "@/dashboard/editor.store";
import { Icon } from "@iconify/vue";

const initialValues = ref<{
  title: string;
  description?: string;
}>();

const schema = z.object({
  title: z.string().min(1, { message: "Geben Sie einen Titel ein." }),
  description: z.string().optional(),
});

type Schema = z.infer<typeof schema>;

const resolver = ref(zodResolver(schema));
const toast = useToast();
const editorStore = useEditorStore();

onMounted(() => {
  // Initialize the form with existing data if available
  if (editorStore.form) {
    initialValues.value = {
      title: editorStore.form.title || "",
      description: editorStore.form.description || "",
    };
  }
});

const onFormSubmit = async (event: FormSubmitEvent<Schema>) => {
  if (!event.valid) {
    return;
  }
  editorStore.form.title = event.values.title;
  editorStore.form.description = event.values.description || "";
  try {
    await editorStore.saveFormState();
    toast.add({
      severity: "success",
      summary: "Einstellungen gespeichert",
      life: 3000,
    });
  } catch (error) {
    console.error("Error saving form state:", error);
    toast.add({
      severity: "error",
      summary: "Fehler",
      detail: "Ein Fehler ist beim Speichern des Formulars aufgetreten.",
    });
    return;
  }
};

const deleteForm = async () => {
  if (!confirm("Sind Sie sicher, dass Sie dieses Formular löschen möchten?")) {
    return;
  }
  try {
    editorStore.editorState.value.isLoading = true; // Set loading state
    const res = await editorStore.formDelete();
    console.log("Form deleted:", res);
  } catch (error) {
    console.error("Error deleting form:", error);
    toast.add({
      severity: "error",
      summary: "Fehler",
      detail: "Ein Fehler ist beim Löschen des Formulars aufgetreten.",
    });
  } finally {
    editorStore.editorState.value.isLoading = false; // Reset loading state
  }
};
</script>

<style scoped></style>
