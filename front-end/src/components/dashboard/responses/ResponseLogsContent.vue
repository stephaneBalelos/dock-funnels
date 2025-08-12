<template>
  <DataTable :value="emailLogs" tableStyle="min-width: 50rem" :size="'small'">
    <Column field="id" header="ID"></Column>
    <Column field="emails" header="E-Mails">
      <template #body="{ data }">
        <ul>
          <li v-for="email in data.emails" :key="email">{{ email }}</li>
        </ul>
      </template>
    </Column>
    <Column field="is_action">
        <template #header>
          <strong v-tooltip.top="'Unterscheidet zwischen E-Mail Benachrichtigung an Admin, und Formular Action and Kunden'">Ist eine Formular Action?</strong>
        </template>
        <template #body="{ data }">
          {{ data.is_action ? 'Ja' : 'Nein' }}
        </template>
    </Column>
    <Column field="sent_at" header="Gesendet am">
      <template #body="{ data }">
        {{ data.sent_at }}
      </template>
    </Column>
    <template #empty>
        <div>No email logs found.</div>
    </template>
  </DataTable>
</template>

<script setup lang="ts">
import { getEmailLogsByFormId } from "@/api/wpAjaxApi";
import { inject, onMounted, ref } from "vue";

type EmailLog = {
  id: number;
  emails: string[];
  is_action: number;
  sent_at: string;
};

const formId = inject('formId') as number | undefined;
const endpoint = inject('ajaxUrl') as string | undefined;
const nonce = inject('nonce') as string | undefined;

const emailLogs = ref<EmailLog[]>([]);



onMounted(async () => {
    if (!formId || !endpoint || !nonce) {
        console.error("Form ID, API endpoint, or nonce not provided");
        return;
    }
    // Fetch form responses using the provided formId
    try {
        const response = await getEmailLogsByFormId(endpoint, nonce, formId);
        console.log(response);
        emailLogs.value = response.data.map((log: any) => ({
            id: log.id,
            emails: JSON.parse(log.emails), // Assuming emails are stored as JSON
            is_action: parseInt(log.is_action) ? true : false,
            sent_at: new Date(log.sent_at).toLocaleString('de-DE', {
                year: 'numeric',
                month: '2-digit',
                day: '2-digit',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
            }),
        }));
    } catch (error) {
        console.error("Error fetching form data:", error);
    }
});
</script>

<style scoped></style>
