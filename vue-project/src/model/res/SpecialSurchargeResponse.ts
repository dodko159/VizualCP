import {HintInterface} from "../interface/HintInterface.js";

export interface SpecialSurchargeResponse extends HintInterface {
    id: string
    calculatedPrice: number
    configuredPrice: number
    count: number
    header: string
    hint: string
    imgSrc: string
    isAssemblySelected: boolean
    isAssemblySelectedDirty: boolean
    label: string
    labelAssembly: string
    youtubeVideoCode: string
    videoSrc: string | null
}