import { createForm, deleteForm, updateForm } from "@/api/wpAjaxApi"
import type { FormFieldCheckboxList, FormFieldDependsOn, FormFieldSelect, FormFieldSubmissionSummary, FormFieldText, FormOnSubmitAction, FormState } from "@/types"
import { getThemePreset } from "@/utils"
import { updatePreset } from "@primeuix/themes"
import { createGlobalState } from "@vueuse/core"
import { useToast } from "primevue"
import { computed, nextTick, reactive, ref } from "vue"

type EditorState = {
    editorMode: 'edit' | 'preview' | 'submission-actions', // 'edit',
    isLoading: boolean,
    isSaving: boolean,
    error: string | null
}

export const useEditorStore = createGlobalState(() => {

    const toast = useToast()

    const apiSettings = ref({
        endpoint: '',
        nonce: '',
        editFormId: 0
    })
    const editorState = ref<EditorState>({
        editorMode: 'edit', // 'edit', 'preview', flow
        isLoading: false,
        isSaving: false,
        error: null as string | null
    })
    const form = reactive<FormState>({
        title: '',
        description: '',
        form_steps: [],
        form_fields: [],
        form_settings: {
            design_settings: {
                colors: {
                    primary: '#0073aa',
                    surface: '#64748b',
                },
                header: {
                    show: true,
                    align: 'center'
                },
                steps: {
                    hide_step_header: false,
                    text_align: 'text-left', // Default text alignment for steps
                    items_align: 'items-start', // Default alignment for step items
                    step_transition: 'slide' // Default transition effect for steps
                },
                footer: {
                    show_progress_bar: true
                }
            },
            smtp_settings: {
                enabled: false,
                host: '',
                port: 587,
                username: '',
                password: '',
                encryption: 'tls',
                from_name: '',
                from_email: '',
                reply_to: ''
            },
            notifications_settings: {
                emails: '',
                subject: 'Neue Formularübermittlung',
                body: 'Hallo Admin, \n\nEs wurde ein neues Formular übermittelt. Hier sind die Details:\n\n{submission_details}\n\nVielen Dank!'
            },
            onSubmitAction: [],
            email_settings: {
                enabled: false,
                send_to_admin: false,
                send_to_user: false,
                user_email_field: '',
                subject: '',
                body: ''
            }
        }
    })

    const formSteps = computed(() => {
        return form.form_steps.map((step, index) => ({
            ...step,
            index: index,
            fields: form.form_fields.filter(field => field.step_index === index)
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
        return form.form_fields.filter(field => field.step_index === stepIndex)
    }

    const selectedFieldName = ref<string | null>(null)
    const setSelectedFieldName = (field_name: string) => {
        const field = form.form_fields.find(f => f.field_name === field_name)
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

    const initEditor = (initialForm?: FormState) => {
        Object.assign(form, initialForm || {
            id: 0,
            title: 'Mein Dock Funnel Formular',
            description: '',
            form_steps: [],
            fields: []
        })
        if (form.form_steps.length === 0) {
            addStep() // Ensure at least one step exists
        }
        if (form.form_steps.length > 0) {
            setSelectedStepIndex(0) // Select the first step by default
        }
        updateEditorThemePreset() // Initialize the theme preset based on the form settings
    }

    const addStep = () => {
        const stepCount = form.form_steps.length
        form.form_steps.push({
            title: 'Schritt ' + (stepCount + 1),
            description: '',
        })
        setSelectedStepIndex(stepCount)
    }

    const removeStep = (index: number) => {
        if (index >= 0 && index < form.form_steps.length) {
            // Remove all fields associated with this step
            form.form_fields = form.form_fields.filter(field => field.step_index !== index)
            // Update step indices for remaining fields
            form.form_fields.forEach(field => {
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
        const newFieldId = form.form_fields.length + 1

        switch (type) {
            case 'text':
                const textField: FormFieldText = {
                    field_name: 'text_field_' + (newFieldId),
                    type: 'text',
                    label: 'Text Field',
                    placeholder: 'Enter text',
                    required: false,
                    step_index: stepIndex,
                    depends_on: [],
                }
                form.form_fields.push(textField)
                return textField.field_name
            case 'select':
                const formFieldSelect: FormFieldSelect = {
                    field_name: 'select_field_' + (newFieldId),
                    type: 'select',
                    label: 'Select Field',
                    options: [],
                    required: false,
                    step_index: stepIndex,
                    depends_on: [],
                }
                form.form_fields.push(formFieldSelect)
                return formFieldSelect.field_name
            case 'checkboxList':
                const checkboxListField: FormFieldCheckboxList = {
                    field_name: 'checkbox_list_field_' + (newFieldId),
                    type: 'checkboxList',
                    label: 'Checkbox List Field',
                    options: [],
                    required: false,
                    step_index: stepIndex,
                    depends_on: [],
                }
                form.form_fields.push(checkboxListField)
                return checkboxListField.field_name
            case 'submissionSummary':
                const submissionSummaryField: FormFieldSubmissionSummary = {
                    field_name: 'submission_summary_field_' + (newFieldId),
                    type: 'submissionSummary',
                    label: 'Zusammenfassung',
                    required: false,
                    show_full_summary: true,
                    step_index: stepIndex,
                    depends_on: [],
                }
                form.form_fields.push(submissionSummaryField)
                return submissionSummaryField.field_name
            default:
                console.warn('Unsupported field type:', type)
                return null
        }
    }

    const updateField = (fieldName: string, fieldData: Partial<FormFieldText | FormFieldSelect | FormFieldCheckboxList | FormFieldSubmissionSummary>) => {
        const field = form.form_fields.find(f => f.field_name === fieldName)
        if (!field) {
            console.warn('Field not found:', fieldName)
            return
        }

        // Todo Update all fields that depend on this field
        // end todo
        Object.assign(field, fieldData)

        setSelectedFieldName(field.field_name)

        console.log(form.form_fields)
    }

    const removeField = (field_name: string) => {
        const fieldIndex = form.form_fields.findIndex(f => f.field_name === field_name)
        if (fieldIndex === -1) {
            console.warn('Field not found:', field_name)
            return
        }
        // TODO: Check if the field is a dependency for other fields
        // Remove the field from the form
        form.form_fields.splice(fieldIndex, 1)
        // Reset selected field name if it was the deleted field
        if (selectedFieldName.value === field_name) {
            selectedFieldName.value = null
        }
        console.log(`Field ${field_name} deleted successfully`)
    }

    const addFieldDependency = (field_name: string, depends_on: FormFieldDependsOn) => {
        const field = form.form_fields.find(f => f.field_name === field_name)
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
        const referencedField = form.form_fields.find(f => f.field_name === depends_on.field_name)
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

    const addOptionDependency = (field_name: string, option_value: string, depends_on: FormFieldDependsOn) => {
        const field = form.form_fields.find(f => f.field_name === field_name)
        if (!field) {
            throw new Error(`Field with name ${field_name} not found`)
        }
        // Validate the field type
        if (field.type !== 'select' && field.type !== 'checkboxList') {
            throw new Error(`Field ${field_name} must be of type select or checkboxList to add option dependencies`)
        }
        // Find the option in the field's options
        const option = field.options.find(opt => opt.value === option_value)
        if (!option) {
            throw new Error(`Option with value ${option_value} not found in field ${field_name}`)
        }
        // Validate the depends_on structure
        if (!depends_on || !depends_on.field_name || !depends_on.value) {
            throw new Error(`Invalid depends_on structure for field ${field_name}`)
        }
        // Find the referenced field
        const referencedField = form.form_fields.find(f => f.field_name === depends_on.field_name)
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
        // Ensure the option has a depends_on array
        if (!option.depends_on || !Array.isArray(option.depends_on)) {
            option.depends_on = []
        }
        // Check if the dependency already exists
        const existingDep = option.depends_on.find(dep => dep.field_name === depends_on.field_name && dep.value === depends_on.value)
        if (existingDep) {
            throw new Error(`Dependency for option ${option_value} in field ${field_name} with value ${depends_on.value} already exists`)
        }
        // Add the dependency to the option
        option.depends_on.push(depends_on)
        // Update the field with the new options
        updateField(field.field_name, { options: field.options })
        console.log(`Added dependency to option ${option_value} in field ${field_name}:`, depends_on)
    }

    const removeOptionDependency = (field_name: string, option_value: string, dep_index: number) => {
        const field = form.form_fields.find(f => f.field_name === field_name)
        if (!field) {
            console.warn('Field not found:', field_name)
            return
        }
        if (field.type !== 'select' && field.type !== 'checkboxList') {
            console.warn(`Field ${field_name} must be of type select or checkboxList to remove option dependencies`)
            return
        }
        const option = field.options.find(opt => opt.value === option_value)
        if (!option) {
            console.warn(`Option with value ${option_value} not found in field ${field_name}`)
            return
        }
        if (!option.depends_on || !Array.isArray(option.depends_on)) {
            console.warn(`No dependencies found for option ${option_value} in field ${field_name}`)
            return
        }
        if (dep_index < 0 || dep_index >= option.depends_on.length) {
            console.warn('Invalid dependency index:', dep_index)
            return
        }
        option.depends_on.splice(dep_index, 1)
        // Update the field with the new options
        updateField(field.field_name, { options: field.options })
        console.log(`Removed dependency from option ${option_value} in field ${field_name} at index ${dep_index}`)
    }

    const removeFieldDependency = (field_name: string, dep_index: number) => {
        const field = form.form_fields.find(f => f.field_name === field_name)
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

    /**
     * Saves the onSubmit action at the specified index.
     * push action to the form's onSubmitAction array when index is not found
     * @param {number} index - The index of the action to save.
     * @param {FormOnSubmitAction} action - The action to save.
     */
    const saveSubmissionAction = (index: number, action: FormOnSubmitAction) => {
        if (index < 0 || index >= form.form_settings.onSubmitAction.length) {
            // If index is out of bounds, push the action to the array
            form.form_settings.onSubmitAction.push(action)
        } else {
            // Otherwise, update the existing action at the specified index
            form.form_settings.onSubmitAction[index] = action
        }
    }

    const removeSubmissionAction = (index: number) => {
        if (index < 0 || index >= form.form_settings.onSubmitAction.length) {
            console.warn('Invalid index for submission action:', index)
            return
        }
        // Remove the action at the specified index
        form.form_settings.onSubmitAction.splice(index, 1)
    }


    /**
     * Saves the current form state to the server.
     * @returns {Promise<any>} The response from the server.
     */
    const saveFormState = async (): Promise<any> => {
        const formState = form
        const endpoint = apiSettings.value.endpoint
        const nonce = apiSettings.value.nonce
        const editFormId = apiSettings.value.editFormId
        try {
            editorState.value.isSaving = true; // Set saving state
            if (!endpoint || !nonce) {
                console.log(formState)
                throw new Error("Endpoint or nonce not provided");
            }
            if (!editFormId) {
                const response = await createForm(endpoint, nonce, JSON.stringify(formState));
                if (!response.success) {
                    console.error('Error creating form:', response.data);
                    throw new Error(`Error creating form: ${response.data}`);
                }
                toast.add({
                    severity: 'success',
                    summary: 'Formular erstellt',
                    detail: 'Das Formular wurde erfolgreich erstellt.',
                    life: 3000,
                });
                await nextTick(); // Ensure the DOM updates before redirecting
                // Redirect to the edit page with the new form ID
                window.location.href = `/wp-admin/admin.php?page=dock-funnels&form_id=${response.data.form_id}`;
            } else {
                const response = await updateForm(endpoint, nonce, editFormId, JSON.stringify(formState));
                if (!response.success) {
                    console.error('Error updating form:', response.data);
                    throw new Error(`Error updating form: ${response.data}`);
                }
                toast.add({
                    severity: 'success',
                    summary: 'Formular gespeichert',
                    detail: 'Das Formular wurde erfolgreich gespeichert.',
                    life: 3000
                });
            }
        } catch (error) {
            toast.add({
                severity: 'error',
                summary: 'Fehler beim Speichern des Formulars',
                detail: error instanceof Error ? error.message : 'Unbekannter Fehler',
                life: 5000
            });
            console.error('Error saving form state:', error);
            throw error;
        } finally {
            editorState.value.isSaving = false; // Reset saving state
        }
    }

    const formDelete = async () => {
        const endpoint = apiSettings.value.endpoint
        const nonce = apiSettings.value.nonce
        const editFormId = apiSettings.value.editFormId
        if (!endpoint || !nonce) {
            console.error("API endpoint or nonce not provided");
            return;
        }

        if (!editFormId) {
            console.error("No form ID provided for deletion");
            return;
        }

        return await deleteForm(endpoint, nonce, editFormId);
    };

    const updateEditorThemePreset = () => {
        const designSettings = form.form_settings.design_settings
        if (!designSettings || !designSettings.colors) {
            console.warn('Design settings or colors not defined, using default preset');
            return
        }
        // Ensure colors are defined
        const preset = getThemePreset(designSettings.colors.primary, designSettings.colors.surface)
        updatePreset(preset)
    }


    return {
        apiSettings,
        editorState,
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
        removeFieldDependency,
        addOptionDependency,
        removeOptionDependency,
        saveSubmissionAction,
        removeSubmissionAction,
        saveFormState,
        formDelete,
        updateEditorThemePreset
    }
})