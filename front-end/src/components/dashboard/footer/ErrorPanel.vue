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
      <div v-if="editorStore.editorState.value.errors">
        <div v-if="editorStore.editorState.value.errors.form_fields">
          <FormFieldsErrors :errors="editorStore.editorState.value.errors.form_fields" />
        </div>
      </div>
    </div>
  </Panel>
</template>

<script setup lang="ts">
import { Icon } from '@iconify/vue';
import { useEditorStore } from '@/dashboard/editor.store';
import { computed } from 'vue';
import FormFieldsErrors from './ErrorGroups/FormFieldsErrors.vue';


const editorStore = useEditorStore();

const hasErrors = computed(() => {
  return editorStore.editorState.value.errors != null
});

</script>

<style scoped></style>
