import { createGlobalState } from '@vueuse/core'
import { computed, ref, watch } from 'vue'
import type { Form, FormSubmissionField } from '@/types'
import z from 'zod'

type FormFieldError = {
    joined_path: string; // Name of the field with the error
    message: string; // Error message
}

export const useFormSubmissionStateStore = createGlobalState(
    () => {
        const form = ref<Form | null>(null)
        const formSubmissionFields = ref<Record<string, FormSubmissionField>>({})

        const currentStepIndex = ref(0)

        const currentStepErrors = ref<FormFieldError[]>([])

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
                        field_label: field.label,
                        field_name: field.field_name,
                        value: value,
                        step_index: field.step_index
                    }
                }
            }
        }

        const nextStep = () => {
            const nextStepIndex = currentStepIndex.value + 1
            // Validate all fields in the current step before proceeding
            if (!validateCurrentStep()) {
                console.warn(`Validation failed for step ${currentStepIndex.value}. Cannot proceed to next step.`)
                return
            }
            if (form.value && nextStepIndex < form.value.form_steps.length) {
                currentStepIndex.value = nextStepIndex
                currentStepErrors.value = [] // Clear errors when moving to the next step
            }
        }
        const previousStep = () => {
            const previousStepIndex = currentStepIndex.value - 1
            if (previousStepIndex >= 0) {
                currentStepIndex.value = previousStepIndex
                currentStepErrors.value = [] // Clear errors when moving to the previous step
            }
        }

        const validateCurrentStep = () => {
            if (!form.value) return true // No form, nothing to validate
            const currentFields = form.value.fields.filter(field => field.step_index === currentStepIndex.value)

            // Create validation schema based on current step fields
            const fieldsSchemas = []
            for (const field of currentFields) {
                if (!field.required) continue // Skip non-required fields
                let fieldSchema;
                switch (field.type) {
                    case 'text':
                        fieldSchema = z.string().min(1, `${field.label} ist erforderlich`)
                        if (field.input_type === 'email') {
                            fieldSchema = fieldSchema.email(`${field.label} muss eine gültige E-Mail-Adresse sein`)
                        } else if (field.input_type === 'number') {
                            fieldSchema = fieldSchema.transform(value => {
                                const num = Number(value)
                                if (isNaN(num)) throw new Error(`${field.label} muss eine gültige Zahl sein`)
                                return num
                            })
                        } else if (field.input_type === 'tel') {
                            fieldSchema = fieldSchema.regex(/^\+?[0-9\s-]+$/, `${field.label} muss eine gültige Telefonnummer sein`)
                        } else if (field.input_type === 'url') {
                            fieldSchema = fieldSchema.url(`${field.label} muss eine gültige URL sein`)
                        }
                        break
                    case 'textarea':
                        fieldSchema = z.string().min(1, `${field.label} ist erforderlich`)
                        break
                    case 'select':
                        const selectValues = field.options ? field.options.map(option => option.value) : []
                        fieldSchema = z.enum([selectValues[0], ...selectValues])
                        break
                    case 'checkboxList':
                        const checkboxValues = field.options ? field.options.map(option => option.value) : []
                        fieldSchema = z.array(z.enum([checkboxValues[0], ...checkboxValues]))
                        if (field.min) {
                            fieldSchema = fieldSchema.min(field.min, `${field.label} muss mindestens ${field.min} ausgewählt sein`)
                        }
                        if (field.max) {
                            fieldSchema = fieldSchema.max(field.max, `${field.label} darf höchstens ${field.max} ausgewählt sein`)
                        }
                        break
                    default:
                        console.warn(`Unsupported field type}`)
                        continue // Skip unsupported field types
                }
                fieldsSchemas.push({
                    field_name: field.field_name,
                    schema: fieldSchema
                })
            }
            // Combine all field schemas into a single schema
            const stepSchema = z.object(
                Object.fromEntries(fieldsSchemas.map(({ field_name, schema }) => [field_name, schema]))
            )
            // Validate the current step fields
            const stepData = Object.fromEntries(
                currentFields.map(field => [
                    field.field_name,
                    formSubmissionFields.value[field.field_name]?.value || null
                ])
            )
            const result = stepSchema.safeParse(stepData)
            if (!result.success) {
                // Handle validation errors
                console.log(result.error)
                currentStepErrors.value = result.error.errors.map(err => ({
                    joined_path: err.path.join('.'),
                    message: err.message
                }))
                return false
            }
            // Clear errors if validation passed
            currentStepErrors.value = []
            return true // Validation passed
        }



        const fieldsForCurrentStep = computed(() => {
            if (!form.value) return []
            // Filter fields that belong to the current step
            const f = form.value.fields.filter(field => field.step_index === currentStepIndex.value)
            // filter out fields that are not visible based on their dependencies
            return f.filter(field => {
                if (!field.depends_on) return true // No dependencies, always visible
                return field.depends_on.every(dependency => {
                    const dependentField = formSubmissionFields.value[dependency.field_name]
                    if (!dependentField) return false // Dependency field not set, hide this field
                    if (Array.isArray(dependentField.value)) {
                        return dependentField.value.includes(dependency.value) // Check if value is in array
                    }
                    return dependentField.value === dependency.value // Check for single value match
                })
            })
        })

        // watch changes in form, reset formSubmissionFields and currentStepIndex
        watch(form, (newForm) => {
            if (newForm) {
                formSubmissionFields.value = {}
                currentStepIndex.value = 0
                // Populate formSubmissionFields with default values from the form
                newForm.fields.forEach(field => {
                    formSubmissionFields.value[field.field_name] = {
                        step_title: newForm.form_steps[field.step_index || 0].title,
                        field_label: field.label || field.label,
                        field_name: field.field_name,
                        value: field.default_value ?? null,
                        step_index: field.step_index || 0
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
            currentStepErrors
        }
    })