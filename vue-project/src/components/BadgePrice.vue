<script setup lang="ts">
import {formatPrice} from "../model/primitive/formatters.js";

const props = defineProps<{ price: number | undefined }>()
</script>

<template>
    <span class="badge bg-primary align-content-center fs-5">{{
        formatPrice(props.price)
      }}</span>
</template>

<style scoped>
</style>