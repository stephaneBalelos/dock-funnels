<template>
  <div>
    <Form
      v-if="
        editorStore.form?.form_settings.onSubmitAction[props.actionIndex]
          ?.type === 'redirect'
      "
      v-slot="$form"
      :initial-values="initialValues"
      :resolver="resolver"
      @submit="($event) => onFormSubmit($event as FormSubmitEvent<Schema>)"
      class="flex justify-center flex-col gap-4"
    >
      <div class="flex flex-col gap-1">
        <label for="url" class="font-semibold">Weiterleitung url</label>
        <InputText
          id="url"
          name="url"
          type="url"
          placeholder="https://example.com/redirect"
        />
        <Message
          v-if="$form.url?.invalid"
          severity="error"
          size="small"
          variant="simple"
          >{{ $form.url.error?.message }}</Message
        >
      </div>
      <div class="flex items-center gap-2">
        <ToggleSwitch 
          id="open_in_new_tab"
          name="open_in_new_tab"></ToggleSwitch>
        <label for="open_in_new_tab">In neuem Tab öffnen</label>
      </div>
      <Button type="submit" severity="secondary" label="Speichern" />
    </Form>
    <Message v-else severity="info" size="small" variant="simple">
      Diese Aktion ist nicht für eine Weiterleitung konfiguriert.
    </Message>
  </div>
</template>

<script setup lang="ts">
import { useEditorStore } from "@/dashboard/editor.store";
import type { FormOnSubmitActionRedirect } from "@/types";
import type { FormSubmitEvent } from "@primevue/forms";
import { zodResolver } from "@primevue/forms/resolvers/zod";
import { useToast } from "primevue";
import { onMounted, ref } from "vue";
import z from "zod";

type Props = {
  actionIndex: number;
};

const props = defineProps<Props>();

const editorStore = useEditorStore();
const initialValues = ref<FormOnSubmitActionRedirect>({
    type: "redirect",
    url: "",
    open_in_new_tab: false,
});

const schema = z.object({
  url: z.string().url({ message: "Geben Sie eine gültige URL ein." }),
    open_in_new_tab: z.boolean().default(false),
});

type Schema = z.infer<typeof schema>;

const resolver = ref(zodResolver(schema));
const toast = useToast();

onMounted(() => {
  const action = editorStore.form.form_settings.onSubmitAction[
    props.actionIndex
  ] as FormOnSubmitActionRedirect;

  console.log("Mounted redirect action editor with action:", action);
  initialValues.value = {
    type: action.type,
    url: action.url || "",
    open_in_new_tab: action.open_in_new_tab || false,
  };
});

const onFormSubmit = async (e: FormSubmitEvent<Schema>) => {
  const formData = e.values;


  if (!e.valid) {
    return;
  }

  editorStore.saveSubmissionAction(props.actionIndex, {
    type: "redirect",
    url: formData.url.trim(),
    open_in_new_tab: formData.open_in_new_tab,
  } as FormOnSubmitActionRedirect);

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
      detail: "Ein Fehler ist beim Speichern der E-Mail-Aktion aufgetreten.",
    });
    return;
  }
};
</script>

<style scoped></style>
