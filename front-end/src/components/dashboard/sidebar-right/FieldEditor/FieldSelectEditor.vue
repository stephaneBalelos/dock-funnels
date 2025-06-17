<template>
  <div class="">
    <Form
      v-slot="$form"
      :resolver
      :initial-values="state"
      @submit="(e) => onFormSubmit(e as FormSubmitEvent<FormFieldSelect>)"
      class="flex flex-col gap-4 w-full"
    >
      <div class="flex flex-col gap-4">
        <FormField v-slot="$field" name="label" class="flex flex-col gap-1">
          <InputText type="text" placeholder="Label" size="small" />
          <Message
            v-if="$field?.invalid"
            severity="error"
            size="small"
            variant="simple"
            >{{ $field.error?.message }}</Message
          >
        </FormField>
        <FormField
          v-slot="$field"
          name="field_name"
          class="flex flex-col gap-1"
        >
          <InputText
            type="text"
            placeholder="Field Name (alphanumeric or underscore)"
            size="small"
          />
          <Message
            v-if="$field?.invalid"
            severity="error"
            size="small"
            variant="simple"
            >{{ $field.error?.message }}</Message
          >
        </FormField>
        <FormField v-slot="$field" name="description" class="flex flex-col gap-1">
          <InputText type="text" placeholder="Description" size="small" />
          <Message
            v-if="$field?.invalid"
            severity="error"
            size="small"
            variant="simple"
            >{{ $field.error?.message }}</Message
          >
        </FormField>
        <FormField v-slot="$field" name="required" class="flex items-center gap-2">
          <ToggleSwitch name="required" />
          <label class="text-sm">Required</label>
          <Message
            v-if="$field?.invalid"
            severity="error"
            size="small"
            variant="simple"
            >{{ $field.error?.message }}</Message
          >
        </FormField>
        <div class="flex items-center gap-2 pt-4">
          <Button
            type="submit"
            label="speichern"
            severity="primary"
            size="small"
          />
          <Button
            type="button"
            label="Löschen"
            severity="danger"
            size="small"
          />

        </div>
      </div>
      <Fieldset legend="Form " class="h-80 overflow-auto">
        <pre class="whitespace-pre-wrap">{{ $form }}</pre>
      </Fieldset>
    </Form>
  </div>
</template>

<script setup lang="ts">
import { reactive } from "vue";
import { computed } from "vue";
import { useEditorStore } from "@/dashboard/editor.store";
import type { FormFieldSelect } from "@/types";
import { zodResolver } from "@primevue/forms/resolvers/zod";
import z from "zod";
import type { FormSubmitEvent } from "@primevue/forms";

type Props = {
  fieldName: string;
};

const props = defineProps<Props>();
const editorStore = useEditorStore();

const field = computed(() => {
  return editorStore.form.fields.find(
    (field) => field.field_name === props.fieldName
  ) as FormFieldSelect;
});

const resolver = zodResolver(
  z.object({
    label: z.string().min(1, { message: "Label is required" }),
    // field_name is required without spaces and special characters
    field_name: z
      .string()
      .min(1, { message: "Field name is required" })
      .regex(/^[a-zA-Z0-9_]+$/, {
        message: "Field name must be alphanumeric or underscore",
      }),
    description: z.string().optional(),
    required: z.boolean().optional(),
  })
);

const state = reactive<FormFieldSelect>({
  step_index: field.value.step_index,
  type: field.value.type,
  required: field.value.required,
  field_name: field.value.field_name,
  label: field.value.label,
  description: field.value.description,
  options: field.value.options,
});

function onFormSubmit($event: FormSubmitEvent<FormFieldSelect>) {
  console.log("Form submitted with state:", $event);
  // Here you can handle the form submission, e.g., save the state or emit an event
  if ($event.valid) {
    editorStore.updateField(props.fieldName, $event.values);
  } else {
    console.error("Form validation failed:", $event.errors);
  }
}
</script>

<style scoped></style>
