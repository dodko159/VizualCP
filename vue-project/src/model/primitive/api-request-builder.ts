import {FormLineItem, FormPriceOffer, FormSelectedDoorLineItem} from "./form-builder.js";
import {ApiResponse} from "../res/ApiResponse.js";
import {ApiRequest} from "../req/ApiRequest.js";
import {DoorReq} from "../req/DoorReq.js";
import {RosetteReq} from "../req/RosetteReq.js";
import {SpecialAccessoriesReq} from "../req/SpecialAccessoriesReq.js";
import {PossibleAdditionalChargeReq} from "../req/PossibleAdditionalChargeReq.js";
import {SpecialSurchargeReq} from "../req/SpecialSurchargeReq.js";
import {LineItemReq} from "../req/LineItemReq.js";
import {SelectedDoorLineItemRequest} from "../req/SelectedDoorLineItemRequest.js";

export function prepareRequest(
    reactiveForm: FormPriceOffer,
    apiResponse: ApiResponse | undefined
): ApiRequest | undefined {
    if (!apiResponse) {
        return
    }

    const doors: Record<string, DoorReq> = (Object.entries(reactiveForm.doors) as [string, Record<string, any>][]).reduce(
        (acc, [key, value]) => {
            acc[key] = {
                category: apiResponse.priceOffer.doors[key].category!,
                isDoorFrameEnabled: value.isDoorFrameEnabled,
                isDtdSelected: value.isDtdSelected,
                material: apiResponse.priceOffer.doors[key].material!,
                type: apiResponse.priceOffer.doors[key].type!,
                width: value.doorWidth
            };
            return acc;
        },
        {} as Record<string, DoorReq>
    );

    const possibleAdditionalCharges: PossibleAdditionalChargeReq[] = reactiveForm.possibleAdditionalCharges.map(r => {
        return {
            id: r.id,
            count: r.count || 0,
            isCountDirty: r.isCountDirty
        }
    })
    const possibleAdditionalChargesLineItems: LineItemReq[] = reactiveForm.possibleAdditionalChargesLineItems.map(it => mapLineItem(it));

    const rosettes: RosetteReq[] = reactiveForm.rosettes.map(it => {
        return {
            id: it.id,
            count: it.count || 0
        }
    })

    const rosettesLineItems: LineItemReq[] = reactiveForm.rosettesLineItems.map(it => mapLineItem(it));

    const selectedDoorsLineItems: SelectedDoorLineItemRequest[] = reactiveForm.selectedDoorsLineItems.map(it => mapSelectedDoorLineItem(it));
    const specialAccessories: SpecialAccessoriesReq[] = reactiveForm.specialAccessories.map(it => {
        return {
            id: it.id,
            count: it.count || 0,
            selectedPrice: it.selectedPrice || 0
        }
    })
    const specialAccessoriesLineItems: LineItemReq[] = reactiveForm.specialAccessoriesLineItems.map(it => mapLineItem(it));

    const specialSurcharges: SpecialSurchargeReq[] = reactiveForm.specialSurcharges.map(it => {
        return {
            id: it.id,
            count: it.count || 0,
            isAssemblySelected: it.isAssemblySelected || false,
            isAssemblySelectedDirty: it.isAssemblySelectedDirty || false
        }
    })
    const specialSurchargesLineItems: LineItemReq[] = reactiveForm.specialSurchargesLineItems.map(it => mapLineItem(it));

    return {
        priceOffer: {
            address: {
                city: reactiveForm.address.city || "",
                district: reactiveForm.address.district || "",
                street: reactiveForm.address.street || "",
                streetNumber: reactiveForm.address.streetNumber || "",
                zipCode: reactiveForm.address.zipCode || ""
            },
            assemblyDoorsCount: reactiveForm.assemblyDoorsCount || 0,
            assemblyPriceHandlesRosettesCount: reactiveForm.assemblyPriceHandlesRosettesCount || 0,
            contact: {
                email: reactiveForm.contact.email || "",
                fullName: reactiveForm.contact.fullName || "",
                phoneNumber: reactiveForm.contact.phoneNumber || ""
            },
            doors: doors,
            handle: {
                name: reactiveForm.handle.name || "",
                price: reactiveForm.handle.price || 0,
                count: reactiveForm.handle.count || 0
            },
            isAssemblyDoorsCountDirty: reactiveForm.isAssemblyDoorsCountDirty || false,
            note: reactiveForm.note || "",
            possibleAdditionalCharges: possibleAdditionalCharges,
            possibleAdditionalChargesLineItems: possibleAdditionalChargesLineItems,
            rosettes: rosettes,
            rosettesLineItems: rosettesLineItems,
            selectedDoorsLineItems: selectedDoorsLineItems,
            specialAccessories: specialAccessories,
            specialAccessoriesLineItems: specialAccessoriesLineItems,
            specialSurcharges: specialSurcharges,
            specialSurchargesLineItems: specialSurchargesLineItems
        }
    };
}

function mapLineItem(it: FormLineItem): LineItemReq {
    return {
        name: it.name,
        price: it.price || 0,
        count: it.count || 0
    }
}

function mapSelectedDoorLineItem(it: FormSelectedDoorLineItem): SelectedDoorLineItemRequest {
    return {
        isDoorFrameEnabled: it.isDoorFrameEnabled,
        name: it.name,
        price: it.price,
        width: it.width
    }
}