import {SelectedDoorResponse} from "./SelectedDoorResponse.js";
import {RosetteResponse} from "./RosetteResponse.js";
import {HandleResponse} from "./HandleResponse.js";
import {AddressResponse} from "./AddressResponse.js";
import {ContactResponse} from "./ContactResponse.js";
import {SectionsCalculatedPriceResponse} from "./SectionsCalculatedPriceResponse.js";
import {SpecialAccessoriesResponse} from "./SpecialAccessoriesResponse.js";
import {PossibleAdditionalChargeResponse} from "./PossibleAdditionalChargeResponse.js";
import {SpecialSurchargeResponse} from "./SpecialSurchargeResponse.js";
import {LineItemResponse} from "./LineItemResponse.js";
import {SelectedDoorLineItemResponse} from "./SelectedDoorLineItemResponse.js";

export interface PriceOfferResponse {
    address: AddressResponse
    assemblyDoorsCount: number | null
    assemblyDoorsCalculatedPrice: number
    assemblyPriceHandlesRosettesCount: number | null
    assemblyPriceHandlesRosettesCalculatedPrice: number
    calculatedPrice: number
    calculatedPriceVat: number
    contact: ContactResponse
    deliveryPrice: number
    doors: Record<string, SelectedDoorResponse>
    handle: HandleResponse
    isAssemblyDoorsCountDirty: boolean | null
    note: string | null
    possibleAdditionalCharges: PossibleAdditionalChargeResponse[]
    possibleAdditionalChargesLineItems: LineItemResponse[]
    rosettes: RosetteResponse[]
    rosettesLineItems: LineItemResponse[]
    sectionsCalculatedPrice: SectionsCalculatedPriceResponse
    selectedDoorsLineItems: SelectedDoorLineItemResponse[]
    specialAccessories: SpecialAccessoriesResponse[]
    specialAccessoriesLineItems: LineItemResponse[]
    specialSurcharges: SpecialSurchargeResponse[]
    specialSurchargesLineItems: LineItemResponse[]
}