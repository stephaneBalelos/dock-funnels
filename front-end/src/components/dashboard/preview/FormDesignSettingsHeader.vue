<template>
  <div class="flex flex-col gap-4">
    <div class="flex flex-col gap-4">
      <div class="flex justify-start items-center pt-2">
        <ToggleSwitch v-model="headerSettings.show" @change="onChange"/>
        <label for="primary-color" class="ml-2 text-surface-900 font-semibold"
          >Header Anzeigen?</label
        >
      </div>
      <div class="flex flex-col" v-if="headerSettings.show">
        <label for="header-align" class="text-surface-900 font-semibold">
          Ausrichtung der Kopfzeile
        </label>
        <SelectButton @change="onChange" v-model="headerSettings.align" :options="headerAlignOptions" :option-value="'value'" :option-label="'label'" :allow-empty="false" />
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
  header: FormState["form_settings"]["design_settings"]["header"];
};

const props = defineProps<Props>();

const toast = useToast();

const editorStore = useEditorStore();

const headerAlignOptions = [
  { label: "Links", value: "left" },
  { label: "Zentriert", value: "center" },
  { label: "Rechts", value: "right" },
];

const headerSettings = reactive({
  show: props.header?.show ?? true,
  align: props.header?.align ?? "left",
});

const schema = z.object({
  show: z.boolean().default(true),
  align: z.enum(["left", "center", "right"])
});

const onChange = () => {
  try {
    const res = schema.safeParse(headerSettings);
    console.log("Header Settings Schema Validation Result:", res);
    if (!res.success) {
      toast.add({
        severity: "error",
        summary: "Fehler",
        detail: res.error.errors.map((e) => e.message).join(", "),
        
      });
      return;
    }
    editorStore.form.form_settings.design_settings.header = res.data;
  } catch (error) {
    toast.add({
      severity: "error",
      summary: "Fehler",
      detail: "Ungültige Einstellungen für die Kopfzeile.",
    });
  }
};
</script>

<style scoped></style>
