import { createGlobalState } from '@vueuse/core'
import { computed, ref, watch } from 'vue'
import type { Form, FormSubmissionField } from '@/types'
import z from 'zod'
import { submitFormResponse } from '@/api/wpAjaxApi'

type FormFieldError = {
    joined_path: string; // Name of the field with the error
    message: string; // Error message
}

type FormSubmissionSettings = {
    endpoint: string; // API endpoint for form submission
    nonce: string; // Nonce for security
    formId: number; // ID of the form being submitted
}

export const useFormSubmissionStateStore = createGlobalState(
    () => {
        const submissionSettings = ref<FormSubmissionSettings | null>(null) // Settings for form submission
        const form = ref<Form | null>(null)
        const formSubmissionFields = ref<Map<string, FormSubmissionField>>(new Map()) // Fields for form submission, keyed by field_name
        const currentStepIndex = ref(0)
        const isFormSubmitted = ref(false) // Flag to indicate if the form has been submitted

        const currentStepErrors = ref<FormFieldError[]>([])

        function setFieldValue(field_name: string, value: string | string[] | null) {
            if (form.value) {
                const field = form.value.form_fields.find(f => f.field_name === field_name)
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
                    formSubmissionFields.value.set(field_name, {
                        field_name: field.field_name,
                        value: value,
                        step_index: field.step_index
                    })
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
            // If validation passed, update formSubmissionFields with the current step fields
            formSubmissionFields.value.forEach((field, field_name) => {
                const currentFieldsNames = fieldsForCurrentStep.value.map(f => f.field_name)
                if (!currentFieldsNames.includes(field_name) && field.step_index === currentStepIndex.value) {
                    // If the field is not in the current step, remove it from submission fields
                    formSubmissionFields.value.delete(field_name)
                }
            })
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
            const currentFields = fieldsForCurrentStep.value
            if (currentFields.length === 0) return true // No fields in current step,

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
                        console.log('select field', field)
                        const selectValues = field.options ? field.options.map(option => option.value) : []
                        const values: z.EnumLike = Object.fromEntries(selectValues.map(value => [value, value]))
                        fieldSchema = z.nativeEnum(values, { message: `${field.label} ist erforderlich` })
                        break
                    case 'checkboxList':
                        const checkboxValues = field.options ? field.options.map(option => option.value) : []
                        fieldSchema = z.array(z.enum([checkboxValues[0], ...checkboxValues]))
                        if (checkboxValues.length === 1 && field.min === 1) {
                            fieldSchema = fieldSchema.nonempty(`${field.label} muss ausgewählt werden`)
                        } else {
                            if (field.min && field.min > 0) {
                                fieldSchema = fieldSchema.min(field.min, `${field.label}: Sie müssen mindestens ${field.min} auswählen`)

                                if (field.max && field.max >= field.min) {
                                    fieldSchema = fieldSchema.max(field.max, `${field.label}: Sie können maximal ${field.max} auswählen`)
                                }
                            }
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
                    formSubmissionFields.value.get(field.field_name)?.value || (field.type === 'checkboxList' ? [] : '')
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
            // If validation passed, filter out the the fields that are not visible based on their dependencies
            // Clear errors if validation passed
            currentStepErrors.value = []

            return true // Validation passed
        }



        const fieldsForCurrentStep = computed(() => {
            if (!form.value) return []
            // Filter fields that belong to the current step
            const f = form.value.form_fields.filter(field => field.step_index === currentStepIndex.value)
            // filter out fields that are not visible based on their dependencies
            return f.filter(field => {
                if (!field.depends_on) return true // No dependencies, always visible
                return field.depends_on.every(dependency => {
                    const dependentField = formSubmissionFields.value.get(dependency.field_name)
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
                formSubmissionFields.value.clear() // Clear previous form submission fields
                currentStepIndex.value = 0
                isFormSubmitted.value = false // Reset submission state

                // Init First Step Fields with default values
                fieldsForCurrentStep.value.forEach(field => {
                    if (field.default_value !== undefined) {
                        setFieldValue(field.field_name, field.default_value)
                    } else {
                        setFieldValue(field.field_name, null) // Set to null if no default value
                    }
                })
            }
        })

        const saveFormSubmission = async () => {
            if (!form.value) return false // No form to save
            // Validate the current step before saving
            if (!validateCurrentStep()) {
                console.warn(`Validation failed for step ${currentStepIndex.value}. Cannot save form submission.`)
                return false
            }
            // Prepare the submission data
            const fields = Object.fromEntries(formSubmissionFields.value.entries())
            console.log('Form submission fields:', fields)
            const submissionData: Record<string, any> = {
                form_id: form.value.id,
                fields: fields,
            }
            console.log('Saving form submission:', submissionData)
            try {
                if (!submissionSettings.value) {
                    console.warn('Submission settings are not set. Cannot save form submission.')
                    return false
                }
                if (!submissionSettings.value.endpoint || !submissionSettings.value.nonce || !submissionSettings.value.formId) {
                    console.warn('Submission settings are incomplete. Cannot save form submission.')
                    return false
                }
                // Call the API to submit the form response
                const { endpoint, nonce, formId } = submissionSettings.value
                submissionData.form_id = formId // Ensure form_id is set
                const res = await submitFormResponse(endpoint, nonce, submissionData)
                if (res.success) {
                    console.log('Form submission saved successfully:', res)
                    isFormSubmitted.value = true // Set form as submitted
                    
                    return res.data// Submission successful
                }
                else {
                    console.error('Form submission failed:', res)
                    return false // Submission failed
                }
            } catch (error) {
                console.error('Error saving form submission:', error)
                return false // Error occurred during submission
            }
        }



        return {
            submissionSettings,
            form,
            formSubmissionFields,
            currentStepIndex,
            isFormSubmitted,
            fieldsForCurrentStep,
            setFieldValue,
            nextStep,
            previousStep,
            currentStepErrors,
            saveFormSubmission
        }
    })