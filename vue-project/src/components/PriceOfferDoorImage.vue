<script setup lang="ts">
import {computed} from "vue";

const props = defineProps<{
  category: string,
  material: string,
  type: string
}>()

const doorFrameImgSrc = computed(() =>
    typeof window !== 'undefined' ? window.location.origin + '/images/zarubna.png' : ''
)
</script>

<template>
  <div class="container-door-image">
    <img :src="'/images/materials/' + props.material + '.png'"
         alt="DoorResponse material"
         class="door-layer layer-material">
    <img
        :src="'/images/doors/' + props.category + '/' + props.type + '.png'"
        alt="DoorResponse"
        class="door-layer layer-door">
    <img :src="doorFrameImgSrc" alt="DoorResponse frame" class="door-layer layer-frame">
  </div>
</template>

<style scoped>
.container-door-image {
  position: relative;
  width: 100%;
  aspect-ratio: 0.46;
}

.door-layer {
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  object-fit: contain;
}

.layer-door {
  z-index: 2;
}

.layer-frame {
  z-index: 3;
  pointer-events: none;
}

.layer-material {
  z-index: 1;
}
</style>