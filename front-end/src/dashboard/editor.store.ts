import type { Form, FormFieldCheckboxList, FormFieldSelect, FormFieldText } from "@/types"
import { createGlobalState } from "@vueuse/core"
import { computed, reactive, ref } from "vue"


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
            selectedFieldName.value = field.field_name
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
            default:
                console.warn('Unsupported field type:', type)
                return null
        }
    }

    const updateField = (fieldName: string, fieldData: Partial<FormFieldText | FormFieldSelect | FormFieldCheckboxList>) => {
        const field = form.fields.find(f => f.field_name === fieldName)
        if (!field) {
            console.warn('Field not found:', fieldName)
            return
        }
        Object.assign(field, fieldData)

        setSelectedFieldName(field.field_name)

        console.log(form.fields)
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
        updateField
    }
})