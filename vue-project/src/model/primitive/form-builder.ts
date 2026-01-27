import {ApiResponse} from "../res/ApiResponse.js";
import {SelectedDoorResponse} from "../res/SelectedDoorResponse.js";
import {HandleResponse} from "../res/HandleResponse.js";
import {AddressResponse} from "../res/AddressResponse.js";
import {ContactResponse} from "../res/ContactResponse.js";
import {RosetteResponse} from "../res/RosetteResponse.js";
import {SpecialAccessoriesResponse} from "../res/SpecialAccessoriesResponse.js";
import {SpecialSurchargeResponse} from "../res/SpecialSurchargeResponse.js";
import {PossibleAdditionalChargeResponse} from "../res/PossibleAdditionalChargeResponse.js";
import {LineItemResponse} from "../res/LineItemResponse.js";
import {SelectedDoorLineItemResponse} from "../res/SelectedDoorLineItemResponse.js";

interface FormContact {
    email: string
    fullName: string
    phoneNumber: string
}

export interface FormAddress {
    city: string
    district: string
    street: string
    streetNumber: string
    zipCode: string
}

export interface FormPriceOffer {
    address: FormAddress
    assemblyDoorsCount: number
    assemblyPriceHandlesRosettesCount: number
    contact: FormContact
    doors: Record<string, FormDoor>,
    handle: FormHandle,
    isAssemblyDoorsCountDirty: boolean,
    note: string,
    possibleAdditionalCharges: FormPossibleAdditionalCharge[],
    possibleAdditionalChargesLineItems: FormLineItem[],
    rosettes: FormRosette[],
    rosettesLineItems: FormLineItem[],
    selectedDoorsLineItems: FormSelectedDoorLineItem[],
    specialAccessories: FormSpecialAccessory[],
    specialAccessoriesLineItems: FormLineItem[],
    specialSurcharges: FormSpecialSurcharge[],
    specialSurchargesLineItems: FormLineItem[]
}

export interface FormDoor {
    doorWidth: number
    isDtdSelected: boolean
    isDoorFrameEnabled: boolean
}

export interface FormHandle {
    count: number
    name: string
    price: number
}

export interface FormLineItem {
    count: number
    name: string
    price: number
}

export interface FormRosette {
    id: string
    count: number
}

export interface FormSelectedDoorLineItem {
    isDoorFrameEnabled: boolean
    name: string
    price: number
    width: string
}

export interface FormSpecialAccessory {
    id: string
    count: number
    selectedPrice: number
}

export interface FormSpecialSurcharge {
    id: string
    count: number
    isAssemblySelected: boolean
    isAssemblySelectedDirty: boolean
}

export interface FormPossibleAdditionalCharge {
    id: string
    count: number
    isCountDirty: boolean
}

export function constructReactiveFormPriceOffer(json: ApiResponse | undefined): FormPriceOffer {
    return {
        address: constructReactiveFormAddress(json?.priceOffer.address),
        assemblyDoorsCount: json?.priceOffer.assemblyDoorsCount || 0,
        assemblyPriceHandlesRosettesCount: json?.priceOffer.assemblyPriceHandlesRosettesCount || 0,
        contact: constructReactiveFormContact(json?.priceOffer.contact),
        doors: constructReactiveFormDoors(json?.priceOffer.doors || {}),
        handle: constructReactiveFormHandle(json?.priceOffer.handle),
        isAssemblyDoorsCountDirty: json?.priceOffer.isAssemblyDoorsCountDirty || false,
        note: json?.priceOffer.note || "",
        possibleAdditionalCharges: constructReactiveFormPossibleAdditionalCharges(json?.priceOffer.possibleAdditionalCharges || []),
        possibleAdditionalChargesLineItems: json?.priceOffer.possibleAdditionalChargesLineItems.map(it => constructReactiveFormLineItem(it)) || [],
        rosettes: constructReactiveFormRosettes(json?.priceOffer.rosettes || []),
        rosettesLineItems: json?.priceOffer.rosettesLineItems.map(it => constructReactiveFormLineItem(it)) || [],
        selectedDoorsLineItems: constructReactiveSelectedDoorsLineItems(json?.priceOffer?.selectedDoorsLineItems ?? []),
        specialAccessories: constructReactiveSpecialAccessories(json?.priceOffer.specialAccessories || []),
        specialAccessoriesLineItems: json?.priceOffer.specialAccessoriesLineItems.map(it => constructReactiveFormLineItem(it)) || [],
        specialSurcharges: constructReactiveSpecialSurcharges(json?.priceOffer.specialSurcharges || []),
        specialSurchargesLineItems: json?.priceOffer.specialSurchargesLineItems.map(it => constructReactiveFormLineItem(it)) || []
    }
}

export function constructReactiveFormContact(contact: ContactResponse | undefined): FormContact {
    return {
        email: contact?.email || "",
        fullName: contact?.fullName || "",
        phoneNumber: contact?.phoneNumber || ""
    }
}

export function constructReactiveFormAddress(address: AddressResponse | undefined): FormAddress {
    return {
        city: address?.city || "",
        district: address?.district || "",
        street: address?.street || "",
        streetNumber: address?.streetNumber || "",
        zipCode: address?.zipCode || ""
    }
}

export function constructReactiveFormDoors(doors: Record<string, SelectedDoorResponse>): Record<string, FormDoor> {
    const reduceDoors: any = {}
    for (const [key, value] of Object.entries(doors) as [string, SelectedDoorResponse][]) {
        reduceDoors[key] = {
            doorWidth: value.width,
            isDtdSelected: value.isDtdSelected,
            isDoorFrameEnabled: value.isDoorFrameEnabled
        }
    }

    return reduceDoors
}

export function constructReactiveFormHandle(handle: HandleResponse | undefined | null): FormHandle {
    return {
        count: handle?.count || 0,
        name: handle?.name || "",
        price: handle?.price || 0
    }
}

export function constructReactiveFormLineItem(handle: LineItemResponse): FormLineItem {
    return {
        count: handle?.count || 0,
        name: handle?.name || "",
        price: handle?.price || 0
    }
}

export function constructReactiveFormPossibleAdditionalCharges(charges: PossibleAdditionalChargeResponse[]): FormPossibleAdditionalCharge[] {
    return charges.map(it => {
        return {
            id: it.id,
            count: it.count ?? 0,
            isCountDirty: it.isCountDirty ?? false
        }
    })
}

export function constructReactiveFormRosettes(rosettes: RosetteResponse[]): FormRosette[] {
    return rosettes.map(rosette => {
        return {
            id: rosette.id,
            count: rosette.count ?? 0
        }
    })
}

export function constructReactiveSpecialAccessories(specialAccessories: SpecialAccessoriesResponse[]): FormSpecialAccessory[] {
    return specialAccessories.map(specialAccessory => {
        return {
            id: specialAccessory.id,
            count: specialAccessory.count ?? 0,
            selectedPrice: specialAccessory.selectedPrice ?? 0.0
        }
    })
}

export function constructReactiveSpecialSurcharges(specialSurcharges: SpecialSurchargeResponse[]): FormSpecialSurcharge[] {
    return specialSurcharges.map(specialSurcharge => {
        return {
            id: specialSurcharge.id,
            count: specialSurcharge.count ?? 0,
            isAssemblySelected: specialSurcharge.isAssemblySelected ?? false,
            isAssemblySelectedDirty: specialSurcharge.isAssemblySelectedDirty ?? false
        }
    })
}

export function constructReactiveSelectedDoorsLineItems(selectedDoorsLineItems: SelectedDoorLineItemResponse[]): FormSelectedDoorLineItem[] {
    return selectedDoorsLineItems.map(it => {
        return {
            isDoorFrameEnabled: it.isDoorFrameEnabled ?? false,
            name: it.name ?? "",
            price: it.price ?? 0,
            width: it.width ?? ""
        }
    })
}

/*
const rosetteMetaById = computed(() =>
  Object.fromEntries(
    apiResponse.priceOffer.rosettes.map(r => [
      r.id,
      {
        label: r.label,
        hint: r.hint,
        imgSrc: r.imgSrc,
        price: r.price
      }
    ])
  )
)
*/

export function findPossibleAdditionalChargeById(id: string, apiResponse: ApiResponse | undefined): PossibleAdditionalChargeResponse | undefined {
    return apiResponse?.priceOffer.possibleAdditionalCharges.find(it => it.id === id)
}

export function findRosetteById(id: string, apiResponse: ApiResponse | undefined): RosetteResponse | undefined {
    return apiResponse?.priceOffer.rosettes.find(it => it.id === id)
}

export function findSpecialAccessoryById(id: string, apiResponse: ApiResponse | undefined): SpecialAccessoriesResponse | undefined {
    return apiResponse?.priceOffer.specialAccessories.find(it => it.id === id)
}

export function findSpecialSurchargeById(id: string, apiResponse: ApiResponse | undefined): SpecialSurchargeResponse | undefined {
    return apiResponse?.priceOffer.specialSurcharges.find(it => it.id === id)
}