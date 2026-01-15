<script setup lang="ts">
import {useRouter} from 'vue-router'

const router = useRouter()

if (sessionStorage.getItem('offerCompleted') !== 'true') {
  router.replace('/')
} else {
  sessionStorage.removeItem('offerCompleted')
}
</script>

<template>
  <div class="min-vh-100 d-flex justify-content-center align-items-center">
    <div class="text-center">
      <span class="fa fa-check text-success" style="font-size: 5em"></span>
      <h3>Ďakujeme.</h3>
      <h3>Cenovú ponuku sme Vám poslali na e-mailovú adresu.</h3>
      <h3>STOLÁRSTVO - SUČANSKÝ</h3>
    </div>
  </div>

</template>

<style scoped>
</style>