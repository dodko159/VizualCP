<script setup lang="ts">
import {ref, onMounted, createApp, onBeforeUnmount, computed} from 'vue'
import {Modal, Popover} from 'bootstrap'
import PopOverContent from './PopOverContent.vue'
import {useI18n} from "vue-i18n";
import {HintInterface} from "../model/interface/HintInterface.js";

const props = defineProps<{
  hintObj: HintInterface
}>()

const popoverBtn = ref<HTMLElement | null>(null)
const modalEl = ref<HTMLElement | null>(null)
const videoEl = ref<HTMLVideoElement | null>(null)

const showIframe = ref<Boolean>(false)

let modalInstance: Modal | null = null

const {t} = useI18n();

const getYoutubeUrl = computed(() => {
  return `https://www.youtube.com/embed/${props.hintObj.youtubeVideoCode}?autoplay=1&mute=1`;
})

function openModal() {
  modalInstance?.show()
}

onBeforeUnmount(() => {
  modalInstance?.dispose()
})

onMounted(() => {
  // Popover
  if (popoverBtn.value) {
    new Popover(popoverBtn.value, {
      trigger: 'click',
      fallbackPlacements: ['top'],
      placement: 'bottom',
      html: true,
      content: () => {
        const container = document.createElement('div')
        const popoverApp = createApp(PopOverContent, {
          hint: props.hintObj.hint,
          imgSrc: props.hintObj.imgSrc,
          videoSrc: props.hintObj.videoSrc,
        })
        popoverApp.mount(container)

        popoverBtn.value?.addEventListener(
            'hidden.bs.popover',
            () => {
              popoverApp.unmount()
              container.remove()
            },
            {once: true}
        )

        return container
      },
    })
  }

  // Modal
  if (modalEl.value) {
    modalInstance = new Modal(modalEl.value, {
      backdrop: true,
      keyboard: true,
    })

    modalEl.value.addEventListener('show.bs.modal', () => {
      if (props.hintObj.youtubeVideoCode) {
        showIframe.value = true
      }
    })

    modalEl.value.addEventListener('shown.bs.modal', () => {
      if (videoEl.value) {
        videoEl.value.play()
      }
    })

    modalEl.value.addEventListener('hidden.bs.modal', () => {
      if (videoEl.value) {
        videoEl.value.pause()
      }

      if (props.hintObj.youtubeVideoCode) {
        showIframe.value = false
      }
    })
  }
})
</script>

<template>
  <a
      v-if="hintObj.imgSrc"
      ref="popoverBtn"
      tabindex="0"
      role="button">
    <i class="fas fa-question-circle color-dark-red"></i>
  </a>

  <a
      v-if="hintObj.videoSrc || hintObj.youtubeVideoCode"
      @click="openModal"
      role="button">
    <i class="fas fa-question-circle color-dark-red"></i>
  </a>

  <Teleport to="body">
    <div ref="modalEl" class="modal fade" tabindex="-1" aria-hidden="true">
      <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title">{{ hintObj.header }}</h5>
            <button
                type="button"
                class="btn-close"
                data-bs-dismiss="modal"
            ></button>
          </div>

          <div class="modal-body">
            <div class="ratio ratio-16x9"
                 v-if="hintObj.youtubeVideoCode">
              <iframe
                  v-if="showIframe"
                  :src="getYoutubeUrl"
                  loading="lazy"
                  allowfullscreen>
              </iframe>
            </div>
            <div class="ratio ratio-16x9"
                 v-if="hintObj.videoSrc">
              <!--              <video-->
              <!--                  preload="metadata"-->
              <!--                  ref="videoEl"-->
              <!--                  controls-->
              <!--                  playsinline>-->
              <!--                <source :src="'http://localhost:8080/video.php?filename=' + hint.videoSrc" type="video/mp4"/>-->
              <!--              </video>-->
            </div>
          </div>
          <div class="modal-footer">
            {{ hintObj.hint }}
            <button
                type="button"
                class="btn btn-secondary"
                data-bs-dismiss="modal">
              {{ t('components.hint.modal.close') }}
            </button>
          </div>
        </div>
      </div>
    </div>
  </Teleport>
</template>

<style scoped>
</style>