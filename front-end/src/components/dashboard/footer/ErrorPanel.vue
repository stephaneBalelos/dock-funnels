<template>
  <Panel toggleable collapsed :class="hasErrors ? 'bg-reg-50' : 'bg-white'">
    <template #header>
        <div v-if="hasErrors" class="flex items-center justify-start gap-2">
            <Icon icon="heroicons:exclamation-circle" class="text-red-500" />
            <span class="font-semibold text-red-600">Ihr Formular enthält Fehler</span>
        </div>
        <div v-else class="flex items-center justify-start gap-2">
            <Icon icon="heroicons:check-circle" class="text-green-500" />
            <span class="font-semibold text-green-600">Keine Fehler im Formular</span>
        </div>
    </template>
    <template #togglebutton="{collapsed, toggleCallback}">
        <Button
            v-if="hasErrors"
          size="small"
          severity="danger" variant="text" 
          class="flex items-center"
          @click="toggleCallback"
        >
          <Icon :icon="collapsed ? 'heroicons:chevron-down' : 'heroicons:chevron-up'" />
          {{ collapsed ? 'Details anzeigen' : 'Details ausblenden' }}
        </Button>
        <div v-else>

        </div>
    </template>
    <div v-if="hasErrors">
        {{ editorStore.editorState.value.errors || 'Keine spezifischen Fehler gefunden.' }}
        <Tree :value="errors" class="w-full p-0"></Tree>
    </div>
  </Panel>
</template>

<script setup lang="ts">
import { Icon } from '@iconify/vue';
import { useEditorStore } from '@/dashboard/editor.store';
import { computed } from 'vue';
import type { TreeNode } from 'primevue/treenode';


const editorStore = useEditorStore();

const hasErrors = computed(() => {
  return editorStore.editorState.value.errors != null
});

const errors = computed<TreeNode[]>(() => {
  const errorMap = editorStore.editorState.value.errors;
  if (!errorMap) return [];

  return Object.entries(errorMap).map(([key, value]) => ({
    key,
    label: key,
    data: value,
    children: Array.isArray(value) ? value.map((item, index) => ({
      key: `${key}-${index}`,
      label: Object.values(item).join(', '),
      data: item
    })) : []
  }));
});
</script>

<style scoped></style>
