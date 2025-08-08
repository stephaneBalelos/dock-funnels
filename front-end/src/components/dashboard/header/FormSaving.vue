<template>
  <div>
    <div
      v-if="editorStore.editorState.value.isSaving"
      class="flex items-center"
    >
      <Badge severity="secondary"> Wird gespeichert </Badge>
    </div>
    <SplitButton
      v-if="editorStore.form && editorStore.form.id && !editorStore.editorState.value.isSaving"
      @click="editorStore.saveFormState"
      size="small"
      severity="secondary"
      class="flex items-center"
      :loading="editorStore.editorState.value.isSaving"
      :model="items"
    >
      <Icon icon="heroicons:arrow-down-tray" class="mr-2" />
      {{ editorStore.form.id ? "Speichern" : "Erstellen" }}
    </SplitButton>
    <Button
      v-if="editorStore.form && !editorStore.form.id && !editorStore.editorState.value.isSaving"
      @click="editorStore.saveFormState"
      size="small"
      severity="secondary"
      class="flex items-center"
      :loading="editorStore.editorState.value.isSaving"
    >
      <Icon icon="heroicons:arrow-down-tray" class="mr-2" />
      {{ editorStore.form.id ? "Speichern" : "Erstellen" }}
    </Button>
  </div>
</template>

<script setup lang="ts">
import { useEditorStore } from "@/dashboard/editor.store";
import { Icon } from "@iconify/vue";
import { computed } from "vue";

const editorStore = useEditorStore();
const items = computed(() => {
    const actions = []
    if (editorStore.form.status === "draft") {
        actions.push({
            label: "Veröffentlichen",
            icon: "heroicons:check-circle",
            command: () => {
                editorStore.form.status = "published";
                editorStore.saveFormState();
            },
        });
    } else if (editorStore.form.status === "published") {
        actions.push({
            label: "Als Entwurf speichern (Draft)",
            icon: "heroicons:x-circle",
            command: () => {
                editorStore.form.status = "draft";
                editorStore.saveFormState();
            },
        });
    }
    return actions;
});
</script>

<style scoped></style>
