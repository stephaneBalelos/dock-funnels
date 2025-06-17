<template>
    <div class="">
        <InputText
            :input-id="props.fieldName"
            :name="props.fieldName"
            label="Feldname"
            :description="'Geben Sie den Namen des Feldes ein.'"
            type="text"
            :required="false"
            :placeholder="'Feldname: ' + props.fieldName"
            input-class="w-full p-2 border rounded"
            v-model="state.fieldName"
        />
    </div>
</template>

<script setup lang="ts">
import { reactive } from 'vue';
import InputText from '@/components/inputs/InputText.vue';

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