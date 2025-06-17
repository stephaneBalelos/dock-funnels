<template>
  <div class="relative">
    <label :for="inputId" class="block text-md font-medium text-gray-700">
      {{ label }}
      <span v-if="required" class="text-red-500">*</span>
      <br />
    </label>
    <p v-if="description" class="text-sm text-gray-500 mt-1">
      {{ description }}
    </p>
    <input
      :id="inputId"
      ref="input"
      :name="name"
      :type="type"
      :required="required"
      :placeholder="placeholder"
      :disabled="disabled"
      :class="inputClasses"
      v-bind="{ value: modelValue }"
      @input="onInput"
      @blur="onBlur"
      @change="onChange"
    />
  </div>
</template>

<script setup lang="ts">
import { ref } from "vue";

const modelValue = defineModel<string>({
  type: [String],
  default: "",
});

defineProps({
  inputId: {
    type: String,
    required: true,
  },
  name: {
    type: String,
    default: "",
  },
  label: {
    type: String,
    default: "",
  },
  description: {
    type: String,
    default: "",
  },
  type: {
    type: String,
    default: "text",
  },
  required: {
    type: Boolean,
    default: false,
  },
  placeholder: {
    type: String,
    default: "",
  },
  disabled: {
    type: Boolean,
    default: false,
  },
});

const $emits = defineEmits(["update:modelValue", "input", "blur", "change"]);

const input = ref<HTMLInputElement | null>(null);

const onInput = (event: Event) => {
  const target = event.target as HTMLInputElement;
  modelValue.value = target.value;
  $emits("update:modelValue", modelValue.value);
};
const onBlur = () => {
  // Handle blur event if needed
  $emits("blur", modelValue.value);
};
const onChange = () => {
  // Handle change event if needed
  $emits("change", modelValue.value);
};

const inputClasses =
  ref(`relative block w-full disabled:cursor-not-allowed disabled:opacity-75 focus:outline-none border-0
           rounded-md
           placeholder-gray-400
           text-sm
           gap-x-2
           px-3 py-2
           shadow-sm bg-white text-gray-900 ring-1 ring-inset ring-gray-300 focus:ring-2 focus:ring-primary-500`);
</script>

<style scoped></style>
