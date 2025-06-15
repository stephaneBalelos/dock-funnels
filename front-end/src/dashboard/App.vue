<script setup lang="ts">
import { onMounted } from "vue";
import { Icon } from '@iconify/vue'
import { EditableArea, EditableCancelTrigger, EditableEditTrigger, EditableInput, EditablePreview, EditableRoot, EditableSubmitTrigger } from 'reka-ui'
import { useEditorStore } from "./editor.store";


// const ajaxUrl = window.DockFunnelsAdmin?.ajaxUrl || '/wp-admin/admin-ajax.php';

const editorStore = useEditorStore();

onMounted(() => {
  // This is a good place to initialize any global state or perform side effects
  console.log("App mounted");
  console.log(window.DockFunnelsAdmin);
  editorStore.initEditor()
});

</script>

<template>
  <div class="h-lvh app-container">
    <div class="header flex items-center">
      <div>
        <EditableRoot
          v-slot="{ isEditing }"
          v-model="editorStore.form.value.title"
          placeholder="Mein Formular Titel"
          class="flex items-center gap-4"
          auto-resize
        >
          <EditableArea class="text-stone-700">
            <EditablePreview class="text-stone-900 font-semibold text-lg" />
            <EditableInput
              class="w-full text-stone-900 placeholder:text-stone-700 font-semibold text-lg"
            />
          </EditableArea>
          <EditableEditTrigger
            v-if="!isEditing"
            class="w-max inline-flex items-center justify-center rounded-lg font-medium text-sm px-[15px] leading-[35px] h-[35px] bg-white text-green11 shadow-sm border outline-none hover:bg-stone-50 focus:shadow-[0_0_0_2px] focus:shadow-black"
          >
          <Icon icon="heroicons:pencil-square" class="w-4 h-4" />
          </EditableEditTrigger>
          <div v-else class="flex gap-2">
            <EditableSubmitTrigger
              class="inline-flex items-center justify-center rounded-lg font-medium text-sm px-[15px] leading-[35px] h-[35px] bg-white text-green11 shadow-sm border outline-none hover:bg-stone-50 focus:shadow-[0_0_0_2px] focus:shadow-black"
            >
              <Icon icon="heroicons:check" class="w-4 h-4" />
            </EditableSubmitTrigger>
            <EditableCancelTrigger
              class="inline-flex items-center justify-center rounded-lg font-medium text-sm px-[15px] leading-[35px] h-[35px] bg-red-700 text-white shadow-sm border outline-none hover:bg-red10 focus:shadow-[0_0_0_2px] focus:shadow-black"
            >
              <Icon icon="heroicons:x-mark" class="w-4 h-4" />
            </EditableCancelTrigger>
          </div>
        </EditableRoot>
      </div>
    </div>
    <div class="sidebar-left">sidebar-left</div>
    <div class="main">
      <div class="toolbar">toolbar</div>
      form preview
    </div>
    <div class="sidebar-right">sidebar-right</div>
  </div>
</template>

<style scoped></style>
