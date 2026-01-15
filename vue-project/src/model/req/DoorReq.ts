export interface DoorReq {
    category: string//e.g. Alica
    isDoorFrameEnabled: boolean
    isDtdSelected: boolean
    material: string//e.g. lamino-jelsa
    type: string//e.g. v1
    width: number | null//e.g. 1
}