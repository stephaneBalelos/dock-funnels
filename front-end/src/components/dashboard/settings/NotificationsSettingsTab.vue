<template>
  <Form
    v-slot="$form"
    :resolver="resolver"
    :initialValues="initialValues"
    :validate-on-blur="true"
    @submit="$e => onFormSubmit($e as FormSubmitEvent<Schema>)"
    class="flex justify-center flex-col gap-4"
  >
    <div class="flex flex-col gap-1">
      <label for="form-emails" class="font-semibold">
        Empfänger E-Mail-Adressen (kommagetrennt)
      </label>
      <InputText
        id="form-emails"
        name="emails"
        type="text"
        placeholder="E-Mail-Adressen (kommagetrennt)"
        spellcheck="false"
      />
      <Message
        v-if="$form.emails?.invalid"
        severity="error"
        size="small"
        variant="simple"
        >{{ $form.emails.error?.message }}</Message
      >
    </div>
    <div class="flex flex-col gap-1">
      <label for="form-subject" class="font-semibold">E-Mail Betreff</label>
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
import { onMounted, ref } from "vue";
import { zodResolver } from "@primevue/forms/resolvers/zod";
import { useToast } from "primevue/usetoast";
import { z } from "zod";
import type { FormSubmitEvent } from "@primevue/forms";
import { useEditorStore } from "@/dashboard/editor.store";

const initialValues = ref({
  emails: "",
  subject: "",
  body: "",
});

const schema = z
  .object({
    emails: z
      .string()
      .min(1, { message: "Geben Sie mindestens eine E-Mail-Adresse ein." }),
    subject: z.string().min(1, { message: "Geben Sie einen Betreff ein." }),
    body: z.string().min(1, { message: "Geben Sie den E-Mail-Inhalt ein." }),
  })
  .superRefine((data, ctx) => {
    const emails = data.emails.split(",").map((email) => email.trim());
    for (let i = 0; i < emails.length; i++) {
      if (!z.string().email().safeParse(emails[i]).success) {
        if (emails[i] !== "") {
          // Only add issue for non-empty invalid emails
          ctx.addIssue({
            path: ["emails"],
            code: z.ZodIssueCode.custom,
            message: `Ungültige E-Mail-Adresse: ${emails[i]}`,
          });
        }
      }
    }
    if (emails.length === 0) {
      ctx.addIssue({
        path: ["emails"],
        code: z.ZodIssueCode.custom,
        message: "Geben Sie mindestens eine E-Mail-Adresse ein.",
      });
    }
  });

type Schema = z.infer<typeof schema>;

const resolver = ref(zodResolver(schema));
const toast = useToast();
const editorStore = useEditorStore();

const onFormSubmit = ($event: FormSubmitEvent<Schema>) => {
  if (!$event.valid) {
    return;
  }
    editorStore.form.form_settings.notifications_settings.emails = $event.values.emails;
    editorStore.form.form_settings.notifications_settings.subject = $event.values.subject;
    editorStore.form.form_settings.notifications_settings.body = $event.values.body;
  editorStore.saveFormState();
  toast.add({
    severity: "success",
    summary: "Einstellungen gespeichert",
    life: 3000,
  });
};

onMounted(() => {
  if (editorStore.form) {
    initialValues.value = {
      emails: editorStore.form.form_settings.notifications_settings.emails || "",
      subject: editorStore.form.form_settings.notifications_settings.subject || "",
      body: editorStore.form.form_settings.notifications_settings.body || "",
    };
  }
})
</script>

<style scoped></style>
