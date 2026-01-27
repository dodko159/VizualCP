<script setup lang="ts">
import {computed, onBeforeUnmount, onMounted, Ref, ref, watch, WatchHandle} from 'vue'
import {ApiResponse} from "../model/res/ApiResponse.js";
import {
  constructReactiveFormPriceOffer,
  findPossibleAdditionalChargeById,
  findRosetteById,
  findSpecialAccessoryById,
  findSpecialSurchargeById,
  FormPriceOffer
} from "../model/primitive/form-builder.js";
import {prepareRequest} from "../model/primitive/api-request-builder.js";
import BadgePrice from "../components/BadgePrice.vue";
import AccordionItem from "../components/AccordionItem.vue";
import {formatPrice} from "../model/primitive/formatters.js";
import PriceOfferDoorImage from "../components/PriceOfferDoorImage.vue";
import {getAppConfig, getForm, submitForm, updateForm} from "../model/rest.js";
import {ValidationMessage} from "../model/validation-message.js";
import ValidationMessages from "../components/ValidationMessages.vue";
import Hint from "../components/Hint.vue";
import LineItems from "../components/LineItems.vue";
import {AppConfigResponse} from "../model/res/AppConfigResponse.js";
import {useAlerts} from "../composables/alert-composables.js";
import {useI18n} from "vue-i18n";
import Toasts from "../components/Toasts.vue";
import {useRouter} from "vue-router";
import {HintInterface} from "../model/interface/HintInterface.js";
import SelectedDoorsLineItems from "../components/SelectedDoorsLineItems.vue";
import SelectedDoors from "../components/SelectedDoors.vue";

const router = useRouter()
const {t} = useI18n();
const {addAlert} = useAlerts()

const apiResponse: Ref<ApiResponse | undefined> = ref()
const appConfigResponse: Ref<AppConfigResponse | null> = ref(null)
let formWatchHandle: WatchHandle | undefined = undefined
const cachedForm: Ref<string> = ref('')
const reactiveForm: Ref<FormPriceOffer> = ref<FormPriceOffer>(constructReactiveFormPriceOffer(undefined))
const formValidations: Ref<Record<string, ValidationMessage[]>> = ref({})
const reCaptchaToken: Ref<string | undefined> = ref()

const submitButtonDisabled = computed(() => {
  const isReCaptchaEnabled = appConfigResponse.value?.reCaptchaEnabled ?? false;
  const reCaptchaCond = isReCaptchaEnabled ? !reCaptchaToken : false;
  const validationsCond = Object.keys(formValidations.value).length > 0;

  return reCaptchaCond || validationsCond;
})

function getFieldValidations(id: string): ValidationMessage[] {
  return formValidations.value[id] ?? []
}

async function handleFormSaveAndReRender(form: FormPriceOffer): Promise<void> {
  const req = prepareRequest(form, apiResponse.value)

  if (req) {
    const response = await updateForm(req);
    if (response.ok) {
      formValidations.value = await response.json();
    } else {
      addAlert(t("alerts.FORM_SAVE_ERROR"));
      throw new Error(response.statusText);
    }

    return fetchDataAndReRenderForm();
  } else {
    return Promise.resolve()
  }
}

const fetchDataFromApi = async (): Promise<ApiResponse> => {
  const response = await getForm();
  const json = await response.json()
  if (response.ok)
    return json as ApiResponse;
  else
    throw new Error(json);
}

async function fetchDataAndReRenderForm(): Promise<void> {
  const json = await fetchDataFromApi()
  apiResponse.value = json;
  reactiveForm.value = constructReactiveFormPriceOffer(json);
  cachedForm.value = JSON.stringify(reactiveForm.value);
}

function renderReCaptcha(): void {
  if (window.grecaptcha && appConfigResponse.value?.reCaptchaSiteKey) {
    window.grecaptcha.render('recaptcha', {
      sitekey: appConfigResponse.value?.reCaptchaSiteKey,
      callback: (token: string) => {
        reCaptchaToken.value = token;
      },
      'expired-callback': () => {
        reCaptchaToken.value = undefined;
      }
    });
  } else {
    console.error('grecaptcha not loaded');
  }
}

function debounce<F extends (...args: any[]) => any>(func: F, wait: number) {
  let timeout: ReturnType<typeof setTimeout>;
  return (...args: Parameters<F>) => {
    clearTimeout(timeout);
    timeout = setTimeout(() => func(...args), wait);
  };
}

const debouncedHandleFormSave = debounce((newForm: FormPriceOffer) => {
  if (cachedForm.value !== JSON.stringify(newForm)) {
    handleFormSaveAndReRender(newForm);
  }
}, 250);

onMounted(async () => {
  appConfigResponse.value = await getAppConfig()

  if (appConfigResponse.value?.reCaptchaEnabled) {
    renderReCaptcha();
  }

  await fetchDataAndReRenderForm()

  formWatchHandle = watch(reactiveForm, (newForm) => {
    console.log('vueForm.value');
    console.log(reactiveForm.value);
    console.log('newVal');
    console.log(JSON.stringify(newForm));

    debouncedHandleFormSave(newForm);
  }, {deep: true});
})

onBeforeUnmount(async () => {
  formWatchHandle?.()
})

async function handleFormSubmit(e: Event): Promise<void> {
  e.preventDefault();
  await handleFormSaveAndReRender(reactiveForm.value);

  const validationsEmpty = Object.keys(formValidations.value).length === 0;
  if (validationsEmpty) {
    const recaptchaToken = (document.querySelector('.g-recaptcha-response') as HTMLTextAreaElement)?.value;
    const response = await submitForm(recaptchaToken);

    if (response.ok) {
      // await handleFormSaveAndReRender(constructReactiveFormPriceOffer(undefined));
      sessionStorage.setItem('offerCompleted', 'true')
      await router.push('/price-offer-finished')
    } else {
      addAlert(t("alerts.MAIL_SENDING_ERROR"));
    }
  } else {
    addAlert(t("alerts.FORM_VALIDATIONS_ERROR"));
  }
}

function handleHandleCountChange(e: Event) {
  const target = e.target as HTMLInputElement;
  reactiveForm.value.assemblyPriceHandlesRosettesCount = Number(target.value);
}
</script>

<template>
  <Toasts/>
  <div>
    <form novalidate>
      <div class="accordion mb-2">
        <AccordionItem id="doors"
                       :is-open-by-default="true"
                       :section-price="apiResponse?.priceOffer?.sectionsCalculatedPrice?.doors">
          <ul class="list-group list-group-flush">
            <li class="list-group-item">
              <div class="row mb-1" v-if="apiResponse?.priceOffer?.doors">
                <SelectedDoors v-model:selected-doors="reactiveForm.doors"
                               :base-url="appConfigResponse?.baseUrl"
                               :selected-doors-response="apiResponse?.priceOffer?.doors"/>
              </div>
              <div class="row">
                <SelectedDoorsLineItems v-model:line-items="reactiveForm.selectedDoorsLineItems"
                                        :selected-doors-line-items-response="apiResponse?.priceOffer?.selectedDoorsLineItems"/>
              </div>
              <div class="row" v-if="!Object.keys(reactiveForm.doors).length && !reactiveForm.selectedDoorsLineItems.length">
                <div class="col-sm-12">{{ t('doors.notSelected') }}</div>
              </div>
            </li>
          </ul>
        </AccordionItem>
        <AccordionItem id="handles"
                       :is-open-by-default="true"
                       :section-price="apiResponse?.priceOffer?.sectionsCalculatedPrice?.handlesAndRosettes">
          <ul class="list-group list-group-flush">
            <li class="list-group-item">
              <div class="row">
                <p>
                  <span class="text-transform-none">{{ t('handlesAdditionalText') }}
                    <a href="https://www.kluckynadvere.sk/" target="_blank">www.kluckynadvere.sk</a>
                  </span>
                </p>
              </div>
              <div class="row">
                <div class="col-xm-12 col-xl-6">
                  <input type="text"
                         v-model.lazy="reactiveForm.handle.name"
                         :id="'handle-name'"
                         :placeholder="t('handles.namePlaceholder')"
                         class="form-control"/>
                </div>
                <div class="col-sm-4 col-xl-2">
                  <div class="input-group">
                    <input type="number"
                           v-model.number="reactiveForm.handle.price"
                           :id="'handle-price'"
                           class="form-control"
                           min="0"/>
                    <span class="input-group-text">€</span>
                  </div>
                </div>
                <div class="col-sm-4 col-xl-2">
                  <div class="input-group">
                    <input type="number"
                           v-model.number="reactiveForm.handle.count"
                           v-on:change="handleHandleCountChange($event)"
                           :id="'handle-count'"
                           class="form-control"
                           min="0"
                           step="1"/>
                    <span class="input-group-text">ks</span>
                  </div>
                </div>
                <div class="col-sm-4 col-xl-2">
                  <BadgePrice
                      class="badge-full-width"
                      :price="apiResponse?.priceOffer?.handle?.calculatedPrice ?? 0.0"/>
                </div>
              </div>
            </li>
            <template v-for="r of reactiveForm.rosettes" :key="r.id">
              <li class="list-group-item">
                <div class="row">
                  <div class="col-sm-12 col-xl-8 align-self-center">{{ findRosetteById(r.id, apiResponse)?.label }}
                    <span>{{ formatPrice(findRosetteById(r.id, apiResponse)?.price) }}/ks </span>
                    <Hint
                        v-if="findRosetteById(r.id, apiResponse)"
                        :hintObj="findRosetteById(r.id, apiResponse) as HintInterface"/>
                  </div>
                  <div class="col-sm-4 d-xl-none"></div>
                  <div class="col-sm-4 col-xl-2">
                    <div class="input-group">
                      <input type="number"
                             v-model.number="r.count"
                             :id="'rosette-count-' + r.id"
                             class="form-control"
                             min="0"
                             step="1">
                      <span class="input-group-text">ks</span>
                    </div>
                  </div>
                  <div class="col-sm-4 col-xl-2">
                    <BadgePrice
                        class="badge-full-width"
                        :price="findRosetteById(r.id, apiResponse)?.calculatedPrice"/>
                  </div>
                </div>
              </li>
            </template>
            <LineItems
                v-model:line-items="reactiveForm.rosettesLineItems"
                :line-items-response="apiResponse?.priceOffer.rosettesLineItems"
            />
            <li class="list-group-item">
              <div class="row">
                <div class="col-sm-12 col-xl-8 align-self-center">{{ t('handles.assemblyHandlesRosettes') }}</div>
                <div class="col-sm-4 d-xl-none"></div>
                <div class="col-sm-4 col-xl-2">
                  <div class="input-group">
                    <input type="number"
                           v-model.number="reactiveForm.assemblyPriceHandlesRosettesCount"
                           id="assemblyPriceHandlesRosettesCount"
                           class="form-control"
                           min="0"
                           step="1">
                    <span class="input-group-text">ks</span>
                  </div>
                </div>
                <div class="col-sm-4 col-xl-2">
                  <BadgePrice
                      class="badge-full-width"
                      :price="apiResponse?.priceOffer.assemblyPriceHandlesRosettesCalculatedPrice"/>
                </div>
              </div>
            </li>
          </ul>
        </AccordionItem>
        <AccordionItem id="delivery"
                       :is-open-by-default="true"
                       :section-price="apiResponse?.priceOffer?.sectionsCalculatedPrice?.delivery">
          <ul class="list-group list-group-flush">
            <li class="list-group-item">
              <div class="row mb-2">
                <div class="col-sm-12 col-xl-6">{{ t("delivery.district") }}</div>
                <div class="col-sm-8 col-xl-4">
                  <select class="form-select" v-model="reactiveForm.address.district">
                    <option value="">Vyberte okres</option>
                    <option v-for="d in apiResponse?.districts"
                            :key="d.id"
                            :value="d.id">{{ d.label }}
                    </option>
                  </select>
                </div>
                <div class="col-sm-4 col-xl-2">
                  <BadgePrice
                      class="badge-full-width"
                      :price="apiResponse?.priceOffer.deliveryPrice"/>
                </div>
              </div>
              <div class="row mb-2">
                <div class="col-sm-12 col-xl-12">{{ t("delivery.note") }}</div>
              </div>
            </li>
            <li class="list-group-item">
              <div class="row">
                <div class="col-sm-12 col-xl-12"><h5>{{ t("delivery.address") }}</h5></div>
                <div class="col-sm-6 col-xl-6">
                  <div class="form-floating">
                    <input id="address-street"
                           type="text"
                           class="form-control"
                           placeholder=" "
                           v-model.lazy="reactiveForm.address.street"/>
                    <label for="address-street">{{ t("delivery.street") }}</label>
                  </div>
                </div>
                <div class="col-sm-6 col-xl-6">
                  <div class="form-floating">
                    <input id="address-streetNumber"
                           type="text"
                           class="form-control"
                           placeholder=" "
                           v-model.lazy="reactiveForm.address.streetNumber"/>
                    <label for="address-streetNumber">{{ t("delivery.streetNumber") }}</label>
                  </div>
                </div>
                <div class="col-sm-6 col-xl-6">
                  <div class="form-floating">
                    <input id="address-city"
                           type="text"
                           class="form-control"
                           placeholder=" "
                           v-model.lazy="reactiveForm.address.city"/>
                    <label for="address-city">{{ t("delivery.city") }}</label>
                  </div>
                </div>
                <div class="col-sm-6 col-xl-6">
                  <div class="form-floating">
                    <input id="address-zipCode"
                           type="text"
                           class="form-control"
                           placeholder=" "
                           v-model.lazy="reactiveForm.address.zipCode"/>
                    <label for="address-zipCode">{{ t("delivery.zipCode") }}</label>
                  </div>
                </div>
              </div>
            </li>
          </ul>
        </AccordionItem>
        <AccordionItem id="assemblyDoors"
                       :is-open-by-default="true"
                       :section-price="apiResponse?.priceOffer?.sectionsCalculatedPrice?.assemblyDoors">
          <ul class="list-group list-group-flush">
            <li class="list-group-item">
              <div class="row">
                <div class="col-sm-12 col-xl-8 text-align-justify">{{ t("assemblyDoors.note") }}</div>
                <div class="col-sm-4 d-xl-none"></div>
                <div class="col-sm-4 col-xl-2">
                  <div class="input-group">
                    <input type="number"
                           v-model.number="reactiveForm.assemblyDoorsCount"
                           @input="reactiveForm.isAssemblyDoorsCountDirty = true"
                           id="assemblyDoorsCount"
                           class="form-control"
                           min="0"
                           step="1">
                    <span class="input-group-text">ks</span>
                  </div>
                </div>
                <div class="col-sm-4 col-xl-2">
                  <BadgePrice
                      class="badge-full-width"
                      :price="apiResponse?.priceOffer.assemblyDoorsCalculatedPrice"/>
                </div>
              </div>

            </li>
          </ul>
        </AccordionItem>
        <AccordionItem id="specialAccessories"
                       :is-open-by-default="false"
                       :section-price="apiResponse?.priceOffer?.sectionsCalculatedPrice?.specialAccessories">
          <ul class="list-group list-group-flush">
            <li v-for="it of reactiveForm.specialAccessories"
                :key="it.id"
                class="list-group-item">
              <div class="row">
                <div class="col-sm-12 col-xl-6 align-self-center">{{
                    findSpecialAccessoryById(it.id, apiResponse)?.label
                  }}
                  <span v-if="findSpecialAccessoryById(it.id, apiResponse)?.configuredPrice">{{
                      formatPrice(findSpecialAccessoryById(it.id, apiResponse)?.configuredPrice)
                    }}/ks </span>
                  <Hint
                      v-if="findSpecialAccessoryById(it.id, apiResponse)"
                      :hintObj="findSpecialAccessoryById(it.id, apiResponse) as HintInterface"/>
                </div>
                <div class="col-sm-4 col-xl-2"
                     :class="{ invisible: findSpecialAccessoryById(it.id, apiResponse)?.configuredPrice !== null }">
                  <div class="input-group">
                    <input type="number"
                           v-model.number="it.selectedPrice"
                           :id="'special-accessory-price-' + it.id"
                           class="form-control"
                           min="0"
                           step="1"/>
                    <span class="input-group-text">€</span>
                  </div>
                </div>
                <div class="col-sm-4 col-xl-2">
                  <div class="input-group">
                    <input type="number"
                           v-model.number="it.count"
                           :id="'special-accessory-count-' + it.id"
                           class="form-control"
                           min="0"
                           step="1">
                    <span class="input-group-text">ks</span>
                  </div>
                </div>
                <div class="col-sm-4 col-xl-2">
                  <BadgePrice
                      class="badge-full-width"
                      :price="findSpecialAccessoryById(it.id, apiResponse)?.calculatedPrice"/>
                </div>
              </div>
            </li>
            <LineItems
                v-model:line-items="reactiveForm.specialAccessoriesLineItems"
                :line-items-response="apiResponse?.priceOffer.specialAccessoriesLineItems"
            />
          </ul>
        </AccordionItem>
        <AccordionItem id="possibleAdditionalCharges"
                       :is-open-by-default="false"
                       :section-price="apiResponse?.priceOffer?.sectionsCalculatedPrice?.possibleAdditionalCharges">
          <ul class="list-group list-group-flush">
            <li v-for="it of reactiveForm.possibleAdditionalCharges"
                :key="it.id"
                class="list-group-item">
              <div class="row">
                <div class="col-sm-12 col-xl-8 align-self-center text-align-justify">
                  {{ findPossibleAdditionalChargeById(it.id, apiResponse)?.label }}
                  <Hint
                      v-if="findPossibleAdditionalChargeById(it.id, apiResponse)"
                      :hintObj="findPossibleAdditionalChargeById(it.id, apiResponse) as HintInterface"/>
                </div>
                <div class="col-sm-4 d-xl-none"></div>
                <div class="col-sm-4 col-xl-2">
                  <div class="input-group">
                    <input type="number"
                           v-model.number="it.count"
                           @input="it.isCountDirty = true"
                           :id="'possible-additional-charge-count-' + it.id"
                           class="form-control"
                           min="0"
                           step="1">
                    <span class="input-group-text">ks</span>
                  </div>
                </div>
                <div class="col-sm-4 col-xl-2">
                  <BadgePrice
                      class="badge-full-width"
                      :price="findPossibleAdditionalChargeById(it.id, apiResponse)?.calculatedPrice"/>
                </div>
              </div>
            </li>
            <LineItems
                v-model:line-items="reactiveForm.possibleAdditionalChargesLineItems"
                :line-items-response="apiResponse?.priceOffer.possibleAdditionalChargesLineItems"
            />
          </ul>
        </AccordionItem>
        <AccordionItem id="specialSurcharges"
                       :is-open-by-default="false"
                       :section-price="apiResponse?.priceOffer?.sectionsCalculatedPrice?.specialSurcharges">
          <ul class="list-group list-group-flush">
            <li v-for="it of reactiveForm.specialSurcharges"
                :key="it.id"
                class="list-group-item">
              <div class="row">
                <div class="col-sm-12 col-xl-8 align-self-center">{{
                    findSpecialSurchargeById(it.id, apiResponse)?.label
                  }}
                  <Hint v-if="findSpecialSurchargeById(it.id, apiResponse)"
                        :hintObj="findSpecialSurchargeById(it.id, apiResponse) as HintInterface"/>
                </div>
                <div class="col-sm-4 d-xl-none"/>
                <div class="col-sm-4 col-xl-2">
                  <div class="row">
                    <div class="col-sm-12">
                      <div class="input-group">
                        <input type="number"
                               v-model.number="it.count"
                               :id="'special-surcharges-count-' + it.id"
                               class="form-control"
                               min="0"
                               step="1">
                        <span class="input-group-text">ks</span>
                      </div>
                    </div>
                  </div>
                </div>
                <div class="col-sm-4 col-xl-2">
                  <BadgePrice
                      class="badge-full-width"
                      :price="findSpecialSurchargeById(it.id, apiResponse)?.calculatedPrice"/>
                </div>
              </div>
              <div class="row">
                <div class="col-sm-8 col-xl-8 align-self-center">{{
                    findSpecialSurchargeById(it.id, apiResponse)?.labelAssembly
                  }}
                </div>
                <div class="col-sm-4 col-xl-2">
                  <div class="form-check">
                    <input class="form-check-input"
                           type="checkbox"
                           :disabled="it.count === 0"
                           @input="it.isAssemblySelectedDirty = true"
                           v-model="it.isAssemblySelected"/>
                    <label class="form-check-label" for="checkDefault">Montáž</label>
                  </div>
                </div>
                <div class="d-sm-none col-xl-2"></div>
              </div>
            </li>
            <LineItems
                v-model:line-items="reactiveForm.specialSurchargesLineItems"
                :line-items-response="apiResponse?.priceOffer.specialSurchargesLineItems"
            />
          </ul>
        </AccordionItem>
        <AccordionItem id="contact"
                       :is-open-by-default="true">
          <ul class="list-group list-group-flush">
            <li class="list-group-item">
              <div class="row">
                <div class="col-sm-6 col-xl-6">
                  <div class="form-floating">
                    <input id="contact-fullName"
                           v-model.lazy="reactiveForm.contact.fullName"
                           type="text"
                           class="form-control"
                           placeholder=" "/>
                    <label for="contact-fullName">{{ t("contact.fullName") }}</label>
                  </div>
                </div>
                <div class="col-sm-6 col-xl-6">
                  <div class="form-floating">
                    <input id="contact-email"
                           v-model.lazy="reactiveForm.contact.email"
                           type="email"
                           class="form-control"
                           :class="{ 'is-invalid': getFieldValidations('contact-email').length }"
                           placeholder=" "
                           required/>
                    <label for="contact-email">{{ t("contact.email") }}</label>
                    <div class="invalid-feedback">
                      <ValidationMessages :validations="getFieldValidations('contact-email')"/>
                    </div>
                  </div>
                </div>
                <div class="col-sm-6 col-xl-6">
                  <div class="form-floating">
                    <input id="contact-phoneNumber"
                           v-model.lazy="reactiveForm.contact.phoneNumber"
                           type="text"
                           class="form-control"
                           placeholder=" "/>
                    <label for="contact-phoneNumber">{{ t("contact.phoneNumber") }}</label>
                  </div>
                </div>
                <div class="col-sm-6 col-xl-6">
                  <div class="form-floating">
          <textarea id="price-offer-note"
                    v-model="reactiveForm.note"
                    class="form-control"
                    placeholder=" "/>
                    <label for="price-offer-note">{{ t("contact.note") }}</label>
                  </div>
                </div>
              </div>
            </li>
          </ul>
        </AccordionItem>
      </div>
      <div v-if="apiResponse" class="price-sticky py-1 px-3 rounded">
        <div class="row mb-2">
          <div class="col-sm-8 col-xl-10 align-content-center">
            CENA SPOLU BEZ DPH
          </div>
          <div class="col-sm-4 col-xl-2">
            <BadgePrice
                class="badge-full-width"
                :price="apiResponse.priceOffer.calculatedPrice"/>
          </div>
        </div>
        <div class="row">
          <div class="col-sm-8 col-xl-10 align-content-center">
            CENA SPOLU S DPH
          </div>
          <div class="col-sm-4 col-xl-2">
            <BadgePrice
                class="badge-full-width"
                :price="apiResponse.priceOffer.calculatedPriceVat"/>
          </div>
        </div>
      </div>
      <div>
        <div id="recaptcha"
             class="mb-1"></div>
        <button type="submit"
                class="btn bg-primary text-white bold"
                :disabled="submitButtonDisabled"
                v-on:click="handleFormSubmit($event)">{{ t('submitButton') }}
        </button>
      </div>
    </form>
  </div>
  <div class="mt-auto footer h-auto">
    <div>Created by</div>
    <div>Ing. Peter Sučanský</div>
    <div>peter@sucansky.sk</div>
    <div>+421 904 901 799</div>
    <div>Ing. Peter Hutáš</div>
    <div>Ing. Dominik Janíček</div>
    <div>Copyright © {{ new Date().getFullYear() }}. All rights reserved</div>
  </div>
</template>

<style lang="scss">
.badge-full-width {
  height: $input-height;
  width: 100%;
  min-width: 105px;
}

.color-dark-red {
  color: darkred;
}

.container-door-form-group {
  display: flex;
  flex-direction: column;
  gap: 5px;
}

.text-align-center {
  text-align: center;
}

.text-transform-none {
  text-transform: none;
}

.text-align-justify {
  text-align: justify;
}

.list-group-item:nth-child(odd) {
  background-color: #fff;
}

.list-group-item:nth-child(even) {
  background-color: rgba($primary, 0.1);
}

.price-sticky {
  position: sticky;
  bottom: 0; // or 0 if you want it glued to the top
  z-index: 10;
  background: white; // important so content doesn’t show through
  padding-top: 0.5rem;
}
</style>