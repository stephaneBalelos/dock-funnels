import { createGlobalState } from '@vueuse/core'
import { computed, ref } from 'vue'
import type { Form, FormSubmissionField } from '@/types'

export const useFormSubmissionStateStore = createGlobalState(
    () => {
        const form = ref<Form | null>(null)
        const formSubmissionFields = ref<Record<string, FormSubmissionField>>({})

        const currentStepIndex = ref(0)

        function setFieldValue(field_name: string, value: string | string[] | null) {
            if (form.value) {
                const field = form.value.fields.find(f => f.field_name === field_name)
                if (!field) {
                    console.warn(`Field with name "${field_name}" not found in form.`)
                    return
                }
                const step = form.value.form_steps[field.step_index || 0]
                if (!field || !step) {
                    console.warn(`Field with name "${field_name}" not found in form or step.`)
                    return
                }
                if (field) {
                    formSubmissionFields.value[field.field_name] = {
                        step_title: step.title,
                        field_id: field.id,
                        field_label: field.label || field.label,
                        field_name: field.field_name,
                        value: value,
                        step_id: step.id,
                    }
                }
            }
        }

        const nextStep = () => {
            const nextStepIndex = currentStepIndex.value + 1
            if (form.value && nextStepIndex < form.value.form_steps.length) {
                currentStepIndex.value = nextStepIndex
            }
        }
        const previousStep = () => {
            const previousStepIndex = currentStepIndex.value - 1
            if (previousStepIndex >= 0) {
                currentStepIndex.value = previousStepIndex
            }
        }

        const currentStep = computed(() => {
            if (!form.value) return null
            // Return the current step object
            return form.value.form_steps[currentStepIndex.value] || null
        })

        const fieldsForCurrentStep = computed(() => {
            if (!form.value) return []
            // Filter fields that belong to the current step
            return form.value.fields.filter(field => field.step_index === currentStepIndex.value)
        })



        return {
            form,
            formSubmissionFields,
            currentStepIndex,
            currentStep,
            fieldsForCurrentStep,
            setFieldValue,
            nextStep,
            previousStep,
        }
    })