<template>
  <div class="flex flex-col flex-wrap py-4">
    <label
      class="text-stone-700 text-lg leading-none mb-3"
      :class="{ 'text-red-500': props.field.required }"
    >
      {{ props.field.label }}
      <span v-if="props.field.required" class="text-red-500">*</span>
      <p
        v-if="props.field.description"
        class="text-stone-500 text-sm leading-none mt-1"
      >
        {{ props.field.description }}
      </p>
    </label>

    <input
      :id="props.field.field_name"
      class="bg-white border inline-flex h-[35px] px-2 appearance-none justify-center rounded-lg text-sm leading-none shadow-sm outline-none focus:shadow-[0_0_0_2px_black] selection:color-white selection:bg-blackA9"
      type="text"
      :placeholder="props.field.placeholder"
      v-model="textValue"
      @change="onChange"
    />
    <Message
      v-if="
        submissionStateStore.currentStepErrors.value.find(
          (e) => e.joined_path === props.field.field_name
        )
      "
      severity="error"
      variant="simple"
      size="small"
    >
      {{
        submissionStateStore.currentStepErrors.value.find(
          (e) => e.joined_path === props.field.field_name
        )?.message
      }}
    </Message>
  </div>
</template>

<script setup lang="ts">
import { useFormSubmissionStateStore } from "@/forms/stores/form";
import type { FormFieldText } from "@/types";
import { ref } from "vue";

type Props = {
  field: FormFieldText;
};

const textValue = ref<string | null>(null);

const props = defineProps<Props>();
const submissionStateStore = useFormSubmissionStateStore();

const onChange = () => {
  submissionStateStore.setFieldValue(
    props.field.field_name,
    textValue.value
  );
};

</script>

<style scoped></style>
