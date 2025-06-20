<template>
    <Button
      v-if="editorStore.form"
      @click="exportFormJson"
      size="small"
      severity="secondary"
      class="flex items-center"
    >
      <Icon icon="heroicons:arrow-down-tray" class="mr-2" />
        Exportiere Formular als JSON
    </Button>
</template>

<script setup lang="ts">
import { useEditorStore } from "@/dashboard/editor.store";
import { slugify } from "@/utils";
import { Icon } from "@iconify/vue";


const editorStore = useEditorStore();


const exportFormJson = () => {
  if (!editorStore.form) {
    console.error("Kein Formular zum Exportieren gefunden.");
    return;
  }

  const formJson = JSON.stringify(editorStore.form, null, 2);
  const blob = new Blob([formJson], { type: "application/json" });
  const url = URL.createObjectURL(blob);

  const filename = slugify(editorStore.form.title || "form") + "-export.json";
  
  const a = document.createElement("a");
  a.href = url;
  a.download = `${filename}`;
  document.body.appendChild(a);
  a.click();
  
  // Cleanup
  document.body.removeChild(a);
  URL.revokeObjectURL(url);
};


</script>

<style scoped>

</style>