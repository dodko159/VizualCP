<script setup lang="ts">
import {onBeforeUnmount} from "vue"
import {useAlerts} from "../composables/alert-composables.js"

const {alerts, removeAlert} = useAlerts()

const listeners = new Map<HTMLElement, EventListener>()

function registerToast(el: HTMLElement | null, id: number) {
  if (!el) return

  const handler = () => removeAlert(id)
  el.addEventListener("hidden.bs.toast", handler)
  listeners.set(el, handler)
}

onBeforeUnmount(() => {
  listeners.forEach((handler, el) => {
    el.removeEventListener("hidden.bs.toast", handler)
  })
})
</script>

<template>
  <Teleport to="body">
    <div class="toast-container position-fixed top-0 end-0 p-3">
      <div
          v-for="alert in alerts"
          :key="alert.id"
          class="toast align-items-center bg-white show"
          role="alert"
          aria-live="assertive"
          aria-atomic="true"
          :ref="el => registerToast(el as HTMLElement, alert.id)">
        <div class="d-flex">
          <div class="toast-body">
            {{ alert.message }}
          </div>
          <button type="button"
                  class="btn-close me-2 m-auto"
                  data-bs-dismiss="toast"
                  aria-label="Close"/>
        </div>
      </div>
    </div>
  </Teleport>
</template>