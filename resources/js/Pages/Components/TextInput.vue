<script setup>
const model = defineModel({
    type: null,
    required: true,
});

defineProps({
    name: {
        type: String,
        required: true,
    },
    type: {
        type:String,
        default: "text",
    },
    placeholder: {
        type: String,
    },
    message: String
});
</script> 

<template>
    <label>{{ name }}</label>
    <input :type="type" :placeholder="placeholder" v-model="model" />
    <small class="error" v-if="message">{{ message }}</small>
</template>