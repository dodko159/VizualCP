import {createRouter, createWebHistory} from 'vue-router'
import PriceOfferFinished from "./views/PriceOfferFinished.vue";
import PriceOffer from "./views/PriceOffer.vue";

const routes = [
    {path: '/', component: PriceOffer},
    {path: '/price-offer-finished', component: PriceOfferFinished}
]

export const router = createRouter({
    history: createWebHistory(),
    routes
})