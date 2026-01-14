import { reactive } from "vue"

export type AlertType = "success" | "danger" | "warning" | "info";

interface Alert {
    id: number
    type: AlertType
    message: string
}

const MAX_VISIBLE = 2
const ALERT_TIMEOUT = 10000

let idCounter = 0

const state = reactive({
    visible: [] as Alert[],
    queue: [] as Alert[],
})

function showNext() {
    if (state.visible.length >= MAX_VISIBLE) return
    if (state.queue.length === 0) return

    const alert = state.queue.shift()!
    state.visible.push(alert)

    setTimeout(() => removeAlert(alert.id), ALERT_TIMEOUT)
}

function removeAlert(id: number) {
    const index = state.visible.findIndex(a => a.id === id)
    if (index === -1) return

    state.visible.splice(index, 1)
    showNext()
}

function addAlert(message: string, type: AlertType = "danger") {
    const alert: Alert = {
        id: ++idCounter,
        message,
        type,
    }

    state.queue.push(alert)
    showNext()
}

export function useAlerts() {
    return {
        alerts: state.visible,
        addAlert,
        removeAlert,
    }
}