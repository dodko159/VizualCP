<script setup lang="ts">
import BadgePrice from "./BadgePrice.vue";
import {useI18n} from "vue-i18n";

const props = defineProps<{
  id: string,
  sectionPrice?: number
}>()

const {t} = useI18n();
</script>

<template>
  <div class="accordion-item">
    <div class="accordion-header"
         :id="`accordion-header-${props.id}`">
      <button
          type="button"
          class="accordion-button d-flex"
          data-bs-toggle="collapse"
          :data-bs-target="`#accordion-collapse-${props.id}`"
          aria-expanded="true"
          :aria-controls="`accordion-collapse-${props.id}`">
        <span class="flex-grow-1">{{ t('accordionHeaders.' + props.id) }}</span>
        <BadgePrice class="accordion-badge"
                    v-if="props.sectionPrice !== undefined"
                    :price="props.sectionPrice"/>
      </button>
    </div>

    <div
        class="accordion-collapse collapse show"
        :id="`accordion-collapse-${props.id}`"
        :aria-labelledby="`accordion-header-${props.id}`">
      <div class="accordion-body">
        <slot></slot>
      </div>
    </div>
  </div>
</template>

<style lang="scss" scoped>
@import "bootstrap/scss/functions";
@import "bootstrap/scss/variables";
@import "bootstrap/scss/mixins";

.accordion-badge {
  height: $input-height;
  margin-left: 1em;
  order: 1;
  flex: 0 0 107px;
}

.accordion button {
  color: #555;
  text-transform: uppercase;
  font-weight: normal;
}

.accordion button:hover {
  order: 2;
  flex: 0 0 auto;
}
</style>