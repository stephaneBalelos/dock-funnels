<template>
  <div class="h-full flex flex-col">
    <div class="responses-header p-2 flex flex-col border-b">
      <div class="flex items-center mb-2">
        <div class="flex flex-col">
          <h2 class="text-lg font-semibold">{{ formState.title }}</h2>
          <p class="text-sm text-surface-500">
            Wählen Sie eine Anfrage aus, um die Details anzuzeigen.
          </p>
        </div>
        <Button
          v-if="adminUrl"
          class="ml-4"
          @click="navigateToEditorPage"
          aria-label="Edit Form"
          severity="secondary"
          size="small"
        >
          <Icon icon="heroicons:pencil-square" />
        </Button>
        <SelectButton
          class="ml-auto"
          v-model="selectedView"
          option-value="value"
          option-label="name"
          :options="options"
          size="small"
        />
      </div>
      <Message
        v-if="!formState.should_save_responses"
        class="text-sm text-surface-500"
        size="small"
      >
        Dieses Formular speichert keine Anfragen.
      </Message>
    </div>
    <div v-if="selectedView === 1" class="grid grid-cols-6 flex-1">
      <div class="col-span-2 border-r">
        <div class="responses-list flex flex-col h-full">
          <div class="relative responses-list-content flex-1 overflow-y-auto">
            <ul
              v-if="responses.length > 0"
              class="absolute inset-0 list-none p-0 m-0"
            >
              <li
                v-for="(res, index) in responses"
                :key="index"
                :class="
                  'p-2 py-4 m-0 border-b hover:bg-surface-100 cursor-pointer' +
                  (selectedResponseId === res.id ? ' bg-surface-100' : '')
                "
                @click="selectedResponseId = res.id"
              >
                Anfrage ID: {{ res.id }} <br />
                Eingereicht am: {{ res.submittedAt.toLocaleDateString("de") }}
              </li>
            </ul>
            <div v-else class="flex flex-col items-center h-full">
              <EmptyState />
              <p class="text-lg text-surface-500">
                Bisher keine Antworten vorhanden.
              </p>
            </div>
          </div>
        </div>
      </div>
      <div class="col-span-4">
        <div
          v-if="selectedResponseId && selectedResponse"
          class="responses-details flex flex-col h-full"
        >
          <div
            class="response-details-header p-2 border-b flex justify-between items-center"
          >
            <div class="flex flex-col">
              <h3 class="text-lg font-semibold">
                Details zu Anfrage ID: {{ selectedResponseId }}
              </h3>
              <p class="text-sm text-surface-500">
                Hier sind die Details zu Ihrer Anfrage
              </p>
            </div>
            <Button
              severity="danger"
              @click="deleteResponse(selectedResponseId)"
            >
              Anfrage löschen
            </Button>
          </div>
          <div
            class="responses-details-content relative flex-1 overflow-y-auto p-2"
          >
            <div class="absolute inset-0 p-4">
              <ResponseContent
                :form-steps="props.formState.form_steps"
                :response="selectedResponse.response"
              />
            </div>
          </div>
        </div>
      </div>
    </div>
    <div v-else class="email-logs flex-1 p-4">
      <h3 class="text-lg font-semibold mb-2">E-Mail Logs</h3>
      <p class="text-sm text-surface-500 mb-2">
        Hier können Sie die E-Mail Logs für dieses Formular einsehen. Logs die
        hier angezeigt werden, stammen aus den E-Mail Benachrichtigungen, die an
        Admins oder Kunden über Wordpress gesendet wurden. Ist aber keine
        Garantie, dass die E-Mails auch tatsächlich zugestellt wurden, da
        technische Probleme oder Spamfilter bei dem Empfänger die Zustellung
        verhindern können.
      </p>
      <div class="email-logs-content relative h-full">
        <div class="absolute inset-0 py-4">
          <ResponseLogsContent />
        </div>
      </div>
    </div>
  </div>
</template>

<script setup lang="ts">
import { type FormState } from "@/types";
import { computed, defineProps, ref } from "vue";
import { deleteFormResponse, getFormResponses } from "@/api/wpAjaxApi";
import { onMounted, inject } from "vue";
import ResponseContent from "./ResponseContent.vue";
import EmptyState from "../UI/Illustrations/EmptyState.vue";
import { Button, useToast } from "primevue";
import { Icon } from "@iconify/vue";

type Props = {
  formState: FormState;
};

type FormResponse = {
  id: number;
  response: Record<string, any>;
  submittedAt: Date;
};

const props = defineProps<Props>();

const formId = inject("formId") as number | undefined;
const endpoint = inject("ajaxUrl") as string | undefined;
const nonce = inject("nonce") as string | undefined;
const adminUrl = inject("adminUrl") as string | undefined;

const responses = ref<FormResponse[]>([]);

const selectedResponseId = ref<number | null>(null);

const toast = useToast();

const selectedResponse = computed(() => {
  return (
    responses.value.find((res) => res.id === selectedResponseId.value) || null
  );
});

const selectedView = ref(1);
const options = ref([
  { name: "Antworten", value: 1 },
  { name: "E-Mail Logs", value: 2 },
]);

onMounted(async () => {
  if (!formId || !endpoint || !nonce) {
    console.error("Form ID, API endpoint, or nonce not provided");
    return;
  }
  // Fetch form responses using the provided formId
  try {
    const res = await getFormResponses(endpoint, nonce, formId);
    responses.value = res.data.map(
      (r: { response: string; id: number; submitted_at: string }) => {
        return {
          id: r.id,
          response: JSON.parse(r.response), // Assuming response is a JSON string
          submittedAt: new Date(r.submitted_at),
        };
      }
    );
  } catch (error) {
    console.error("Error fetching form responses:", error);
  }
});

async function deleteResponse(responseId: number) {
  if (!endpoint || !nonce || !formId) {
    console.error("API endpoint, nonce, or form ID not provided");
    return;
  }
  if (!confirm("Sind Sie sicher, dass Sie diese Anfrage löschen möchten?")) {
    return;
  }
  try {
    const res = await deleteFormResponse(endpoint, nonce, formId, responseId);
    if (res.success) {
      responses.value = responses.value.filter((r) => r.id !== responseId);
      selectedResponseId.value = null;
      toast.add({
        severity: "success",
        summary: "Erfolg",
        detail: "Antwort erfolgreich gelöscht.",
      });
    } else {
      throw new Error(res.data.message || "Failed to delete response");
    }
  } catch (error) {
    console.error("Error deleting response:", error);
    toast.add({
      severity: "error",
      summary: "Fehler",
      detail: "Antwort konnte nicht gelöscht werden.",
    });
  }
}

function navigateToEditorPage() {
  const url = new URL(adminUrl || "", window.location.origin);
  url.searchParams.set("page", "dock-funnels-editor");
  url.searchParams.set("form_id", String(formId));

  window.location.href = url.toString();
}
</script>

<style scoped></style>
