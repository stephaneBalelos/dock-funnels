<template>
  <Card class="mb-4">
    <template #content>
      <div v-if="isEditing">
        <Form
          v-slot="$form"
          :initialValues="state"
          :resolver
          @submit="(e) => onFormSubmit(e as FormSubmitEvent<FormStep>)"
          class="flex flex-col gap-2"
        >
          <div class="flex flex-col gap-1">
            <InputText name="title" type="text" placeholder="Titel" size="small" />
            <Message
              v-if="$form.title?.invalid"
              severity="error"
              size="small"
              variant="simple"
            >
              {{ $form.title.error?.message }}
            </Message>
          </div>
          <div class="flex flex-col gap-1">
            <InputTextarea
              name="description"
              type="text"
              placeholder="Beschreibung"
              rows="3"
            />
            <Message
              v-if="$form.description?.invalid"
              severity="error"
              size="small"
              variant="simple"
            >
              {{ $form.description.error?.message }}
            </Message>
          </div>
          <div class="flex gap-2 justify-end items-center pt-2">
            <Button type="submit" size="small">speichern</Button>
            <Button @click.prevent="cancelEdit" size="small">abbrechen</Button>
          </div>
        </Form>
      </div>
      <div v-else class="flex flex-col gap-2">
        <div class="flex justify-between w-full">
          <div class="flex flex-col">
            <span class="text-stone-900 font-semibold text-sm">
              {{ state.title }}
            </span>
            <span class="text-stone-500 text-xs"> #{{ props.stepIndex }} </span>
          </div>
          <Button
            @click="toggleEdit"
            size="small"
            severity="secondary"
          >
            <Icon icon="heroicons:pencil-square" />
          </Button>
        </div>
        <div class="text-xs text-stone-600">
          {{ state.description }}
        </div>
      </div>
    </template>
  </Card>
</template>

<script setup lang="ts">
import { ref } from "vue";
import type { FormStep } from "@/types";
import { useEditorStore } from "@/dashboard/editor.store";
import { defineProps } from "vue";
import { Icon } from "@iconify/vue";
import Card from "primevue/card";
import InputText from "primevue/inputtext";
import InputTextarea from "primevue/textarea";
import Message from "primevue/message";
import { Form, type FormSubmitEvent } from "@primevue/forms";
import { zodResolver } from "@primevue/forms/resolvers/zod";
import { z } from "zod";

type Props = {
  stepIndex: number;
  step: FormStep;
};
const props = defineProps<Props>();
const isEditing = ref(false);
const editorStore = useEditorStore();
const resolver = ref(
  zodResolver(
    z.object({
      title: z.string().min(1, { message: "Titel ist erforderlich" }),
      description: z.string().optional(),
    })
  )
);

const toggleEdit = () => {
  isEditing.value = !isEditing.value;
  if (isEditing.value) {
    editorStore.setSelectedStepIndex(props.stepIndex);
  }
};

const state = ref({
  title: props.step.title,
  description: props.step.description,
});

const updateStep = () => {
  editorStore.updateStep(props.stepIndex, {
    title: state.value.title,
    description: state.value.description,
  });
  toggleEdit();
};
const cancelEdit = () => {
  state.value.title = props.step.title;
  state.value.description = props.step.description;
  toggleEdit();
};

const onFormSubmit = (form: FormSubmitEvent<FormStep>) => {
  // Handle form submission if needed
  console.log("Form submitted:", form.values.title);
  state.value.title = form.values.title
  state.value.description = form.values.description;
  // Update the step with the new values
  updateStep();
};
</script>

<style scoped></style>
