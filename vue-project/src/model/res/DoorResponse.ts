export interface DoorResponse {
    category: string//e.g. Alica
    isDoorFrameEnabled: boolean
    isDtdAvailable: boolean
    isDtdSelected: boolean
    material: string//e.g. lamino-jelsa
    calculatedPrice: number//e.g. 150
    type: string//e.g. v1
    width: number//e.g. 1
}