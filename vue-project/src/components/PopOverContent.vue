<script setup lang="ts">
const props = defineProps<{
  hint?: string;
  imgSrc?: string | null;
  videoSrc?: string | null;
}>();
</script>

<template>
  <div>
    <img v-if="props.imgSrc" :src="props.imgSrc" class="img-fluid rounded" alt="popover image"/>
    <div v-if="props.hint">{{ props.hint }}</div>
  </div>
</template>

<style scoped>
</style>