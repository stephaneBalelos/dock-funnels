<template>
  <div class="flex flex-col gap-4 py-4">
    <Form
      :initial-values="state"
      @change="validateForm"
      @submit="(e) => onFormSubmit(e as FormSubmitEvent<FormFieldSelect>)"
      class="flex flex-col gap-4 w-full"
    >
      <div class="flex items-center">
        <div class="text-lg leading-[18px] font-semibold">Auswahlfeld</div>
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
            placeholder="Description"
            size="small"
            v-model="state.description"
          />
          <Message
            v-if="
              errorState?.errors.find((e) => e.path.join('.') === 'description')
            "
            severity="error"
            size="small"
            variant="simple"
            >{{
              errorState?.errors.find((e) => e.path.join(".") === "description")
                ?.message
            }}</Message
          >
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
        <FormField v-slot="$field" name="options" class="flex flex-col gap-1">
          <div class="flex flex-col gap-2">
            <label class="text-sm font-semibold">
              Auswahlmöglichkeiten (mindestens eine erforderlich)
              <span class="text-red-500">*</span>
            </label>
            <div
              v-for="(_option, index) in state.options"
              :key="index"
              class="flex flex-col gap-2 border p-4 rounded"
            >
              <div class="flex justify-between items-center">
                <div class="flex flex-col">
                  <span class="text-sm font-semibold">
                    Auswahl {{ index + 1 }}
                  </span>
                </div>
                <Button
                  v-if="state.options.length > 1"
                  severity="danger"
                  size="small"
                  @click="state.options.splice(index, 1)"
                >
                  <Icon icon="heroicons:trash" />
                </Button>
              </div>
              <FormField class="flex flex-col gap-1">
                <InputText
                  type="text"
                  placeholder="Option Label"
                  size="small"
                  v-model="state.options[index].label"
                />
                <Message
                  v-if="
                    errorState?.errors.find(
                      (e) => e.path.join('.') === `options.${index}.label`
                    )
                  "
                  severity="error"
                  size="small"
                  variant="simple"
                  >{{
                    errorState?.errors.find(
                      (e) => e.path.join(".") === `options.${index}.label`
                    )?.message
                  }}</Message
                >
              </FormField>
              <FormField class="flex flex-col gap-1">
                <InputText
                  type="text"
                  placeholder="Option Description (optional)"
                  size="small"
                  v-model="state.options[index].description"
                />
                <Message
                  v-if="
                    errorState?.errors.find(
                      (e) => e.path.join('.') === `options.${index}.description`
                    )
                  "
                  severity="error"
                  size="small"
                  variant="simple"
                  >{{
                    errorState?.errors.find(
                      (e) => e.path.join(".") === `options.${index}.description`
                    )?.message
                  }}</Message
                >
              </FormField>
              <div class="flex flex-col border p-2 rounded">
                <div class="flex justify-between items-center">
                  <span class="text-sm font-semibold">
                    Abhängigkeiten (optional)
                  </span>
                  <FieldOptionDependencyInput
                    :field_name="state.field_name"
                    :option_value="state.options[index].value"
                    v-slot="{ toggleDialog }"
                  >
                    <Button
                      severity="secondary"
                      size="small"
                      @click="toggleDialog"
                    >
                      <Icon icon="heroicons:plus" />
                    </Button>
                  </FieldOptionDependencyInput>
                </div>
                <div
                  v-if="
                    state.options[index].depends_on &&
                    state.options[index].depends_on.length > 0
                  "
                  class="flex flex-col mt-2"
                >
                  <div
                    v-for="(dep, dep_idx) in state.options[index].depends_on"
                    class="flex items-center"
                  >
                    <Badge severity="info" class="mr-2"
                      >{{ dep.field_name }} ist gleich {{ dep.value }}</Badge
                    >
                    <Button
                      severity="danger"
                      size="small"
                      @click="
                        editorStore.removeOptionDependency(
                          state.field_name,
                          state.options[index].value,
                          dep_idx
                        )
                      "
                    >
                      <Icon icon="heroicons:trash" />
                    </Button>
                  </div>
                </div>
              </div>
            </div>
            <Button
              severity="secondary"
              size="small"
              @click="
                state.options.push({
                  label: `Auswahl ${state.options.length + 1}`,
                  value: `${fieldName}_${state.options.length + 1}`,
                  description: '',
                  depends_on: [],
                })
              "
            >
              Auswahl hinzufügen
              <Icon icon="heroicons:plus" />
            </Button>
          </div>
          <Message
            v-if="$field?.invalid"
            severity="error"
            size="small"
            variant="simple"
            >{{ $field.error?.message }}</Message
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
import type { FormFieldSelect } from "@/types";
import z from "zod";
import type { FormSubmitEvent } from "@primevue/forms";
import { Icon } from "@iconify/vue";

type Props = {
  fieldName: string;
};

const props = defineProps<Props>();
const editorStore = useEditorStore();

const field = computed(() => {
  return editorStore.form.form_fields.find(
    (field) => field.field_name === props.fieldName
  ) as FormFieldSelect;
});

const schema = z.object({
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
  options: z.array(
    z.object({
      label: z.string().min(1, { message: "Option label is required" }),
      value: z.string().min(1, { message: "Option value is required" }),
      description: z.string().optional(),
    })
  ),
});

const state = reactive<FormFieldSelect>({
  step_index: field.value.step_index,
  type: field.value.type,
  required: field.value.required,
  field_name: field.value.field_name,
  label: field.value.label,
  description: field.value.description,
  options: field.value.options ?? [],
  depends_on: field.value.depends_on || [],
});

const errorState = ref<z.ZodError<FormFieldSelect> | null>(null);

function onFormSubmit(_$event: FormSubmitEvent<FormFieldSelect>) {
  // Validate the form before proceeding
  if (!validateForm()) {
    console.error("Form validation failed");
    return;
  }

  console.log("Form submitted with state:", state);
  // Here you can handle the form submission, e.g., save the state or emit an event
  editorStore.updateField(props.fieldName, state);
}

const validateForm = () => {
  try {
    const res = schema.safeParse(state);
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
</script>

<style scoped></style>
