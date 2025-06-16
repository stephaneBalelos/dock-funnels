<script setup lang="ts">
import { onMounted } from "vue";
import { useEditorStore } from "./editor.store";
import FormTitle from "@/components/dashboard/header/FormTitle.vue";
import StepItem from "@/components/dashboard/sidebar-left/StepItem.vue";
import { Icon } from "@iconify/vue";

// const ajaxUrl = window.DockFunnelsAdmin?.ajaxUrl || '/wp-admin/admin-ajax.php';

const editorStore = useEditorStore();

onMounted(() => {
  // This is a good place to initialize any global state or perform side effects
  console.log("App mounted");
  console.log(window.DockFunnelsAdmin);
  editorStore.initEditor();
});
</script>

<template>
  <div class="h-lvh app-container">
    <div class="header flex items-center">
      <div>
        <FormTitle />
      </div>
    </div>
    <div class="sidebar-left flex flex-col">
      <div class="flex justify-between items-center mb-4 p-4">
        <div class="text-lg leading-[18px] font-semibold">
          Formular Schritte
        </div>
        <button
          @click="editorStore.addStep()"
          class="w-5 h-5 hover:text-green-600 transition-colors duration-200"
        >
          <Icon icon="heroicons:plus" />
        </button>
      </div>
      <div class="relative flex-1 overflow-y-auto">
        <div class="absolute inset-0 overflow-y-scroll divide-y divide-gray-200">
          <StepItem
            v-for="(step, index) in editorStore.form.value.form_steps"
            :key="'step-' + index"
            :step="step"
          >
          </StepItem>
        </div>
      </div>
    </div>
    <div class="main">
      <div class="toolbar">toolbar</div>
      form preview
    </div>
    <div class="sidebar-right">sidebar-right</div>
  </div>
</template>

<style scoped></style>
