<script setup lang="ts">
import {ValidationMessage} from "../model/validation-message.js";
import {useI18n} from "vue-i18n";

const props = defineProps<{
  validations: ValidationMessage[],
}>()

const {t} = useI18n();
</script>

<template>
  <div v-for="v of props.validations">
    {{ t('validations.' + v.key) }}
  </div>
</template>

<style scoped>

</style>