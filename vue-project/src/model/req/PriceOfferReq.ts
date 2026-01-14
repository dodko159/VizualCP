import {DoorReq} from "./DoorReq.js";
import {RosetteReq} from "./RosetteReq.js";
import {HandleReq} from "./HandleReq.js";
import {AddressReq} from "./AddressReq.js";
import {ContactReq} from "./ContactReq.js";
import {SpecialAccessoriesReq} from "./SpecialAccessoriesReq.js";
import {PossibleAdditionalChargeReq} from "./PossibleAdditionalChargeReq.js";
import {SpecialSurchargeReq} from "./SpecialSurchargeReq.js";
import {LineItemReq} from "./LineItemReq.js";

export interface PriceOfferReq {
    address: AddressReq
    assemblyDoorsCount: number
    assemblyPriceHandlesRosettesCount: number
    contact: ContactReq
    doors: Record<string, DoorReq>
    handle: HandleReq
    isAssemblyDoorsCountDirty: boolean
    note: string
    possibleAdditionalCharges: PossibleAdditionalChargeReq[]
    possibleAdditionalChargesLineItems: LineItemReq[]
    rosettes: RosetteReq[]
    rosettesLineItems: LineItemReq[]
    specialAccessories: SpecialAccessoriesReq[]
    specialAccessoriesLineItems: LineItemReq[]
    specialSurcharges: SpecialSurchargeReq[]
    specialSurchargesLineItems: LineItemReq[]
}