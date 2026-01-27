import {HintInterface} from "../interface/HintInterface.js";

export interface SpecialAccessoriesResponse extends HintInterface {
    id: string
    calculatedPrice: number
    configuredPrice: number | null
    count: number | null
    header: string | null
    hint: string | null
    imgSrc: string | null
    label: string | null
    selectedPrice: number | null
    youtubeVideoCode: string | null
    videoSrc: string | null
}