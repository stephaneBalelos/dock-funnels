import { createGlobalState } from '@vueuse/core'
import { ref } from 'vue'
import type { FormSubmission} from '@/types'

export const useFormSubmissionStateStore = createGlobalState(
    () => {
        const formSubmission = ref<FormSubmission | null>(null)

        function setFieldValue(fieldName: string, value: FormSubmission['fields'][string]['value']) {
            if (formSubmission.value) {
                formSubmission.value.fields[fieldName].value = value
            }
        }
        return {
            formSubmission,
            setFieldValue
        }
    })