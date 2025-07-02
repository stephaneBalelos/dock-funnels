<template>
  <div v-if="action">
    <SubmissionActionEditorEmail
      v-if="action.type === 'mail'"
      :action-index="props.actionIndex"
    />
    <SubmissionActionEditorRedirect
      v-else-if="action.type === 'redirect'"
      :action-index="props.actionIndex"
    />
  </div>
  <div v-else class="text-gray-500 flex items-center justify-center h-full">
    <p>Keine Aktion ausgewählt oder nicht implementiert.</p>
  </div>
</template>

<script setup lang="ts">
import SubmissionActionEditorEmail from "./SubmissionActionEditorMail.vue";
import SubmissionActionEditorRedirect from "./SubmissionActionEditorRedirect.vue";
import { useEditorStore } from "@/dashboard/editor.store";
import { computed } from "vue";

type Props = {
    actionIndex: number;
};

const props = defineProps<Props>();
const editStore = useEditorStore();

const action = computed(() => {
    return editStore.form.form_settings.onSubmitAction[props.actionIndex];
});
</script>

<style scoped></style>
