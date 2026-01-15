<script setup lang="ts">
import {onBeforeUnmount} from "vue"
import {useAlerts} from "../composables/alert-composables.js"

const {alerts, removeAlert} = useAlerts()

const listeners = new Map<HTMLElement, EventListener>()

function registerAlert(el: HTMLElement | null, id: number) {
  if (!el) return
  const handler = () => removeAlert(id)
  el.addEventListener("closed.bs.alert", handler)
  listeners.set(el, handler)
}

onBeforeUnmount(() => {
  listeners.forEach((handler, el) => {
    el.removeEventListener("closed.bs.alert", handler)
  })
})
</script>

<template>
  <Teleport to="body">
    <div class="alert-stack">
      <div
          v-for="alert in alerts"
          :key="alert.id"
          class="alert alert-dismissible fade show"
          :class="`alert-${alert.type}`"
          role="alert"
          :ref="el => registerAlert(el as HTMLElement, alert.id)">
        {{ alert.message }}
        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
            aria-label="Close"
        ></button>
      </div>
    </div>
  </Teleport>
</template>

<style scoped>
.alert-stack {
  position: fixed;
  top: 1rem;
  right: 1rem;
  width: 320px;
  z-index: 1055;
}
</style>