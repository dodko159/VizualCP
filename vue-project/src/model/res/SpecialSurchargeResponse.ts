import {HintInterface} from "../interface/HintInterface.js";

export interface SpecialSurchargeResponse extends HintInterface {
    id: string
    calculatedPrice: number
    configuredPrice: number | null
    count: number | null
    header: string | null
    hint: string | null
    imgSrc: string | null
    isAssemblySelected: boolean | null
    isAssemblySelectedDirty: boolean | null
    label: string | null
    labelAssembly: string | null
    youtubeVideoCode: string | null
    videoSrc: string | null
}