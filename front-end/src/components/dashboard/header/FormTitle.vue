<template>
  <div class="flex items-center gap-4">
    <p class="text-stone-900 font-semibold text-lg">
      <Icon icon="heroicons:document-text" class="inline mr-2" />
      {{ editorStore.form.title }}
    </p>
    <Badge v-if="editFormId" :value="`Formular ID: ${editFormId}`" size="small"></Badge>
    <Badge v-else severity="success" size="small">Neues Formular</Badge>
  </div>
</template>

<script setup lang="ts">
import { useEditorStore } from "@/dashboard/editor.store";
import { Icon } from "@iconify/vue";
import { inject, onMounted, ref } from "vue";
import z from "zod";

const editorStore = useEditorStore();
const editFormId = inject("editFormId") as number | undefined;


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
</script>

<style scoped></style>
