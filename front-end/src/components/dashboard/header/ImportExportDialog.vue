<template>
  <div>
    <slot :closeDialog="closeDialog" :openDialog="openDialog">
      <Button label="Import/Export" @click="openDialog" size="small" />
    </slot>
    <Dialog
      v-model:visible="visible"
      modal
      header="Formular Importieren/Exportieren"
      :style="{ width: '100%', maxWidth: '800px' }"
      :breakpoints="{ '1199px': '75vw', '575px': '90vw' }"
    >
      <Tabs value="0">
        <TabList>
          <Tab value="0">Importieren</Tab>
          <Tab value="1">Exportieren</Tab>
        </TabList>
        <TabPanels>
          <TabPanel value="0">
            <div v-if="state" class="mb-4">
              <p class="text-sm text-gray-600">
                Formular: <strong>{{ state.title }}</strong>
              </p>
              <p class="text-sm text-gray-600">
                Beschreibung: <strong>{{ state.description }}</strong>
              </p>
              <p class="text-sm text-gray-600">
                Schritte: <strong>{{ state.form_steps.length }}</strong>
              </p>
              <Button
                label="Importieren"
                @click="confirmImport"
                severity="primary"
                class="w-full"
              />
            </div>
            <FileUpload 
            v-else
              @select="handleSelect"
              ref="fileupload"
              mode="basic"
              name="import[]"
              accept=".json"
              :auto="true"
              :maxFileSize="1000000"
            >
            </FileUpload>
          </TabPanel>
          <TabPanel value="1">
            <Button label="Export Form" @click="exportFormJson" />
          </TabPanel>
        </TabPanels>
      </Tabs>
    </Dialog>
  </div>
</template>

<script setup lang="ts">
import { useEditorStore } from "@/dashboard/editor.store";
import type { FormState } from "@/types";
import { slugify } from "@/utils";
import { ref } from "vue";

const visible = ref(false);

const state = ref<FormState>();
const editorStore = useEditorStore();

const openDialog = () => {
  visible.value = true;
};
const closeDialog = () => {
  visible.value = false;
};

const handleSelect = (event: any) => {
  const file = event.files[0];
  if (file) {
    const reader = new FileReader();
    reader.onload = (e) => {
      try {
        const formData = JSON.parse(e.target?.result as string);
        state.value = formData;
        console.log("Selected form data:", formData);
      } catch (error) {
        console.error("Error parsing JSON:", error);
      }
    };
    reader.readAsText(file);
  } else {
    console.error("No file selected or file is empty.");
  }
};

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

const confirmImport = () => {
  if (state.value) {
    // Assuming you have a method to handle the import of the form data
    editorStore.initEditor(state.value);
    closeDialog();
  } else {
    console.error("No form data to import.");
  }
};
</script>

<style scoped></style>
