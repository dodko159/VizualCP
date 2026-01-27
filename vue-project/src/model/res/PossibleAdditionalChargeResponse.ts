import {HintInterface} from "../interface/HintInterface.js";

export interface PossibleAdditionalChargeResponse extends HintInterface {
    id: string
    calculatedPrice: number
    configuredPrice: number | null
    count: number | null
    header: string | null
    hint: string | null
    imgSrc: string | null
    isCountDirty: boolean | null
    label: string | null
    youtubeVideoCode: string | null
    videoSrc: string | null
}