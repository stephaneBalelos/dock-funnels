<template>
  <div class="flex flex-col gap-4">
      <div class="flex flex-col gap-2">
        <div class="flex justify-start items-center pt-2">
          <ColorPicker name="primaryColor" id="primary-color" v-model="colorsSettings.primaryColor" :format="'hex'" @change="onChangePrimaryColor" />
          <label for="primary-color" class="ml-2 text-surface-900 font-semibold"
            >Primärfarbe</label
          >
        </div>
        <!-- <Message
          v-if="$form.color?.invalid"
          severity="error"
          size="small"
          variant="simple"
          >{{ $form.color.error?.message }}</Message
        > -->
      </div>
      <div class="flex flex-col gap-2">
        <div class="flex items-center pt-2">
          <ColorPicker name="surfaceColor" id="surface-color" v-model="colorsSettings.surfaceColor" :format="'hex'" @change="onChangeSurfaceColor"/>
          <label for="surface-color" class="ml-2 text-surface-900 font-semibold"
            >Oberflächenfarbe</label
          >
        </div>
        <!-- <Message
          v-if="$form.color?.invalid"
          severity="error"
          size="small"
          variant="simple"
          >{{ $form.color.error?.message }}</Message
        > -->
      </div>
  </div>
</template>

<script setup lang="ts">
import { onMounted, reactive } from "vue";
import { useToast } from "primevue/usetoast";
import { z } from "zod";
import { useEditorStore } from "@/dashboard/editor.store";
import type { FormState } from "@/types";
import type { ColorPickerChangeEvent } from "primevue";

type Props = {
    colors: FormState["form_settings"]["design_settings"]["colors"];
}

const props = defineProps<Props>();

const toast = useToast();
const schema = z.object({
  primaryColor: z.string().regex(/^(#[0-9A-F]{3,6}|[a-zA-Z]+)$/i, {
    message: "Geben Sie eine gültige Primärfarbe ein",
  }),
  surfaceColor: z.string().regex(/^(#[0-9A-F]{3,6}|[a-zA-Z]+)$/i, {
    message: "Geben Sie eine gültige Oberflächenfarbe ein",
  }),
});

type Schema = z.infer<typeof schema>;

const colorsSettings = reactive<Schema>({
  primaryColor: props.colors.primary,
    surfaceColor: props.colors.surface,
});

const editorStore = useEditorStore();


onMounted(() => {

})

const onChangePrimaryColor = ($event: ColorPickerChangeEvent) => {
    const parsedColor = tryParseColor($event.value, schema.shape.primaryColor);
    if (parsedColor) {
        editorStore.form.form_settings.design_settings.colors.primary = parsedColor;
        editorStore.updateEditorThemePreset();
    }
};
const onChangeSurfaceColor = ($event: ColorPickerChangeEvent) => {
    const parsedColor = tryParseColor($event.value, schema.shape.surfaceColor);
    if (parsedColor) {
        editorStore.form.form_settings.design_settings.colors.surface = parsedColor;
        editorStore.updateEditorThemePreset();
    }
};

const tryParseColor = (color: string, shape: z.ZodString) => {
  try {
    return shape.parse('#' + color);
  } catch (error) {
    if (error instanceof z.ZodError) {
      toast.add({
        severity: "error",
        summary: "Fehler bei der Farbe",
        detail: error.errors[0].message,
        life: 3000,
      });
      return null;
    }
  }
};
</script>

<style scoped></style>
