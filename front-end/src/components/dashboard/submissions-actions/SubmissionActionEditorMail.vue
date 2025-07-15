<template>
  <Form
    v-if="initialValues"
    v-slot="$form"
    :resolver="resolver"
    :initialValues="initialValues"
    @submit="$e => onFormSubmit($e as FormSubmitEvent<Schema>)"
    class="flex justify-center flex-col gap-4"
  >
    <Message severity="warn" v-if="emailFormFields.length === 0">
      Es sind keine E-Mail-Felder im Formular vorhanden. Bitte fügen Sie ein
      E-Mail-Feld hinzu, um diese Aktion zu verwenden.
    </Message>
    <div class="flex flex-col gap-1">
      <label for="form-email-field" class="font-semibold"> Email Feld </label>
      <p class="text-sm text-gray-600 mb-2">
        Wählen Sie das E-Mail-Feld aus, an das die E-Mail gesendet werden soll.
      </p>
      <Select
        :disabled="emailFormFields.length === 0"
        id="form-email-field"
        name="email_field"
        :options="emailFormFields"
        optionLabel="label"
        option-value="field_name"
        placeholder="Wählen Sie einen Feld"
        class="w-full"
      />
      <Message
        v-if="$form.email_field?.invalid"
        severity="error"
        size="small"
        variant="simple"
        >{{ $form.email_field.error?.message }}</Message
      >
    </div>
    <div class="flex flex-col gap-1">
      <label for="form-subject" class="font-semibold">E-Mail Betreff</label>
      <p class="text-sm text-gray-600 mb-2">
        Der Betreff der E-Mail, die an den Absender gesendet wird. Sie können
        Platzhalter wie {form_name} verwenden, um den Formularnamen einzufügen.
      </p>
      <InputText
        id="form-subject"
        name="subject"
        type="text"
        placeholder="Betreff der E-Mail"
      />
      <Message
        v-if="$form.subject?.invalid"
        severity="error"
        size="small"
        variant="simple"
        >{{ $form.subject.error?.message }}</Message
      >
    </div>
    <div class="flex flex-col gap-1">
      <label for="form-body" class="font-semibold">E-Mail Inhalt</label>
      <p class="text-sm text-gray-600 mb-2">
        Sie können Platzhalter wie {submission_data} verwenden, um die Formulardaten
        einzufügen.
      </p>
      <Editor name="body" editorStyle="height: 320px" />
      <Message
        v-if="$form.content?.invalid"
        severity="error"
        size="small"
        variant="simple"
        >{{ $form.content.error?.message }}</Message
      >
      <Message
        v-if="$form.body?.invalid"
        severity="error"
        size="small"
        variant="simple"
        >{{ $form.body.error?.message }}</Message
      >
    </div>
    <Button type="submit" severity="secondary" label="Speichern" />
  </Form>
</template>

<script setup lang="ts">
import { useEditorStore } from "@/dashboard/editor.store";
import type { FormOnSubmitActionMail } from "@/types";
import type { FormSubmitEvent } from "@primevue/forms";
import { zodResolver } from "@primevue/forms/resolvers/zod";
import { useToast } from "primevue";
import { computed, onMounted, ref } from "vue";
import z from "zod";

type Props = {
  actionIndex: number;
};

const props = defineProps<Props>();

const initialValues = ref<FormOnSubmitActionMail>();
const editorStore = useEditorStore();

const emailFormFields = computed(() => {
  const form = editorStore.form;
  if (!form) return [];

  return form.form_fields
    .filter((field) => field.type === "text")
    .filter((field) => field.input_type === "email")
    .map((field) => ({
      label: field.label,
      field_name: field.field_name,
      value: field.field_name,
    }));
});

const schema = z
  .object({
    email_field: z.string(),
    subject: z.string().min(1, { message: "Geben Sie einen Betreff ein." }),
    body: z.string().min(1, { message: "Geben Sie den E-Mail-Inhalt ein." }),
  })
  .superRefine((data, ctx) => {
    const email_field = data.email_field.trim();
    const allowedFieldsNames = emailFormFields.value.map(
      (field) => field.field_name
    );
    if (!email_field) {
      ctx.addIssue({
        path: ["email_field"],
        code: z.ZodIssueCode.custom,
        message: "Wählen Sie ein E-Mail-Feld aus.",
      });
    } else if (!allowedFieldsNames.includes(email_field)) {
      ctx.addIssue({
        path: ["email_field"],
        code: z.ZodIssueCode.custom,
        message: `Das E-Mail-Feld "${email_field}" ist nicht im Formular vorhanden.`,
      });
    }
  });

type Schema = z.infer<typeof schema>;

const resolver = ref(zodResolver(schema));
const toast = useToast();

const onFormSubmit = async (e: FormSubmitEvent<Schema>) => {
  const formData = e.values;
  console.log(e);

  if (!e.valid) {
    return;
  }

  editorStore.saveSubmissionAction(props.actionIndex, {
    type: "mail",
    email_field: formData.email_field,
    subject: formData.subject,
    body: formData.body,
  } as FormOnSubmitActionMail);

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

onMounted(() => {
  const action = editorStore.form.form_settings.onSubmitAction[
    props.actionIndex
  ] as FormOnSubmitActionMail;

  initialValues.value = {
    type: action.type,
    email_field: action.email_field || "",
    subject: action.subject || "",
    body: action.body || "",
  };
});
</script>

<style scoped></style>
