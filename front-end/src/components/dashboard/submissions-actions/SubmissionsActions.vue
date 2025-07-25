<template>
  <div class="w-full max-w-5xl mx-auto p-4">
    <Tabs value="0" class="h-full">
      <TabList>
        <Tab value="0">Nachricht nach Einreichung</Tab>
        <Tab value="1">Aktionen bei Einreichungen</Tab>
      </TabList>
      <TabPanels>
        <TabPanel value="0">
          <h2 class="text-xl font-semibold">Nachricht nach Einreichung</h2>
          <SubmissionOutroForm />
        </TabPanel>
        <TabPanel value="1">
          <div class="flex gap-4 items-start w-full">
            <div
              class="flex flex-1 flex-col gap-4 bg-gray-100 p-4 rounded-lg h-full"
            >
              <h2 class="text-xl font-semibold">Aktionen bei Einreichungen</h2>
              <p class="text-gray-600">
                Hier können Sie Aktionen konfigurieren, die ausgeführt werden,
                wenn ein Formular eingereicht wird.
              </p>
              <div class="relative flex flex-col flex-1 h-full gap-4">
                <div
                  class="flex flex-col gap-4"
                  v-if="editStore.form.form_settings.onSubmitAction?.length > 0"
                >
                  <div
                    v-for="(action, index) in editStore.form.form_settings
                      .onSubmitAction"
                    :key="index"
                    :class="
                      selectedActionIdx == index
                        ? 'border border-primary-300 bg-primary-50'
                        : 'bg-white'
                    "
                    class="rounded-lg shadow hover:shadow-lg transition-shadow duration-200 cursor-pointer"
                  >
                    <div v-if="action.type == 'mail'" class="flex g-2 justify-between items-center p-4 cursor-pointer" @click="editAction(index)">
                      <div class="flex self-start justify-center items-center w-6 h-6">
                        <Icon
                          icon="heroicons:envelope"
                          class="text-gray-400 w-4 h-4"
                        />
                      </div>
                      <div class="flex-1">
                        <h3 class="text-lg font-semibold">E-Mail senden</h3>
                        <p v-if="action.email_field && action.subject" class="text-gray-600">
                          E-Mail an {{ action.email_field }} senden mit Betreff
                          "{{ action.subject }}"
                        </p>
                        <p v-else class="text-yellow-600">
                          E-Mail senden (Details fehlen)
                        </p>
                      </div>
                      <Icon
                        icon="heroicons:chevron-right"
                        class="text-gray-400 w-6 h-6"
                      />
                    </div>
                    <div
                      v-else-if="action.type == 'redirect'"
                      class="flex g-2 justify-between items-center p-4 cursor-pointer"
                      @click="editAction(index)"
                    >
                      <div class="flex self-start justify-center items-center w-6 h-6">
                        <Icon
                          icon="heroicons:arrow-right"
                          class="text-gray-400 w-4 h-4"
                        />
                      </div>
                      <div class="flex-1">
                        <h3 class="text-lg font-semibold">{{ action.type }}</h3>
                        <p v-if="action.url" class="text-gray-600">
                          Weiterleitung nach Einreichung zu
                          <span class="font-semibold">{{ action.url }}</span>
                          <span v-if="action.open_in_new_tab"
                            class="text-sm text-gray-500"
                            > (in neuem Tab)</span>
                        </p>
                        <p v-else class="text-yellow-600">
                          Weiterleitung (Details fehlen)
                        </p>
                      </div>
                      <Icon
                        icon="heroicons:chevron-right"
                        class="text-gray-400 w-6 h-6"
                      />
                    </div>
                  </div>
                </div>
                <div
                  v-else
                  class="text-yellow-500 flex items-center justify-center h-full border border-dashed border-gray-300 rounded-lg p-4"
                >
                  <p>
                    Keine Aktionen konfiguriert. Klicken Sie auf "Aktion
                    hinzufügen", um eine neue Aktion zu erstellen.
                  </p>
                </div>
                <Card class="flex flex-col">
                  <template #title>Neue Aktion hinzufügen</template>
                  <template #content>
                    <p class="m-0">
                      Wählen Sie eine Aktion aus, die ausgeführt werden soll,
                      wenn das Formular eingereicht wird.
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
            <div class="flex-1 h-full bg-white p-4 rounded-lg shadow">
              <SubmissionActionEditor
                v-if="selectedActionIdx != undefined"
                :action-index="selectedActionIdx"
              />
              <div
                v-else
                class="text-gray-500 flex items-center justify-center h-full"
              >
                <p>Bitte wählen Sie eine Aktion aus, um sie zu bearbeiten.</p>
              </div>
            </div>
          </div>
        </TabPanel>
      </TabPanels>
    </Tabs>
  </div>
</template>

<script setup lang="ts">
import { useEditorStore } from "@/dashboard/editor.store";
import SubmissionActionEditor from "./SubmissionActionEditor.vue";
import { ref } from "vue";
import type { FormOnSubmitAction } from "@/types";
import { Icon } from "@iconify/vue";

const editStore = useEditorStore();

const selectedActionIdx = ref<number>();

const addAction = (actionType: FormOnSubmitAction["type"]) => {
  let newAction: FormOnSubmitAction;
  if (actionType === "mail") {
    newAction = {
      type: "mail",
      email_field: "",
      subject: "",
      body: "",
    };
  } else if (actionType === "redirect") {
    newAction = {
      type: "redirect",
      url: "",
      open_in_new_tab: false,
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
