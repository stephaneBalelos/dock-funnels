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
    <InputGroup>
      <InputGroupAddon v-if="props.field.input_type === 'email'">
        <Icon icon="heroicons:envelope" />
      </InputGroupAddon>
      <InputGroupAddon v-if="props.field.input_type === 'tel'">
        <Icon icon="heroicons:phone" />
      </InputGroupAddon>
      <InputGroupAddon v-if="props.field.input_type === 'number'">
        <Icon icon="heroicons:hashtag" />
      </InputGroupAddon>
      <InputText
        :id="props.field.field_name"
        v-model="textValue"
        @change="onChange"
        :type="props.field.input_type || 'text'"
        :placeholder="props.field.placeholder "
      />
    </InputGroup>
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
import { onMounted, ref } from "vue";
import { Icon } from "@iconify/vue";

type Props = {
  field: FormFieldText;
};

const textValue = ref<string | null>(null);

const props = defineProps<Props>();
const submissionStateStore = useFormSubmissionStateStore();

onMounted(() => {
  const initialValue = submissionStateStore.formSubmissionFields.value[
    props.field.field_name
  ]?.value as string | undefined;
  if (initialValue) {
    textValue.value = initialValue;
  } else {
    textValue.value = props.field.default_value || null;
  }
});

const onChange = () => {
  submissionStateStore.setFieldValue(props.field.field_name, textValue.value);
};
</script>

<style scoped></style>
