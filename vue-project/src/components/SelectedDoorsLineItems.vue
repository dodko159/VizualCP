<script setup lang="ts">
import {FormSelectedDoorLineItem} from "../model/primitive/form-builder.js";
import BadgePrice from "../components/BadgePrice.vue";
import {useI18n} from "vue-i18n";
import {SelectedDoorLineItemResponse} from "../model/res/SelectedDoorLineItemResponse.js";
import PriceOfferDoorLineItemImage from "../components/PriceOfferDoorLineItemImage.vue";

const selectedDoorsLineItems = defineModel<FormSelectedDoorLineItem[]>('lineItems', {
  required: true
})

const props = defineProps<{
  selectedDoorsLineItemsResponse?: SelectedDoorLineItemResponse[]
}>()

const {t} = useI18n();

function addLineItem(): void {
  selectedDoorsLineItems.value = [
    ...selectedDoorsLineItems.value,
    {
      isDoorFrameEnabled: false,
      name: '',
      price: 0,
      width: ""
    }
  ]
}

function removeLineItem(idx: number): void {
  selectedDoorsLineItems.value = selectedDoorsLineItems.value.filter((_, i) => i !== idx)
}

function findLineItemByOrderIdx(orderIdx: number) {
  return props.selectedDoorsLineItemsResponse?.at(orderIdx)
}
</script>

<template>
  <div class="col-sm-6 col-lg-4 col-xl-2 g-1" v-for="(it, idx) in selectedDoorsLineItems" :key="idx">
    <div class="row gy-1">
      <div class="col-12">
        <PriceOfferDoorLineItemImage/>
      </div>
      <div class="col-12">
        <input type="text"
               v-model.lazy="it.name"
               class="form-control"
               :placeholder="t('doors.name')"/>
      </div>
      <div class="col-12">
        <select class="form-select" v-model="it.width">
          <option value="">{{ t("doors.doorWidth") }}</option>
          <option value="W60">60</option>
          <option value="W70">70</option>
          <option value="W80">80</option>
          <option value="W90">90</option>
        </select>
      </div>
      <div class="col-12">
        <div class="form-check">
          <input class="form-check-input" type="checkbox"
                 v-model="it.isDoorFrameEnabled">
          <label class="form-check-label" for="checkDefault">{{ t("doors.isDoorFrameEnabled") }}</label>
        </div>
      </div>
      <div class="col-12">
        <div class="input-group">
          <input type="number"
                 v-model.number="it.price"
                 class="form-control"
                 min="0"/>
          <span class="input-group-text">€</span>
        </div>
      </div>
      <div class="col-12">
        <BadgePrice
            class="badge-full-width"
            :price="findLineItemByOrderIdx(idx)?.calculatedPrice"/>
      </div>
      <div class="col-12">
        <button type="button"
                v-on:click="removeLineItem(idx)"
                class="btn btn-outline-secondary w-100">
          <span class="fas fa-trash"></span>
          {{ t("doors.remove") }}
        </button>
      </div>
    </div>
  </div>
  <div class="col-sm-6 col-lg-4 col-xl-2 g-1" v-if="selectedDoorsLineItems.length === 0">
    <div class="col-12">
      <button type="button"
              class="btn btn-outline-success w-100"
              @click="addLineItem">
        <span class="fas fa-plus me-1"/>
        {{ t("components.lineItems.add") }}
      </button>
    </div>
  </div>
  <div class="col-sm-6 col-lg-4 col-xl-2 g-1" v-if="selectedDoorsLineItems.length > 0">
    <div class="col-12">
      <button type="button"
              class="btn btn-outline-success w-100"
              @click="addLineItem"
              style="aspect-ratio: 0.46">
        <span class="fas fa-plus me-1"/>
        {{ t("components.lineItems.add") }}
      </button>
    </div>
  </div>
</template>

<style lang="scss">
.badge-full-width {
  height: $input-height;
  width: 100%;
}
</style>