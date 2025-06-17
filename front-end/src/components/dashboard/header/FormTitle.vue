<template>
        <EditableRoot
          v-slot="{ isEditing }"
          v-model="editorStore.form.title"
          placeholder="Mein Formular Titel"
          class="flex items-center gap-4"
          auto-resize
        >
          <EditableArea class="text-stone-700">
            <EditablePreview class="text-stone-900 font-semibold text-lg" />
            <EditableInput
              class="w-full text-stone-900 placeholder:text-stone-700 font-semibold text-lg"
            />
          </EditableArea>
          <EditableEditTrigger
            v-if="!isEditing"
            class="w-max inline-flex items-center justify-center rounded-lg font-medium text-sm px-[15px] leading-[35px] h-[35px] bg-white text-green11 shadow-sm border outline-none hover:bg-stone-50 focus:shadow-[0_0_0_2px] focus:shadow-black"
          >
          <Icon icon="heroicons:pencil-square" class="w-4 h-4" />
          </EditableEditTrigger>
          <div v-else class="flex gap-2">
            <EditableSubmitTrigger
              class="inline-flex items-center justify-center rounded-lg font-medium text-sm px-[15px] leading-[35px] h-[35px] bg-white text-green11 shadow-sm border outline-none hover:bg-stone-50 focus:shadow-[0_0_0_2px] focus:shadow-black"
            >
              <Icon icon="heroicons:check" class="w-4 h-4" />
            </EditableSubmitTrigger>
            <EditableCancelTrigger
              class="inline-flex items-center justify-center rounded-lg font-medium text-sm px-[15px] leading-[35px] h-[35px] bg-red-700 text-white shadow-sm border outline-none hover:bg-red10 focus:shadow-[0_0_0_2px] focus:shadow-black"
            >
              <Icon icon="heroicons:x-mark" class="w-4 h-4" />
            </EditableCancelTrigger>
          </div>
        </EditableRoot>
</template>

<script setup lang="ts">
import { useEditorStore } from '@/dashboard/editor.store';
import { EditableArea, EditableCancelTrigger, EditableEditTrigger, EditableInput, EditablePreview, EditableRoot, EditableSubmitTrigger } from 'reka-ui'
import { Icon } from '@iconify/vue'


const editorStore = useEditorStore();
</script>

<style scoped>

</style>