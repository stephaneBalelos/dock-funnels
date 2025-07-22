<template>
  <div class="flex flex-col gap-4 py-4">
    <Form
      :initial-values="state"
      @change="validateForm"
      @submit="(e) => onFormSubmit(e as FormSubmitEvent<FormFieldSubmissionSummary>)"
      class="flex flex-col gap-4 w-full"
    >
      <div class="flex items-center">
        <div class="text-lg leading-[18px] font-semibold">
          Zusammenfassung Feld
        </div>
        <Button
          v-if="editorStore.form"
          :type="'submit'"
          size="small"
          class="ml-auto"
        >
          Speichern
        </Button>
        <Button
          v-if="editorStore.form"
          size="small"
          severity="danger"
          class="ml-2"
          @click="editorStore.removeField(props.fieldName)"
        >
          <Icon icon="heroicons:trash" />
        </Button>
      </div>
      <div class="flex flex-col gap-4">
        <FormField name="label" class="flex flex-col gap-1">
          <InputText
            type="text"
            placeholder="Label"
            size="small"
            v-model="state.label"
            @change="generateSlug"
          />
          <Message
            v-if="errorState?.errors.find((e) => e.path.join('.') === 'label')"
            severity="error"
            size="small"
            variant="simple"
            >{{
              errorState?.errors.find((e) => e.path.join(".") === "label")
                ?.message
            }}</Message
          >
        </FormField>
        <FormField name="description" class="flex flex-col gap-1">
          <InputText
            type="text"
            placeholder="Beschreibung (optional)"
            size="small"
            v-model="state.description"
          />
        </FormField>
        <FormField name="show_full_summary" class="flex flex-col gap-2">
          <p class="shrink text-sm text-gray-700 font-semibold">
            Vollständige Zusammenfassung anzeigen <br />
            <span class="text-xs text-gray-500 font-normal">
              Wenn aktiviert, wird die vollständige Zusammenfassung angezeigt,
              andernfalls nur die Felder, die aus vorherigen Schritten
              ausgefüllt wurden.
            </span>
          </p>
          <ToggleSwitch name="required" v-model="state.show_full_summary" />
        </FormField>
      </div>
    </Form>
  </div>
</template>

<script setup lang="ts">
import { reactive, ref } from "vue";
import { computed } from "vue";
import { useEditorStore } from "@/dashboard/editor.store";
import type { FormFieldSubmissionSummary } from "@/types";
import type { FormSubmitEvent } from "@primevue/forms";
import z from "zod";
import { slugify } from "@/utils";
import { Icon } from "@iconify/vue";

type Props = {
  fieldName: string;
};

const props = defineProps<Props>();
const editorStore = useEditorStore();

const field = computed(() => {
  return editorStore.form.form_fields.find(
    (field) => field.field_name === props.fieldName
  ) as FormFieldSubmissionSummary;
});

const schema = z.object({
  field_name: z
    .string()
    .regex(
      /^[a-zA-Z0-9_]+$/,
      "Feld Name muss alphanumerisch oder mit Unterstrichen sein"
    ),
  label: z.string().min(1, "Label ist erforderlich"),
  description: z.string().optional(),
  show_full_summary: z.boolean().optional(),
});

const state = reactive<FormFieldSubmissionSummary>({
  step_index: field.value.step_index,
  type: field.value.type,
  show_full_summary: field.value.show_full_summary,
  required: field.value.required,
  field_name: field.value.field_name,
  label: field.value.label,
  description: field.value.description,
  depends_on: field.value.depends_on || [],
});

const errorState = ref<z.ZodError<FormFieldSubmissionSummary> | null>(null);

function onFormSubmit($event: FormSubmitEvent<FormFieldSubmissionSummary>) {
  // Validate the form before proceeding
  if (!validateForm()) {
    console.error("Form validation failed", errorState.value);
    return;
  }
  // Here you can handle the form submission, e.g., save the state or emit an event
  if ($event.valid) {
    editorStore.updateField(props.fieldName, {
      description: state.description,
      label: state.label,
      field_name: state.field_name,
      show_full_summary: state.show_full_summary,
    });
  } else {
    console.error("Form validation failed:", $event.errors);
  }
}

const validateForm = () => {
  try {
    const res = schema.safeParse(state, {});
    if (!res.success) {
      errorState.value = res.error;
      return false;
    } else {
      errorState.value = null;
      return true;
    }
  } catch (error) {
    return false;
  }
};

const generateSlug = () => {
  if (state.label) {
    state.field_name = slugify(state.label);
  }
};
</script>

<style scoped></style>
