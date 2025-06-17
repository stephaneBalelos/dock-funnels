<template>
    <div class="">
        {{ state }}
    </div>
</template>

<script setup lang="ts">
import { reactive } from 'vue';

type Props = {
    fieldName: string;
}

const props = defineProps<Props>();


const state = reactive({
    fieldName: props.fieldName,
});
 
</script>

<style scoped>

</style>