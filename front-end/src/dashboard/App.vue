<script setup lang="ts">
import HelloWorld from '../components/HelloWorld.vue'
import { onMounted } from 'vue';

onMounted(() => {
  // This is a good place to initialize any global state or perform side effects
  console.log('App mounted');
  console.log(window.DockFunnelsData);
});
</script>

<template>
  <div class="flex justify-center items-center flex-col">
  </div>
  <HelloWorld msg="Vite + Vue" />
</template>

<style scoped>
.logo {
  height: 6em;
  padding: 1.5em;
  will-change: filter;
  transition: filter 300ms;
}
.logo:hover {
  filter: drop-shadow(0 0 2em #646cffaa);
}
.logo.vue:hover {
  filter: drop-shadow(0 0 2em #42b883aa);
}
</style>
