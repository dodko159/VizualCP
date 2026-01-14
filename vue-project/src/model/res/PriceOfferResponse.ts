import {DoorResponse} from "./DoorResponse.js";
import {RosetteResponse} from "./RosetteResponse.js";
import {HandleResponse} from "./HandleResponse.js";
import {AddressResponse} from "./AddressResponse.js";
import {ContactResponse} from "./ContactResponse.js";
import {SectionsCalculatedPriceResponse} from "./SectionsCalculatedPriceResponse.js";
import {SpecialAccessoriesResponse} from "./SpecialAccessoriesResponse.js";
import {PossibleAdditionalChargeResponse} from "./PossibleAdditionalChargeResponse.js";
import {SpecialSurchargeResponse} from "./SpecialSurchargeResponse.js";
import {LineItemResponse} from "./LineItemResponse.js";

export interface PriceOfferResponse {
    address: AddressResponse
    assemblyDoorsCount: number
    assemblyDoorsCalculatedPrice: number
    assemblyPriceHandlesRosettesCount: number
    assemblyPriceHandlesRosettesCalculatedPrice: number
    calculatedPrice: number
    calculatedPriceVat: number
    contact: ContactResponse
    deliveryPrice: number
    doors: Record<string, DoorResponse>
    handle: HandleResponse | null
    isAssemblyDoorsCountDirty: boolean
    note: string
    possibleAdditionalCharges: PossibleAdditionalChargeResponse[]
    possibleAdditionalChargesLineItems: LineItemResponse[]
    rosettes: RosetteResponse[]
    rosettesLineItems: LineItemResponse[]
    sectionsCalculatedPrice: SectionsCalculatedPriceResponse
    specialAccessories: SpecialAccessoriesResponse[]
    specialAccessoriesLineItems: LineItemResponse[]
    specialSurcharges: SpecialSurchargeResponse[]
    specialSurchargesLineItems: LineItemResponse[]
}