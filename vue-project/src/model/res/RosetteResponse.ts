import {HintInterface} from "../interface/HintInterface.js";

export interface RosetteResponse extends HintInterface {
    id: string
    calculatedPrice: number
    count: number | null
    header: string | null
    hint: string | null
    imgSrc: string | null
    label: string | null
    price: number | null
    youtubeVideoCode: string | null
    videoSrc: string | null
}