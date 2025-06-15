import { createGlobalState } from '@vueuse/core'
import { computed, ref, watch } from 'vue'
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

        const fieldsForCurrentStep = computed(() => {
            if (!form.value) return []
            // Filter fields that belong to the current step
            const f = form.value.fields.filter(field => field.step_index === currentStepIndex.value)
            // filter out fields that are not visible based on their dependencies
            return f.filter(field => {
                if (!field.depends_on) return true // No dependencies, always visible
                const dependentField = form.value?.fields.find(f => f.field_name === field.depends_on?.field_name)
                if (!dependentField) return true // Dependent field not found, show this field
                const dependentValue = formSubmissionFields.value[dependentField.field_name]?.value
                if (Array.isArray(dependentValue)) {
                    return dependentValue.includes(field.depends_on.value as string)
                }
                return dependentValue === field.depends_on.value
            })
        })

        // watch changes in form, reset formSubmissionFields and currentStepIndex
        watch(form, (newForm) => {
            if (newForm) {
                formSubmissionFields.value = {}
                currentStepIndex.value = 0
                // Populate formSubmissionFields with default values from the form
                newForm.fields.forEach(field => {
                    if (field.default_value !== undefined) {
                        formSubmissionFields.value[field.field_name] = {
                            step_title: newForm.form_steps[field.step_index || 0].title,
                            field_id: field.id,
                            field_label: field.label || field.label,
                            field_name: field.field_name,
                            value: field.default_value,
                            step_id: newForm.form_steps[field.step_index || 0].id,
                        }
                    }
                })
            }
        })



        return {
            form,
            formSubmissionFields,
            currentStepIndex,
            fieldsForCurrentStep,
            setFieldValue,
            nextStep,
            previousStep,
        }
    })