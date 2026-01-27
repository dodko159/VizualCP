<script setup lang="ts">
import {FormDoor} from "../model/primitive/form-builder.js";
import BadgePrice from "../components/BadgePrice.vue";
import {useI18n} from "vue-i18n";
import {SelectedDoorResponse} from "../model/res/SelectedDoorResponse.js";
import PriceOfferDoorImage from "../components/PriceOfferDoorImage.vue";

const selectedDoors = defineModel<Record<string, FormDoor>>('selectedDoors', {
  required: true
})

defineProps<{
  baseUrl: string | null | undefined,
  selectedDoorsResponse?: Record<string, SelectedDoorResponse>
}>()

const {t} = useI18n();

const handleDoorRemove = (key: string) => {
  delete selectedDoors.value[key];
}

</script>

<template>
  <div class="col-sm-6 col-lg-4 col-xl-2 g-1" v-for="(_, key) in selectedDoors" :key="key">
    <div class="row gy-1" v-if="selectedDoorsResponse && selectedDoors[key]">
      <div class="col-12">
        <PriceOfferDoorImage :base-url="baseUrl"
                             :door="selectedDoorsResponse[key]"/>
        <div class="text-align-center">{{ selectedDoorsResponse[key].type?.toUpperCase() }}</div>
      </div>
      <div class="col-12">
        <select class="form-select" v-model="selectedDoors[key].doorWidth">
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
                 v-model="selectedDoors[key].isDoorFrameEnabled">
          <label class="form-check-label" for="checkDefault">{{ t("doors.isDoorFrameEnabled") }}</label>
        </div>
      </div>
      <div class="col-12">
        <BadgePrice
            class="badge-full-width"
            :price="selectedDoorsResponse[key]?.calculatedPrice"/>
      </div>
      <div class="col-12">
        <button type="button"
                v-on:click="handleDoorRemove(key)"
                class="btn btn-outline-secondary w-100">
          <span class="fas fa-trash"></span>
          {{ t("doors.remove") }}
        </button>
      </div>
    </div>
  </div>
</template>

<style lang="scss">
.badge-full-width {
  height: $input-height;
  width: 100%;
}
</style>