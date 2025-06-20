<template>
  <div class="flex flex-col gap-4 py-4">
    <Form
      :initial-values="state"
      @change="validateForm"
      @submit="(e) => onFormSubmit(e as FormSubmitEvent<FormFieldText>)"
      class="flex flex-col gap-4 w-full"
    >
      <div class="flex items-center">
        <div class="text-lg leading-[18px] font-semibold">TextFeld</div>
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
        <FormField name="field_name" class="flex flex-col gap-1">
          <InputText
            type="text"
            placeholder="Field Name (alphanumeric or underscore)"
            size="small"
            v-model="state.field_name"
          />
          <Message
            v-if="
              errorState?.errors.find((e) => e.path.join('.') === 'field_name')
            "
            severity="error"
            size="small"
            variant="simple"
            >{{
              errorState?.errors.find((e) => e.path.join(".") === "field_name")
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
        <FormField name="required" class="flex items-center gap-2">
          <ToggleSwitch name="required" v-model="state.required" />
          <label class="text-sm"> Ist dieses Feld erforderlich? </label>
          <Message
            v-if="
              errorState?.errors.find((e) => e.path.join('.') === 'required')
            "
            severity="error"
            size="small"
            variant="simple"
            >{{
              errorState?.errors.find((e) => e.path.join(".") === "required")
                ?.message
            }}</Message
          >
        </FormField>
        <FormField name="input_type" class="flex flex-col gap-1">
          <Select
            v-model="state.input_type"
            placeholder="Eingabetyp"
            :option-label="'label'"
            :option-value="'value'"
            :options="[
              { label: 'Text', value: 'text' },
              { label: 'E-Mail', value: 'email' },
              { label: 'Nummer', value: 'number' },
              { label: 'Telefon', value: 'tel' },
              { label: 'URL', value: 'url' },
              { label: 'Datum', value: 'date' }
            ]"
            size="small"
          />
          <Message
            v-if="
              errorState?.errors.find((e) => e.path.join('.') === 'input_type')
            "
            severity="error"
            size="small"
            variant="simple"
            >{{
              errorState?.errors.find((e) => e.path.join(".") === "input_type")
                ?.message
            }}</Message
          >
        </FormField>
        <FormField name="placeholder" class="flex flex-col gap-1">
          <InputText
            type="text"
            placeholder="Platzhalter (optional)"
            size="small"
            v-model="state.placeholder"
          />
          <Message
            v-if="
              errorState?.errors.find((e) => e.path.join('.') === 'placeholder')
            "
            severity="error"
            size="small"
            variant="simple"
            >{{
              errorState?.errors.find((e) => e.path.join(".") === "placeholder")
                ?.message
            }}</Message
          >
        </FormField>
        <FormField name="default_value" class="flex flex-col gap-1">
          <InputText
            type="text"
            placeholder="Standardwert (optional)"
            size="small"
            v-model="state.default_value"
          />
          <Message
            v-if="
              errorState?.errors.find((e) => e.path.join('.') === 'default_value')
            "
            severity="error"
            size="small"
            variant="simple"
            >{{
              errorState?.errors.find((e) => e.path.join(".") === "default_value")
                ?.message
            }}</Message
          >
        </FormField>

      </div>
      <Fieldset legend="Form " class="h-80 overflow-auto">
        <pre class="whitespace-pre-wrap">{{ state }}</pre>
      </Fieldset>
    </Form>
  </div>
</template>

<script setup lang="ts">
import { reactive, ref } from "vue";
import { computed } from "vue";
import { useEditorStore } from "@/dashboard/editor.store";
import type { FormFieldText } from "@/types";
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
  return editorStore.form.fields.find(
    (field) => field.field_name === props.fieldName
  ) as FormFieldText;
});

const schema = z
  .object({
    input_type: z.enum(["text", "email", "number", "tel", "url", "date"], {
      errorMap: (issue, ctx) => {
        if (issue.code === "invalid_enum_value") {
          return { message: "Ungültiger Eingabetyp" };
        }
        return { message: ctx.defaultError };
      },
    }),
    required: z.boolean(),
    field_name: z
      .string()
      .regex(
        /^[a-zA-Z0-9_]+$/,
        "Feld Name muss alphanumerisch oder mit Unterstrichen sein"
      ),
    label: z.string().min(1, "Label ist erforderlich"),
    description: z.string().optional(),
    placeholder: z.string().optional(),
    default_value: z.string().optional(),
  })

  console.log("Field Text Editor", field.value);

const state = reactive<FormFieldText>({
  step_index: field.value.step_index,
  type: field.value.type,
  input_type: field.value.input_type,
  required: field.value.required,
  field_name: field.value.field_name,
  label: field.value.label,
  description: field.value.description,
  placeholder: field.value.placeholder,
  default_value: field.value.default_value,
});

const errorState = ref<z.ZodError<FormFieldText> | null>(null);

function onFormSubmit(_$event: FormSubmitEvent<FormFieldText>) {
  // Validate the form before proceeding
  if (!validateForm()) {
    console.error("Form validation failed", errorState.value);
    return;
  }
  console.log("Form submitted successfully", state);
  // Here you can handle the form submission, e.g., save the state or emit an event
  editorStore.updateField(props.fieldName, state);
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
