<template>
  <div class="flex flex-col gap-4">
    <div class="flex flex-col gap-4">
      <div class="flex justify-start items-center pt-2">
        <ToggleSwitch v-model="headerSettings.show_progress_bar" @change="onChange"/>
        <label for="primary-color" class="ml-2 text-surface-900 font-semibold"
          >
          Fortschrittsbalken anzeigen?
          </label
        >
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
  footer: FormState["form_settings"]["design_settings"]["footer"];
};

const props = defineProps<Props>();

const toast = useToast();

const editorStore = useEditorStore();


const headerSettings = reactive({
    show_progress_bar: props.footer?.show_progress_bar ?? true,
});

const schema = z.object({
  show_progress_bar: z.boolean().default(true),
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
    editorStore.form.form_settings.design_settings.footer= res.data;
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
