import {HintInterface} from "../interface/HintInterface.js";

export interface PossibleAdditionalChargeResponse extends HintInterface {
    id: string
    calculatedPrice: number
    configuredPrice: number
    count: number
    header: string
    hint: string
    imgSrc: string | null
    isCountDirty: boolean
    label: string
    youtubeVideoCode: string
    videoSrc: string
}