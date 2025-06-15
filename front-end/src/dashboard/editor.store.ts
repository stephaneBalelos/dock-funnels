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

    const initEditor = (initialForm?: Form) => {
        form.value = initialForm || {
            id: 0,
            title: 'djklaskkds',
            description: '',
            form_steps: [],
            fields: []
        }
    }

    return {
        form,
        initEditor
    }
})