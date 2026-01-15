import {PriceOfferResponse} from "./PriceOfferResponse.js";
import {DistrictResponse} from "./DistrictResponse.js";

export interface ApiResponse {
    districts: DistrictResponse[]
    priceOffer: PriceOfferResponse
}