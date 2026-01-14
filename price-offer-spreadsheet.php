<?php
include_once 'json-data-manipulation.php';

require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

function generateSpreadSheet(PriceOfferResponse $priceOffer): string
{
    $spreadsheet = loadSpreadSheet('assets/xlsx/cenova_ponuka_Hutas_04012026.xlsx');
    addBusinessDataIntoSpreadsheet($spreadsheet, $priceOffer);
    return writeSpreadSheetToOutput($spreadsheet);
}

function addBusinessDataIntoSpreadsheet(Spreadsheet $spreadsheet, PriceOfferResponse $priceOffer): void
{
    $rowIdx = 1;

    /*** Contact ***/
    $sheet = $spreadsheet->getActiveSheet();

    $fullName = $priceOffer->contact->fullName;
    if ($fullName) {
        $sheet->setCellValue('F' . $rowIdx, $fullName);
    }
    $rowIdx++;

    $phoneNumber = $priceOffer->contact->phoneNumber;
    if ($phoneNumber) {
        $sheet->setCellValueExplicit('F' . $rowIdx, $phoneNumber, DataType::TYPE_STRING);
    }
    $rowIdx++;

    $address = getFormattedAddress($priceOffer->address);
    if ($address) {
        $sheet->setCellValue('F' . $rowIdx, $address);
    }
    $rowIdx++;

    $email = $priceOffer->contact->email;
    if ($email) {
        $sheet->setCellValue('F' . $rowIdx, $email);
    }
    $rowIdx++;

    /*** Doors ***/
    $doors = $priceOffer->doors;
    $rowIdx = $rowIdx + 2;

    $doorsCount = count($doors);
    $rowCountToInsert = $doorsCount > 9 ? $doorsCount - 9 : 0;

    if ($rowCountToInsert > 0) {
        $sheet->insertNewRowBefore(16, $rowCountToInsert);
    }

    foreach ($doors as $door) {
        $sheet->setCellValue("A$rowIdx", Width::getWidthString($door->width) ?: 60);
        $sheet->setCellValue("B$rowIdx", $door->type);
        $sheet->setCellValue("C$rowIdx", DoorsJsonDataManipulation::getMaterialTranslation($door->material));
        $sheet->setCellValue("E$rowIdx", "=IFERROR(VLOOKUP(B$rowIdx, Calc!A:B, 2, FALSE), 0)+IF(A$rowIdx<=69, 0,IF(A$rowIdx<=79, 3,IF(A$rowIdx<=89, 6, 9)))");
        $sheet->setCellValue("F$rowIdx", $door->isDoorFrameEnabled ? 'TRUE' : 'FALSE');
        $sheet->setCellValue("G$rowIdx", 1);
        $sheet->setCellValue(
            "H$rowIdx",
            "=IF(ISBLANK(B$rowIdx),0, G$rowIdx*E$rowIdx+IF(F$rowIdx, IF(B$rowIdx=\"v1\", 79, 93), 0))"
        );
        $rowIdx = $rowIdx + 1;
    }

    /*** Handles and rosettes ***/
    $rowIdx = max($rowIdx + 2, 18);
    $handle = $priceOffer->handle;
    if ($handle) {
        if ($handle->name) {
            $sheet->setCellValue("A$rowIdx", $handle->name);
        }

        if ($handle->price) {
            $sheet->setCellValue("F$rowIdx", $handle->price);
        }

        if ($handle->count) {
            $sheet->setCellValue("G$rowIdx", $handle->count);
        }
    }

    $rowIdx = $rowIdx + 1;
    foreach ($priceOffer->rosettes as $rosette) {
        $count = $rosette->count;
        if ($count) {
            $sheet->setCellValue("G" . $rowIdx, $count);
        }

        $rowIdx = $rowIdx + 1;
    }

    $rowCountToInsert = insertLineItems($priceOffer->rosettesLineItems, $sheet, $rowIdx);
    $rowIdx = $rowIdx + 1 + $rowCountToInsert;
    $assemblyPriceHandlesRosettesCount = $priceOffer->assemblyPriceHandlesRosettesCount;
    if ($assemblyPriceHandlesRosettesCount) {
        $sheet->setCellValue('G' . $rowIdx, $assemblyPriceHandlesRosettesCount);
    }

    /*** Delivery ***/
    $rowIdx = $rowIdx + 3;
    $district = $priceOffer->address->district;
    if ($district) {
        $districtConfig = DistrictsJsonDataManipulation::findByIdOrFalse($district);
        $sheet->setCellValue('F' . $rowIdx, $districtConfig ? $districtConfig['label'] : "");
    }

    $deliveryCost = $priceOffer->deliveryPrice;
    if ($deliveryCost) {
        $sheet->setCellValue('G' . $rowIdx, $deliveryCost);
    }
    $rowIdx++;

    $street = $priceOffer->address->street;
    if ($street) {
        $sheet->setCellValue('F' . $rowIdx, $street);
    }

    $rowIdx++;
    $streetNumber = $priceOffer->address->streetNumber;
    if ($streetNumber) {
        $sheet->setCellValue('F' . $rowIdx, $streetNumber);
    }

    $rowIdx++;
    $city = $priceOffer->address->city;
    if ($city) {
        $sheet->setCellValue('F' . $rowIdx, $city);
    }

    $rowIdx++;
    $zipCode = $priceOffer->address->zipCode;
    if ($zipCode) {
        $sheet->setCellValue('F' . $rowIdx, $zipCode);
    }

    /*** Assembly ***/
    $rowIdx = $rowIdx + 3;
    $assemblyDoorsCount = $priceOffer->assemblyDoorsCount;
    if ($assemblyDoorsCount) {
        $sheet->setCellValue('G' . $rowIdx, $assemblyDoorsCount);
    }

    /*** Special accessories ***/
    $rowIdx = $rowIdx + 3;
    foreach ($priceOffer->specialAccessories as $item) {
        $selectedPrice = $item->selectedPrice;
        if ($selectedPrice) {
            $sheet->setCellValue("F" . $rowIdx, $selectedPrice);
        }

        $count = $item->count;
        if ($count) {
            $sheet->setCellValue("G" . $rowIdx, $count);
        }

        $rowIdx = $rowIdx + 1;
    }

    $rowCountToInsert = insertLineItems($priceOffer->specialAccessoriesLineItems, $sheet, $rowIdx);
    $rowIdx = $rowIdx + $rowCountToInsert;

    /*** Possible additional charges ***/
    $rowIdx = $rowIdx + 3;
    foreach ($priceOffer->possibleAdditionalCharges as $item) {
        $count = $item->count;
        if ($count) {
            $sheet->setCellValue("G" . $rowIdx, $count);
        }

        $sheet->getRowDimension($rowIdx)->setRowHeight(70);
        $rowIdx = $rowIdx + 1;
    }

    $rowCountToInsert = insertLineItems($priceOffer->possibleAdditionalChargesLineItems, $sheet, $rowIdx);
    $rowIdx = $rowIdx + $rowCountToInsert;

    /*** Special surcharges ***/
    $rowIdx = $rowIdx + 3;
    foreach ($priceOffer->specialSurcharges as $item) {
        $isAssemblySelected = $item->isAssemblySelected;
        $sheet->setCellValue("F" . $rowIdx, $isAssemblySelected ? 'TRUE' : 'FALSE');

        $count = $item->count;
        if ($count) {
            $sheet->setCellValue("G" . $rowIdx, $count);
        }

        $sheet->getRowDimension($rowIdx)->setRowHeight(30);
        $rowIdx = $rowIdx + 1;
    }

    insertLineItems($priceOffer->specialSurchargesLineItems, $sheet, $rowIdx);
}

function loadSpreadSheet(string $filename): Spreadsheet
{
    $reader = new Xlsx();
    return $reader->load($filename);
}

function writeSpreadSheetToOutput(Spreadsheet $spreadsheet): string
{
    ob_start();
    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save('php://output');
    return ob_get_clean();
}

function getFormattedAddress(AddressResponse $address): string
{
    $parts = array_filter([
        trim($address->street . ' ' . $address->streetNumber),
        trim($address->zipCode . ' ' . $address->city),
    ]);

    return implode(', ', $parts);
}

function insertLineItems(
    array $lineItems,
    Worksheet $sheet,
    int $rowIdx
): int
{
    $lineItemsCount = count($lineItems);
    $rowCountToInsert = $lineItemsCount > 1 ? $lineItemsCount - 1 : 0;

    if ($rowCountToInsert > 0) {
        $sheet->insertNewRowBefore($rowIdx, $rowCountToInsert);
    }

    foreach ($lineItems as $item) {
        $name = $item->name;
        if ($name) {
            $sheet->setCellValue("A" . $rowIdx, $name);
        }

        $price = $item->price;
        if ($price) {
            $sheet->setCellValue("F" . $rowIdx, $price);
        }

        $count = $item->count;
        if ($count) {
            $sheet->setCellValue("G" . $rowIdx, $count);
        }

        $sheet->setCellValue("H$rowIdx", "=F$rowIdx*G$rowIdx");

        $sheet->mergeCells('A' . $rowIdx . ':E' . $rowIdx);
        $sheet->getStyle("A{$rowIdx}:H{$rowIdx}")->getFill()
            ->setFillType(Fill::FILL_SOLID)
            ->getStartColor()->setRGB('FFFFFF');
        $sheet->getRowDimension($rowIdx)->setRowHeight(15);

        $rowIdx = $rowIdx + 1;
    }

    return $rowCountToInsert;
}
?>