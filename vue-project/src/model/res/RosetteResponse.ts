import {HintInterface} from "../interface/HintInterface.js";

export interface RosetteResponse extends HintInterface {
    id: string
    calculatedPrice: number
    count: number
    header: string
    hint: string
    imgSrc: string
    label: string
    price: number
    youtubeVideoCode: string
    videoSrc: string | null
}