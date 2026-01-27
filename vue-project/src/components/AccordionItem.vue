<script setup lang="ts">
import BadgePrice from "./BadgePrice.vue";
import {useI18n} from "vue-i18n";

defineProps<{
  id: string,
  isOpenByDefault: boolean,
  sectionPrice?: number
}>()

const {t} = useI18n();
</script>

<template>
  <div class="accordion-item">
    <div class="accordion-header"
         :id="`accordion-header-${id}`">
      <button
          type="button"
          :class="{'collapsed': !isOpenByDefault}"
          class="accordion-button d-flex"
          data-bs-toggle="collapse"
          :data-bs-target="`#accordion-collapse-${id}`"
          aria-expanded="true"
          :aria-controls="`accordion-collapse-${id}`">
        <span class="flex-grow-1">{{ t('accordionHeaders.' + id) }}</span>
        <BadgePrice class="accordion-badge"
                    v-if="sectionPrice !== undefined"
                    :price="sectionPrice"/>
      </button>
    </div>

    <div
        :class="{'show': isOpenByDefault}"
        class="accordion-collapse collapse"
        :id="`accordion-collapse-${id}`"
        :aria-labelledby="`accordion-header-${id}`">
      <div class="accordion-body p-0">
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
  flex: 0 0 105px;
  min-width: 105px;
}

.accordion button {
  text-transform: uppercase;
  font-weight: bold;
}

.accordion button:hover {
  order: 2;
  flex: 0 0 auto;
}

@media (max-width: 1000px) {
  .accordion-button {
    flex-wrap: wrap;
  }

  .accordion-badge {
    flex: 1 1 100%;
    margin-left: 0;
    margin-top: 0.5em;
  }
}
</style>