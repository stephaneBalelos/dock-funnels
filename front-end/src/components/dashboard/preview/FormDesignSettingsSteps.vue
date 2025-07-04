<template>
  <div class="flex flex-col gap-4">
    <div class="flex flex-col gap-4">
      <div class="flex justify-start items-center pt-2">
        <ToggleSwitch
          v-model="stepsSettings.hide_step_header"
          @change="onChange"
        />
        <label for="primary-color" class="ml-2 text-surface-900 font-semibold">
          Schritt-Kopfzeile ausblenden?
        </label>
      </div>
      <div class="flex flex-col" v-if="!stepsSettings.hide_step_header">
        <label for="header-align" class="text-surface-900 font-semibold">
          Ausrichtung der Kopfzeile
        </label>
        <SelectButton
          @change="onChange"
          v-model="stepsSettings.items_align"
          :options="stepAlignOptions"
          :option-value="'value'"
          :option-label="'label'"
          :allow-empty="false"
        />
      </div>
      <div class="flex flex-col" v-if="!stepsSettings.hide_step_header">
        <label for="text-align" class="text-surface-900 font-semibold">
          Textausrichtung
        </label>
        <SelectButton
          @change="onChange"
          v-model="stepsSettings.text_align"
          :options="textAlignOptions"
          :option-value="'value'"
          :option-label="'label'"
          :allow-empty="false"
        />
      </div>
        <div class="flex flex-col">
            <label for="step-transition" class="text-surface-900 font-semibold">
            Schrittübergang
            </label>
            <SelectButton
            @change="onChange"
            v-model="stepsSettings.step_transition"
            :options="stepTransitionOptions"
            :option-value="'value'"
            :option-label="'label'"
            :allow-empty="false"
            />
        </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { useEditorStore } from "@/dashboard/editor.store";
import type { FormState } from "@/types";
import { useToast } from "primevue";
import { reactive } from "vue";
import z from "zod";

type Props = {
  steps: FormState["form_settings"]["design_settings"]["steps"];
};

const props = defineProps<Props>();

const toast = useToast();

const editorStore = useEditorStore();

const textAlignOptions = [
  { label: "Links", value: "text-left" },
  { label: "Zentriert", value: "text-center" },
  { label: "Rechts", value: "text-right" },
];

const stepAlignOptions = [
  { label: "Start", value: "items-start" },
  { label: "Zentriert", value: "items-center" },
  { label: "Ende", value: "items-end" },
];

const stepTransitionOptions = [
  { label: "Standard", value: "default" },
  { label: "Slide", value: "slide" },
];

const stepsSettings = reactive({
  hide_step_header: props.steps?.hide_step_header ?? false,
  text_align: props.steps?.text_align ?? "text-left",
  items_align: props.steps?.items_align ?? "items-start",
  step_transition: props.steps?.step_transition ?? "default",
});

const schema = z.object({
  hide_step_header: z.boolean().default(false),
  text_align: z
    .enum(["text-left", "text-center", "text-right"])
    .default("text-left"),
  items_align: z
    .enum(["items-start", "items-center", "items-end"])
    .default("items-start"),
  step_transition: z.enum(["default", "slide"]).default("default"),
});

const onChange = () => {
  try {
    const res = schema.safeParse(stepsSettings);
    console.log("Steps Settings Schema Validation Result:", res);
    if (!res.success) {
      toast.add({
        severity: "error",
        summary: "Fehler",
        detail: res.error.errors.map((e) => e.message).join(", "),
      });
      return;
    }
    editorStore.form.form_settings.design_settings.steps = res.data;
  } catch (error) {
    toast.add({
      severity: "error",
      summary: "Fehler",
      detail: "Ungültige Einstellungen für die Schritte.",
    });
  }
};
</script>

<style scoped></style>
