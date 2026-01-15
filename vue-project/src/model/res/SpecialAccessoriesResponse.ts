import {HintInterface} from "../interface/HintInterface.js";

export interface SpecialAccessoriesResponse extends HintInterface {
    id: string
    calculatedPrice: number
    configuredPrice: number | null
    count: number
    header: string
    hint: string
    imgSrc: string
    label: string
    selectedPrice: number
    youtubeVideoCode: string
    videoSrc: string | null
}