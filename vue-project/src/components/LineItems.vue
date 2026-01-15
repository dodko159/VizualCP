<script setup lang="ts">
import {FormLineItem} from "../model/primitive/form-builder.js";
import {LineItemResponse} from "../model/res/LineItemResponse.js";
import BadgePrice from "../components/BadgePrice.vue";
import {useI18n} from "vue-i18n";

/**
 * v-model:line-items
 */
const lineItems = defineModel<FormLineItem[]>('lineItems', {
  required: true
})

/**
 * regular props (not part of v-model)
 */
const props = defineProps<{
  lineItemsResponse?: LineItemResponse[]
}>()

const {t} = useI18n();

function addLineItem(): void {
  lineItems.value = [
    ...lineItems.value,
    {
      count: 0,
      name: '',
      price: 0
    }
  ]
}

function removeLineItem(idx: number): void {
  lineItems.value = lineItems.value.filter((_, i) => i !== idx)
}

function findLineItemByOrderIdx(orderIdx: number) {
  return props.lineItemsResponse?.at(orderIdx)
}
</script>

<template>
  <div
      class="row mb-1"
      v-for="(it, index) in lineItems"
      :key="index"
  >
    <div class="col-sm-2 col-xl-1">
      <button
          type="button"
          class="btn btn-outline-secondary h-100"
          @click="removeLineItem(index)"
      >
        <span class="fas fa-trash"/>
      </button>
    </div>

    <div class="col-sm-10 col-xl-5">
      <input
          v-model="it.name"
          class="form-control"
          :placeholder="t('components.lineItems.namePlaceholder')"
      />
    </div>

    <div class="col-sm-4 col-xl-2">
      <div class="input-group">
        <input
            type="number"
            v-model.number="it.price"
            class="form-control"
            min="0"
        />
        <span class="input-group-text">€</span>
      </div>
    </div>

    <div class="col-sm-4 col-xl-2">
      <div class="input-group">
        <input
            type="number"
            v-model.number="it.count"
            class="form-control"
            min="0"
        />
        <span class="input-group-text">ks</span>
      </div>
    </div>

    <div class="col-sm-4 col-xl-2">
      <BadgePrice
          class="badge-full-width"
          :price="findLineItemByOrderIdx(index)?.calculatedPrice"
      />
    </div>
  </div>

  <button
      type="button"
      class="btn btn-outline-success"
      @click="addLineItem">
    <span class="fas fa-plus me-1"/>
    {{ t("components.lineItems.add") }}
  </button>
</template>

<style>
</style>