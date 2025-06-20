import type { Form, FormFieldCheckboxList, FormFieldDependsOn, FormFieldSelect, FormFieldSubmissionSummary, FormFieldText } from "@/types"
import { createGlobalState } from "@vueuse/core"
import { computed, nextTick, reactive, ref } from "vue"


export const useEditorStore = createGlobalState(() => {
    const form = reactive<Form>({
        id: 0,
        title: '',
        description: '',
        form_steps: [],
        fields: []
    })

    const formSteps = computed(() => {
        return form.form_steps.map((step, index) => ({
            ...step,
            index: index,
            fields: form.fields.filter(field => field.step_index === index)
        }))
    })

    const selectedStepIndex = ref<number | null>(null)
    const setSelectedStepIndex = (index: number | null) => {
        selectedStepIndex.value = index
    }
    const getFieldsByStepIndex = (stepIndex: number) => {
        if (stepIndex < 0 || stepIndex >= form.form_steps.length) {
            return []
        }
        return form.fields.filter(field => field.step_index === stepIndex)
    }

    const selectedFieldName = ref<string | null>(null)
    const setSelectedFieldName = (field_name: string) => {
        const field = form.fields.find(f => f.field_name === field_name)
        if (field) {
            selectedFieldName.value = null // Reset before setting to avoid stale state
            nextTick(() => {
                selectedFieldName.value = field.field_name
            })
        } else {
            console.warn('Field not found:', field_name)
            selectedFieldName.value = null
        }
    }

    const initEditor = (initialForm?: Form) => {
        Object.assign(form, initialForm || {
            id: 0,
            title: 'Mein Dock Funnel Formular',
            description: '',
            form_steps: [],
            fields: []
        })
    }

    const addStep = () => {
        const stepCount = form.form_steps.length
        form.form_steps.push({
            title: 'Schritt ' + (stepCount + 1),
            description: '',
        })
    }

    const removeStep = (index: number) => {
        if (index >= 0 && index < form.form_steps.length) {
            // Remove all fields associated with this step
            form.fields = form.fields.filter(field => field.step_index !== index)
            // Update step indices for remaining fields
            form.fields.forEach(field => {
                if (field.step_index > index) {
                    field.step_index -= 1
                }
            })
            // Remove the step itself
            if (selectedStepIndex.value === index) {
                selectedStepIndex.value = null // Reset selected step index if it was the removed step
            }
            // Remove the step from the form steps
            // Perform the removal
            form.form_steps.splice(index, 1)
        }
    }
    const updateStep = async (index: number, step: { title: string; description: string }) => {
        const s = form.form_steps[index]
        if (!s) {
            console.warn('Step not found for index:', index)
            return
        } else {
            form.form_steps[index].title = step.title
            form.form_steps[index].description = step.description
        }
    }
    const moveStep = (fromIndex: number, toIndex: number) => {
        if (fromIndex < 0 || fromIndex >= form.form_steps.length || toIndex < 0 || toIndex >= form.form_steps.length) {
            return
        }
        const step = form.form_steps[fromIndex]
        form.form_steps.splice(fromIndex, 1)
        form.form_steps.splice(toIndex, 0, step)
    }

    const addField = (stepIndex: number, type: string) => {
        const step = form.form_steps[stepIndex]
        if (!step) {
            console.warn('Step not found for index:', stepIndex)
            return null
        }
        const newFieldId = form.fields.length + 1

        switch (type) {
            case 'text':
                const textField: FormFieldText = {
                    field_name: 'text_field_' + (newFieldId),
                    type: 'text',
                    label: 'Text Field',
                    placeholder: 'Enter text',
                    required: false,
                    step_index: stepIndex,
                }
                form.fields.push(textField)
                return textField.field_name
            case 'select':
                const formFieldSelect: FormFieldSelect = {
                    field_name: 'select_field_' + (newFieldId),
                    type: 'select',
                    label: 'Select Field',
                    options: [],
                    required: false,
                    step_index: stepIndex,
                }
                form.fields.push(formFieldSelect)
                return formFieldSelect.field_name
            case 'checkboxList':
                const checkboxListField: FormFieldCheckboxList = {
                    field_name: 'checkbox_list_field_' + (newFieldId),
                    type: 'checkboxList',
                    label: 'Checkbox List Field',
                    options: [],
                    required: false,
                    step_index: stepIndex,
                }
                form.fields.push(checkboxListField)
                return checkboxListField.field_name
            case 'submissionSummary':
                const submissionSummaryField: FormFieldSubmissionSummary = {
                    field_name: 'submission_summary_field_' + (newFieldId),
                    type: 'submissionSummary',
                    label: 'Zusammenfassung',
                    required: false,
                    show_full_summary: true,
                    step_index: stepIndex,
                }
                form.fields.push(submissionSummaryField)
                return submissionSummaryField.field_name
            default:
                console.warn('Unsupported field type:', type)
                return null
        }
    }

    const updateField = (fieldName: string, fieldData: Partial<FormFieldText | FormFieldSelect | FormFieldCheckboxList | FormFieldSubmissionSummary>) => {
        const field = form.fields.find(f => f.field_name === fieldName)
        if (!field) {
            console.warn('Field not found:', fieldName)
            return
        }

        // Todo Update all fields that depend on this field
        // end todo
        Object.assign(field, fieldData)

        setSelectedFieldName(field.field_name)

        console.log(form.fields)
    }

    const removeField = (field_name: string) => {
        const fieldIndex = form.fields.findIndex(f => f.field_name === field_name)
        if (fieldIndex === -1) {
            console.warn('Field not found:', field_name)
            return
        }
        // TODO: Check if the field is a dependency for other fields
        // Remove the field from the form
        form.fields.splice(fieldIndex, 1)
        // Reset selected field name if it was the deleted field
        if (selectedFieldName.value === field_name) {
            selectedFieldName.value = null
        }
        console.log(`Field ${field_name} deleted successfully`)
    }

    const addFieldDependency = (field_name: string, depends_on: FormFieldDependsOn) => {
        const field = form.fields.find(f => f.field_name === field_name)
        if (!field) {
            throw new Error(`Field with name ${field_name} not found`)
        }

        // CHeck if the dependency is already in the field
        if (!field.depends_on) {
            field.depends_on = []
        }
        if (!Array.isArray(field.depends_on)) {
            throw new Error(`Field ${field_name} depends_on should be an array`)
        }
        const existingDep = field.depends_on.find(dep => dep.field_name === depends_on.field_name && dep.value === depends_on.value)
        if (existingDep) {
            throw new Error(`Dependency for field ${field_name} with value ${depends_on.value} already exists`)
        }
        // Validate the depends_on structure
        if (!depends_on || !depends_on.field_name || !depends_on.value) {
            throw new Error(`Invalid depends_on structure for field ${field_name}`)
        }
        // Find the referenced field
        const referencedField = form.fields.find(f => f.field_name === depends_on.field_name)
        if (!referencedField) {
            throw new Error(`Referenced field ${depends_on.field_name} not found`)
        }
        // Ensure the referenced field is of type select or checkboxList
        if (referencedField.type !== 'select' && referencedField.type !== 'checkboxList') {
            throw new Error(`Field ${depends_on.field_name} must be of type select or checkboxList`)
        }
        // check the value is valid for the referenced field
        if (referencedField.type === 'select' && !referencedField.options.some(option => option.value === depends_on.value)) {
            throw new Error(`Value ${depends_on.value} is not a valid option for field ${depends_on.field_name}`)
        }
        if (referencedField.type === 'checkboxList' && !referencedField.options.some(option => option.value === depends_on.value)) {
            throw new Error(`Value ${depends_on.value} is not a valid option for field ${depends_on.field_name}`)
        }

        field.depends_on.push(depends_on)
        updateField(field.field_name, { depends_on: field.depends_on })
        console.log(`Added dependency to field ${field_name}:`, depends_on)
    }

    const removeFieldDependency = (field_name: string, dep_index: number) => {
        const field = form.fields.find(f => f.field_name === field_name)
        if (!field) {
            console.warn('Field not found:', field_name)
            return
        }
        if (!field.depends_on || !Array.isArray(field.depends_on)) {
            console.warn('No dependencies found for field:', field_name)
            return
        }
        if (dep_index < 0 || dep_index >= field.depends_on.length) {
            console.warn('Invalid dependency index:', dep_index)
            return
        }
        field.depends_on.splice(dep_index, 1)
        updateField(field.field_name, { depends_on: field.depends_on })
    }



    return {
        form,
        initEditor,
        formSteps,
        addStep,
        removeStep,
        updateStep,
        moveStep,
        selectedStepIndex,
        setSelectedStepIndex,
        getFieldsByStepIndex,
        selectedFieldName,
        setSelectedFieldName,
        addField,
        updateField,
        removeField,
        addFieldDependency,
        removeFieldDependency
    }
})