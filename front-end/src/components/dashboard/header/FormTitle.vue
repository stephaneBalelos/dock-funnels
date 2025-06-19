<template>
  <div class="flex items-center gap-4">
    <p class="text-stone-900 font-semibold text-lg">
      <Icon icon="heroicons:document-text" class="inline mr-2" />
      {{ editorStore.form.title }}
    </p>
    <Button label="Show" @click="showEditFormDialog = true" size="small" severity="secondary">
      <Icon icon="heroicons:pencil-square" />
      Einstellungen
    </Button>
    <Dialog
      v-model:visible="showEditFormDialog"
      modal
      header="Formular Einstellungen"
      :style="{ width: '25rem' }"
    >
      <span class="text-surface-500 dark:text-surface-400 block mb-8"
        >
        Passen Sie die Einstellungen für Ihr Formular an. Hier können Sie den
        Titel, die Beschreibung und andere Details bearbeiten.
        </span
      >
      <div class="flex flex-col gap-4 mb-4">
        <label for="form-title" class="font-semibold w-24">Titel</label>
        <InputText id="form-title" class="flex-auto" autocomplete="off" v-model="state.title" />
      </div>
      <div class="flex flex-col gap-4 mb-8">
        <label for="form-description" class="font-semibold w-24">Beschreibung</label>
        <InputText id="form-description" class="flex-auto" autocomplete="off" v-model="state.description" />
      </div>
      <div class="flex justify-end gap-2">
        <Button
          type="button"
          label="Abbrechen"
          size="small"
          severity="secondary"
          @click="showEditFormDialog = false"
        ></Button>
        <Button type="button" label="Speichern" size="small" @click="updateForm"></Button>
      </div>
    </Dialog>
  </div>
</template>

<script setup lang="ts">
import { useEditorStore } from "@/dashboard/editor.store";
import { Icon } from "@iconify/vue";
import { onMounted, ref } from "vue";
import z from "zod";

const editorStore = useEditorStore();
const showEditFormDialog = ref(false);

const schema = z.object({
  title: z.string().min(1, "Titel ist erforderlich"),
  description: z.string().optional(),
});

const state = ref<z.infer<typeof schema>>({
  title: editorStore.form?.title || "",
  description: editorStore.form?.description || "",
});

onMounted(() => {
  if (editorStore.form) {
    state.value.title = editorStore.form.title;
    state.value.description = editorStore.form.description || "";
  }
});

console.log("FormTitle.vue state:", state.value);
const updateForm = () => {
  const result = schema.safeParse(state.value);
  if (result.success && editorStore.form) {
    editorStore.form.title = result.data.title;
    editorStore.form.description = result.data.description || "";
    showEditFormDialog.value = false;
  } else {
    // Handle validation errors
    console.error(result.error);
  }
};

console.log("FormTitle.vue editorStore.form:", editorStore.form);
</script>

<style scoped></style>
