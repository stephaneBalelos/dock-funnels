<template>
  <div class="flex gap-4 p-4 items-start w-full">
    <div class="flex flex-2 flex-col gap-4 bg-gray-100 p-4 rounded-lg h-full">
      <h2 class="text-xl font-semibold">Aktionen bei Einreichungen</h2>
      <p class="text-gray-600">
        Hier können Sie Aktionen konfigurieren, die ausgeführt werden, wenn ein
        Formular eingereicht wird.
      </p>
      <div class="relative flex flex-col flex-1 h-full gap-4">
        <div class="absolute overflow-auto h-full inset-0 p-4 flex flex-col gap-4">
                  <div
          v-for="(action, index) in editStore.form.form_settings.onSubmitAction"
          :key="index"
          :class="selectedActionIdx == index ? 'border border-blue-300' : ''"
          class="rounded-lg shadow hover:shadow-lg transition-shadow duration-200 cursor-pointer"
        >
          <Card v-if="action.type == 'mail'" style="overflow: hidden">
            <template #title>{{ action.type }}</template>
            <template #subtitle>
              E-Mail an Absender des Formulars senden
            </template>
            <template #footer>
              <div class="flex gap-4 mt-1">
                <Button
                  label="Löschen"
                  severity="danger"
                  outlined
                    size="small"
                  class="w-full"
                  @click="() => editStore.removeSubmissionAction(index)"
                />
                <Button label="Bearbeiten" size="small" class="w-full" @click="() => editAction(index)" />
              </div>
            </template>
          </Card>
          <Card v-else-if="action.type == 'redirect'" style="overflow: hidden">
            <template #title>{{ action.type }}</template>
            <template #subtitle> Weiterleitung nach Einreichung </template>
            <template #footer>
              <div class="flex gap-4 mt-1">
                <Button
                  label="Löschen"
                  severity="danger"
                  size="small"
                  outlined
                  class="w-full"
                    @click="() => editStore.removeSubmissionAction(index)"
                />
                <Button label="Bearbeiten" size="small" class="w-full" @click="() => editAction(index)" />
              </div>
            </template>
          </Card>
        </div>
        <Card class="flex flex-col">
          <template #title>Neue Aktion hinzufügen</template>
          <template #content>
            <p class="m-0">
              Wählen Sie eine Aktion aus, die ausgeführt werden soll, wenn das
              Formular eingereicht wird.
            </p>
            <div class="flex flex-col gap-2 mt-2">
              <Button
                label="E-Mail senden"
                severity="secondary"
                class="w-full"
                @click="addAction('mail')"
              />
              <Button
                label="Weiterleiten"
                severity="secondary"
                class="w-full"
                @click="addAction('redirect')"
              />
            </div>
          </template>
        </Card>
        </div>
      </div>
    </div>
    <div class="flex-1 h-full bg-white p-4 rounded-lg shadow">
        <SubmissionActionEditor v-if="selectedActionIdx != undefined" :action-index="selectedActionIdx" />
    </div>
  </div>
</template>

<script setup lang="ts">
import { useEditorStore } from "@/dashboard/editor.store";
import SubmissionActionEditor from "./SubmissionActionEditor.vue";
import { ref } from "vue";
import type { FormOnSubmitAction } from "@/types";

const editStore = useEditorStore();

const selectedActionIdx = ref<number>();

const addAction = (actionType: FormOnSubmitAction['type']) => {
    let newAction: FormOnSubmitAction;
    if (actionType === 'mail') {
        newAction = {
            type: 'mail',
            email_field: '',
            subject: '',
            body: ''
        };
    } else if (actionType === 'redirect') {
        newAction = {
            type: 'redirect',
            url: '',
            open_in_new_tab: false
        };
    } else {
        return;
    }
    if (!editStore.form.form_settings.onSubmitAction) {
        editStore.form.form_settings.onSubmitAction = [];
    }
    editStore.form.form_settings.onSubmitAction.push(newAction);
};

const editAction = (index: number) => {
    selectedActionIdx.value = index;
};
</script>

<style scoped></style>
