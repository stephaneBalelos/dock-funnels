<template>
  <DockFunnelForm v-if="form" :form="form" />
</template>

<script setup lang="ts">
import { onMounted, ref } from "vue";
import type { Form } from "../types/index.ts";
import DockFunnelForm from "@/components/forms/DockFunnelForm.vue";


const form = ref<Form | null>({
  id: 1,
  title: "Sample Form",
  description: "This is a sample form description.",
  form_steps: [{
    id: 1,
    title: "Step 1",
    description: "This is the first step of the form.",
    order: 1,
    fields: [{
      id: 1,
      step_id: 1,
      name: "first_name",
      type: "text",
      label: "First Name",
      placeholder: "Enter your first name",
      required: true
    }, {
      id: 2,
      step_id: 1,
      name: "last_name",
      type: "text",
      label: "Last Name",
      placeholder: "Enter your last name",
      required: true
    }, {
      id: 3,
      step_id: 1,
      name: "message",
      type: "textarea",
      label: "Email",
      placeholder: "Enter your email address",
      required: true
    }]
  },
  {
    id: 2,
    title: "Step 2",
    description: "This is the second step of the form.",
    order: 2,
    fields: [{
      id: 4,
      step_id: 2,
      name: "phone",
      type: "text",
      label: "Phone Number",
      placeholder: "Enter your phone number",
      required: false
    }, {
      id: 5,
      step_id: 2,
      name: "address",
      type: "text",
      label: "Address",
      placeholder: "Enter your address",
      required: false
    }]
  }]
});

onMounted(() => {
  // This is a good place to initialize any global state or perform side effects
  console.log("Form mounted");
  console.log(window.DockFunnelsData);
});
</script>

<style scoped></style>
