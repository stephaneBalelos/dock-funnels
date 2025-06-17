<script setup lang="ts">
import { ref } from 'vue'
import { Icon } from '@iconify/vue'
import {
  DialogClose,
  DialogContent,
  DialogDescription,
  DialogOverlay,
  DialogPortal,
  DialogRoot,
  DialogTitle,
  DialogTrigger,
} from 'reka-ui'

const open = ref(false)

const $emits = defineEmits<{
  (e: 'add-field', fieldType: string): void
}>()

function close(type: string) {
  $emits('add-field', type)
  open.value = false
}

</script>

<template>
  <DialogRoot v-model:open="open" @update:open="(open) => console.log('Dialog open state:', open)">
    <DialogTrigger
      class="font-semibold inline-flex h-[35px] items-center justify-center rounded-md bg-white px-[15px] leading-none shadow-sm focus:shadow-[0_0_0_2px] focus:shadow-black focus:outline-none border"
    >
      Feld hinzufügen
    </DialogTrigger>
    <DialogPortal>
      <DialogOverlay class="bg-stone-100/50  fixed inset-0 z-30" />
      <DialogContent
        class="data-[state=open]:animate-contentShow fixed top-[50%] left-[50%] max-h-[85vh] w-[90vw] max-w-[450px] translate-x-[-50%] translate-y-[-50%] rounded-[6px] bg-white p-[25px] shadow-[hsl(206_22%_7%_/_35%)_0px_10px_38px_-10px,_hsl(206_22%_7%_/_20%)_0px_10px_20px_-15px] focus:outline-none z-[100]"
      >
        <DialogTitle class="text-mauve12 m-0 text-[17px] font-semibold">
          Feld hinzufügen
        </DialogTitle>
        <DialogDescription class="text-mauve11 mt-[10px] mb-5 text-sm leading-normal">
          Wählen Sie den Feldtyp, den Sie hinzufügen möchten, und geben Sie die erforderlichen Informationen ein.
        </DialogDescription>
        
        <div class="flex flex-col gap-2">
            <button class="font-semibold inline-flex h-[35px] items-center justify-center rounded-md bg-white px-[15px] leading-none shadow-sm focus:shadow-[0_0_0_2px] focus:shadow-black focus:outline-none border"
                @click="close('select')">
                Auswahlfeld
            </button>
            <button class="font-semibold inline-flex h-[35px] items-center justify-center rounded-md bg-white px-[15px] leading-none shadow-sm focus:shadow-[0_0_0_2px] focus:shadow-black focus:outline-none border"
                @click="close('text')">
                Textfeld
            </button>
            <button class="font-semibold inline-flex h-[35px] items-center justify-center rounded-md bg-white px-[15px] leading-none shadow-sm focus:shadow-[0_0_0_2px] focus:shadow-black focus:outline-none border"
                @click="close('checkboxList')">
                Checkbox
            </button>
        </div>
        <DialogClose
          class="text-grass11 hover:bg-green4 focus:shadow-green7 absolute top-[10px] right-[10px] inline-flex h-[25px] w-[25px] appearance-none items-center justify-center rounded-full focus:shadow-[0_0_0_2px] focus:outline-none"
          aria-label="Close"
        >
          <Icon icon="lucide:x" />
        </DialogClose>
      </DialogContent>
    </DialogPortal>
  </DialogRoot>
</template>