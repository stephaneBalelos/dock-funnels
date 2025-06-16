import type { Form } from "@/types"
import { createGlobalState } from "@vueuse/core"
import { ref } from "vue"


export const useEditorStore = createGlobalState(() => {
    const form = ref<Form>({
        id: 0,
        title: '',
        description: '',
        form_steps: [],
        fields: []
    })

    const selectedStepIndex = ref<number | null>(null)
    const setSelectedStepIndex = (index: number | null) => {
        selectedStepIndex.value = index
    }

    const getFieldsByStepIndex = (stepIndex: number) => {
        if (stepIndex < 0 || stepIndex >= form.value.form_steps.length) {
            return []
        }
        return form.value.fields.filter(field => field.step_index === stepIndex)
    }

    const initEditor = (initialForm?: Form) => {
        form.value = initialForm || {
            id: 0,
            title: 'djklaskkds',
            description: '',
            form_steps: [],
            fields: []
        }
    }

    const addStep = () => {
        const stepCount = form.value.form_steps.length
        form.value.form_steps.push({
            id: stepCount + 1,
            title: 'Step ' + (stepCount + 1),
            description: '',
        })
    }

    const removeStep = (index: number) => {
        if (index >= 0 && index < form.value.form_steps.length) {
            form.value.form_steps.splice(index, 1)
        }
    }
    const updateStep = (index: number, step: { title: string; description: string }) => {
        if (index >= 0 && index < form.value.form_steps.length) {
            form.value.form_steps[index].title = step.title
            form.value.form_steps[index].description = step.description
        }
    }
    const moveStep = (fromIndex: number, toIndex: number) => {
        if (fromIndex < 0 || fromIndex >= form.value.form_steps.length || toIndex < 0 || toIndex >= form.value.form_steps.length) {
            return
        }
        const step = form.value.form_steps[fromIndex]
        form.value.form_steps.splice(fromIndex, 1)
        form.value.form_steps.splice(toIndex, 0, step)
    }

    return {
        form,
        initEditor,
        addStep,
        removeStep,
        updateStep,
        moveStep,
        selectedStepIndex,
        setSelectedStepIndex,
        getFieldsByStepIndex
    }
})