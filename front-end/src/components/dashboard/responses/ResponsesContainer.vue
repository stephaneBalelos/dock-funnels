<template>
    <div class="grid grid-cols-6 gap-4 h-full">
        <div class="col-span-2 border-r">
            <div class="responses-list flex flex-col h-full">
                <div class="responses-header">
                    <h2 class="text-lg font-semibold p-2">Form Responses</h2>
                </div>
                <div class="relative responses-list-content flex-1 overflow-y-auto">
                    <ul class="absolute inset-0 list-none p-0 m-0">
                        <li v-for="res, index in responses" :key="index" :class="'p-2 py-4 m-0 border-t hover:bg-primary-100 cursor-pointer' + (selectedResponseId === res.id ? ' bg-primary-100' : '')" @click="selectedResponseId = res.id">
                            Anfrage ID: {{ res.id }} <br> Eingereicht am: {{ res.submittedAt.toLocaleDateString("de") }}
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="col-span-4">
            <div class="responses-details">
                <h3 class="text-lg font-semibold py-2">Response Details</h3>
                <div class="responses-details-content">
                    <p v-if="!selectedResponse">Select a response to view details.</p>
                    <div v-else>
                        <h4 class="text-md font-semibold">Details for Anfrage ID: {{ selectedResponse.id }}</h4>
                        <ResponseContent :response="selectedResponse.response" />
                    </div>
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
    console.log(props.formState);
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