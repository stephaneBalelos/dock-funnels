<script setup lang="ts">
import { onMounted } from 'vue';

const ajaxUrl = window.DockFunnelsAdmin?.ajaxUrl || '/wp-admin/admin-ajax.php';

onMounted(() => {
  // This is a good place to initialize any global state or perform side effects
  console.log('App mounted');
  console.log(window.DockFunnelsAdmin);
});

async function createTestForm() {
  try {
    const response = await fetch(`${ajaxUrl}?action=dock_funnel_ajax_create_form`, {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
      },
      body: JSON.stringify({
        data: {
          name: 'Test Form',
          description: 'This is a test form created via AJAX.',
          fields: [
            {
              type: 'text',
              label: 'Name',
              required: true,
            },
            {
              type: 'email',
              label: 'Email',
              required: true,
            },
          ],
        },
        nonce: window.DockFunnelsAdmin.nonce || '',
      }),
    });
    if (!response.ok) {
      throw new Error('Network response was not ok');
    }
    const result = await response.json();
    if (result.success) {
      console.log('Test form created successfully:', result.data);
    } else {
      console.error('Error creating test form:', result.data);
    }
  }
  catch (error) {
    console.error('Error creating test form:', error);
  }
}
</script>

<template>
  <div class="flex justify-center items-center flex-col">
    <h1 class="text-3xl font-bold mb-4">Welcome to Dock Funnels</h1>
    <p class="mb-4">This is a simple dashboard for managing your funnels.</p>
    <button @click="createTestForm" class="bg-blue-500 text-white px-4 py-2 rounded hover:bg-blue-600">
      Create Test Form
    </button>
  </div>
</template>

<style scoped>

</style>
