<template>
    <div class="grid grid-cols-6 h-full">
        <div class="col-span-2 border-r">
            <div class="responses-list flex flex-col h-full">
                <div class="responses-header p-2">
                    <h2 class="text-lg font-semibold">{{ formState.title }}</h2>
                    <p class="text-sm text-surface-500">Wählen Sie eine Antwort aus, um die Details anzuzeigen.</p>
                </div>
                <div class="relative responses-list-content flex-1 overflow-y-auto">
                    <ul v-if="responses.length > 0" class="absolute inset-0 list-none p-0 m-0">
                        <li v-for="res, index in responses" :key="index" :class="'p-2 py-4 m-0 border-t hover:bg-surface-100 cursor-pointer' + (selectedResponseId === res.id ? ' bg-surface-100' : '')" @click="selectedResponseId = res.id">
                            Anfrage ID: {{ res.id }} <br> Eingereicht am: {{ res.submittedAt.toLocaleDateString("de") }}
                        </li>
                    </ul>
                    <div v-else class="flex flex-col items-center h-full">
                        <EmptyState />
                        <p class="text-lg text-surface-500">Bisher keine Antworten vorhanden.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-span-4" v-if="selectedResponseId && selectedResponse">
            <div class="responses-details">
                <div class="response-details-header p-2 border-b">
                    <h3 class="text-lg font-semibold">
                        Details zu Anfrage ID: {{ selectedResponseId }}
                    </h3>
                    <p class="text-sm text-surface-500">Hier sind die Details zu Ihrer Anfrage</p>
                </div>
                <div class="responses-details-content p-2">
                    <ResponseContent :form-steps="props.formState.form_steps" :response="selectedResponse.response" />
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { type FormState } from '@/types';
import { computed, defineProps, ref } from 'vue';
import { getFormResponses } from '@/api/wpAjaxApi';
import { onMounted, inject } from 'vue';
import ResponseContent from './ResponseContent.vue';
import EmptyState from '../UI/Illustrations/EmptyState.vue';

type Props = {
    formState: FormState;
}

type FormResponse = {
    id: string;
    response: Record<string, any>;
    submittedAt: Date
}

const props = defineProps<Props>();

const formId = inject('formId') as number | undefined;
const endpoint = inject('ajaxUrl') as string | undefined;
const nonce = inject('nonce') as string | undefined;

const responses = ref<FormResponse[]>([]);

const selectedResponseId = ref<string | null>(null);

const selectedResponse = computed(() => {
    return responses.value.find(res => res.id === selectedResponseId.value) || null;
});

onMounted(async () => {
    if (!formId || !endpoint || !nonce) {
        console.error("Form ID, API endpoint, or nonce not provided");
        return;
    }
    // Fetch form responses using the provided formId
    try {
        const res = await getFormResponses(endpoint, nonce, formId);
        responses.value = res.data.map(((r: { response: string; id: string, submitted_at: string }) => {
            return {
                id: r.id,
                response: JSON.parse(r.response), // Assuming response is a JSON string
                submittedAt: new Date(r.submitted_at)
            }
        }))
    } catch (error) {
        console.error("Error fetching form responses:", error);
    }
});

</script>

<style scoped>

</style>