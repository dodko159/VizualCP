<?php

include_once('constants.php');
include_once('functions.php');

//Requests
class AddressRequest
{
    /** @var string $city */
    public $city;

    /** @var string $district */
    public $district;

    /** @var string $street */
    public $street;

    /** @var string $streetNumber */
    public $streetNumber;

    /** @var string $zipCode */
    public $zipCode;

    public function __construct(array $json)
    {
        $this->city = $json['city'];
        $this->district = $json['district'];
        $this->street = $json['street'];
        $this->streetNumber = $json['streetNumber'];
        $this->zipCode = $json['zipCode'];
    }
}

class ContactRequest
{
    /** @var string $email */
    public $email;

    /** @var string $fullName */
    public $fullName;

    /** @var string $phoneNumber */
    public $phoneNumber;

    public function __construct(array $json)
    {
        $this->email = $json['email'];
        $this->fullName = $json['fullName'];
        $this->phoneNumber = $json['phoneNumber'];
    }
}

class HandleRequest
{
    /** @var int $count */
    public $count;

    /** @var string $name */
    public $name;

    /** @var float $price */
    public $price;

    public function __construct(array $json)
    {
        $this->count = $json['count'];
        $this->name = $json['name'];
        $this->price = $json['price'];
    }
}

class LineItemRequest
{
    /** @var int $count */
    public $count;

    /** @var string $name */
    public $name;

    /** @var float $price */
    public $price;

    public function __construct(array $json)
    {
        $this->count = $json['count'];
        $this->name = $json['name'];
        $this->price = $json['price'];
    }
}

class PossibleAdditionalChargeRequest
{
    /** @var string id */
    public $id;

    /** @var int $count */
    public $count;

    /** @var boolean $isCountDirty */
    public $isCountDirty;

    public function __construct(array $json)
    {
        $this->id = $json['id'];
        $this->count = $json['count'];
        $this->isCountDirty = $json['isCountDirty'];
    }
}

class RosetteRequest
{
    /** @var string $id */
    public $id;

    /** @var int $count */
    public $count;

    public function __construct(array $json)
    {
        $this->id = $json['id'];
        $this->count = $json['count'];
    }
}

class SpecialAccessoryRequest
{
    /** @var string $id */
    public $id;

    /** @var int $count */
    public $count;

    /** @var float $selectedPrice */
    public $selectedPrice;

    public function __construct(array $json)
    {
        $this->id = $json['id'];
        $this->count = $json['count'];
        $this->selectedPrice = $json['selectedPrice'];
    }
}

class SpecialSurchargeRequest
{
    /** @var string $id */
    public $id;

    /** @var int $count */
    public $count;

    /** @var boolean $isAssemblySelected */
    public $isAssemblySelected;

    /** @var boolean $isAssemblySelectedDirty */
    public $isAssemblySelectedDirty;

    public function __construct(array $json)
    {
        $this->id = $json['id'];
        $this->count = $json['count'];
        $this->isAssemblySelected = $json['isAssemblySelected'];
        $this->isAssemblySelectedDirty = $json['isAssemblySelectedDirty'];
    }
}

class DoorRequest
{
    /** @var string $category */
    public $category;

    /** @var boolean $isDoorFrameEnabled */
    public $isDoorFrameEnabled;

    /** @var boolean $isDtdSelected */
    public $isDtdSelected;

    /** @var string $material */
    public $material;

    /** @var string $type */
    public $type;

    /** @var string $width */
    public $width;

    public function __construct(array $json)
    {
        $this->category = $json['category'];
        $this->isDoorFrameEnabled = $json['isDoorFrameEnabled'];
        $this->isDtdSelected = $json['isDtdSelected'];
        $this->material = $json['material'];
        $this->type = $json['type'];
        $this->width = $json['width'];
    }
}

class PriceOfferRequest
{
    /** @var AddressRequest $address */
    public $address;

    /** @var int $assemblyDoorsCount */
    public $assemblyDoorsCount;

    /** @var int $assemblyPriceHandlesRosettesCount */
    public $assemblyPriceHandlesRosettesCount;

    /** @var ContactRequest $contact */
    public $contact;

    /** @var HandleRequest $handle */
    public $handle;

    /** @var boolean $isAssemblyDoorsCountDirty */
    public $isAssemblyDoorsCountDirty;

    /** @var DoorRequest[] $doors */
    public $doors = array();

    /** @var string $note */
    public $note;

    /** @var PossibleAdditionalChargeRequest[] $possibleAdditionalCharges */
    public $possibleAdditionalCharges = array();

    /** @var LineItemRequest[] $possibleAdditionalChargesLineItems */
    public $possibleAdditionalChargesLineItems = array();

    /** @var RosetteRequest[] $rosettes */
    public $rosettes = array();

    /** @var LineItemRequest[] $rosettesLineItems */
    public $rosettesLineItems = array();

    /** @var SpecialAccessoryRequest[] $specialAccessories */
    public $specialAccessories = array();

    /** @var LineItemRequest[] $specialAccessoriesLineItems */
    public $specialAccessoriesLineItems = array();

    /** @var SpecialSurchargeRequest[] $specialSurcharges */
    public $specialSurcharges = array();

    /** @var LineItemRequest[] $specialSurchargesLineItems */
    public $specialSurchargesLineItems = array();

    public function __construct(array $json)
    {
        $this->address = $json['address'] ? new AddressRequest($json['address']) : null;
        $this->assemblyDoorsCount = $json['assemblyDoorsCount'] ?? 0;
        $this->assemblyPriceHandlesRosettesCount = $json['assemblyPriceHandlesRosettesCount'] ?? 0;
        $this->contact = $json['contact'] ? new ContactRequest($json['contact']) : null;
        $this->doors = array_map(function ($doorData) {
            return new DoorRequest($doorData);
        }, $json['doors'] ?? array());
        $this->handle = new HandleRequest($json['handle']);
        $this->isAssemblyDoorsCountDirty = $json['isAssemblyDoorsCountDirty'];
        $this->note = $json['note'] ?? "";
        $this->possibleAdditionalCharges = array_map(function ($p) {
            return new PossibleAdditionalChargeRequest($p);
        }, $json['possibleAdditionalCharges'] ?? array());
        $this->possibleAdditionalChargesLineItems = array_map(function ($p) {
            return new LineItemRequest($p);
        }, $json['possibleAdditionalChargesLineItems'] ?? array());
        $this->rosettes = array_map(function ($rosetteData) {
            return new RosetteRequest($rosetteData);
        }, $json['rosettes'] ?? array());
        $this->rosettesLineItems = array_map(function ($p) {
            return new LineItemRequest($p);
        }, $json['rosettesLineItems'] ?? array());
        $this->specialAccessories = array_map(function ($specialAccessoryData) {
            return new SpecialAccessoryRequest($specialAccessoryData);
        }, $json['specialAccessories'] ?? array());
        $this->specialAccessoriesLineItems = array_map(function ($p) {
            return new LineItemRequest($p);
        }, $json['specialAccessoriesLineItems'] ?? array());
        $this->specialSurcharges = array_map(function ($specialSurchargeData) {
            return new SpecialSurchargeRequest($specialSurchargeData);
        }, $json['specialSurcharges'] ?? array());
        $this->specialSurchargesLineItems = array_map(function ($p) {
            return new LineItemRequest($p);
        }, $json['specialSurchargesLineItems'] ?? array());
    }
}

class ApiRequest
{
    /** @var PriceOfferRequest $priceOffer */
    public $priceOffer;

    public function __construct(array $json)
    {
        $this->priceOffer = new PriceOfferRequest($json['priceOffer']);
    }
}

//Responses
class AppConfigResponse
{
    /** @var boolean $reCaptchaEnabled */
    public $reCaptchaEnabled;

    /** @var string $reCaptchaSiteKey */
    public $reCaptchaSiteKey;

    public function __construct($reCaptchaEnabled, $reCaptchaSiteKey)
    {
        $this->reCaptchaEnabled = $reCaptchaEnabled;
        $this->reCaptchaSiteKey = $reCaptchaSiteKey;
    }
}

class AddressResponse implements JsonSerializable
{
    /** @var string $city */
    public $city;

    /** @var string $district */
    public $district;

    /** @var string $street */
    public $street;

    /** @var string $streetNumber */
    public $streetNumber;

    /** @var string $zipCode */
    public $zipCode;

    public function __construct($city, $district, $street, $streetNumber, $zipCode)
    {
        $this->city = $city;
        $this->district = $district;
        $this->street = $street;
        $this->streetNumber = $streetNumber;
        $this->zipCode = $zipCode;
    }

    public function jsonSerialize(): array
    {
        return [
            'city' => $this->city,
            'district' => $this->district,
            'street' => $this->street,
            'streetNumber' => $this->streetNumber,
            'zipCode' => $this->zipCode
        ];
    }
}

class ContactResponse implements JsonSerializable
{
    /** @var string $email */
    public $email;

    /** @var string $fullName */
    public $fullName;

    /** @var string $phoneNumber */
    public $phoneNumber;

    public function __construct($email, $fullName, $phoneNumber)
    {
        $this->email = $email;
        $this->fullName = $fullName;
        $this->phoneNumber = $phoneNumber;
    }

    public function jsonSerialize(): array
    {
        return [
            'email' => $this->email,
            'fullName' => $this->fullName,
            'phoneNumber' => $this->phoneNumber
        ];
    }
}

class DoorResponse implements JsonSerializable
{
    /** @var float $calculatedPrice */
    public $calculatedPrice;

    /** @var string $category */
    public $category;

    /** @var boolean $isDoorFrameEnabled */
    public $isDoorFrameEnabled;

    /** @var boolean $isDtdAvailable */
    public $isDtdAvailable;

    /** @var boolean $isDtdSelected */
    public $isDtdSelected;

    /** @var string $material */
    public $material;

    /** @var string $type */
    public $type;

    /** @var string $width */
    public $width;

    /**
     * @param $calculatedPrice
     * @param $category
     * @param $isDoorFrameEnabled
     * @param $isDtdAvailable
     * @param $isDtdSelected
     * @param $material
     * @param $type
     * @param $width
     */
    public function __construct($calculatedPrice, $category, $isDoorFrameEnabled, $isDtdAvailable, $isDtdSelected, $material, $type, $width)
    {
        $this->calculatedPrice = $calculatedPrice;
        $this->category = $category;
        $this->isDoorFrameEnabled = $isDoorFrameEnabled;
        $this->isDtdAvailable = $isDtdAvailable;
        $this->isDtdSelected = $isDtdSelected;
        $this->material = $material;
        $this->type = $type;
        $this->width = $width;
    }

    public function jsonSerialize(): array
    {
        return [
            'calculatedPrice' => $this->calculatedPrice,
            'category' => $this->category,
            'isDoorFrameEnabled' => $this->isDoorFrameEnabled,
            'isDtdAvailable' => $this->isDtdAvailable,
            'isDtdSelected' => $this->isDtdSelected,
            'material' => $this->material,
            'type' => $this->type,
            'width' => $this->width
        ];
    }
}

class HandleResponse implements JsonSerializable
{
    /** @var float $calculatedPrice */
    public $calculatedPrice;

    /** @var int $count */
    public $count;

    /** @var string $name */
    public $name;

    /** @var float $price */
    public $price;

    public function __construct($calculatedPrice, $count, $name, $price)
    {
        $this->calculatedPrice = $calculatedPrice;
        $this->count = $count;
        $this->name = $name;
        $this->price = $price;
    }

    public function jsonSerialize(): array
    {
        return [
            'calculatedPrice' => $this->calculatedPrice,
            'count' => $this->count,
            'name' => $this->name,
            'price' => $this->price
        ];
    }
}

class LineItemResponse implements JsonSerializable
{
    /** @var float $calculatedPrice */
    public $calculatedPrice;

    /** @var int $count */
    public $count;

    /** @var string $name */
    public $name;

    /** @var float $price */
    public $price;

    public function __construct($calculatedPrice, $count, $name, $price)
    {
        $this->calculatedPrice = $calculatedPrice;
        $this->count = $count;
        $this->name = $name;
        $this->price = $price;
    }

    public function jsonSerialize(): array
    {
        return [
            'calculatedPrice' => $this->calculatedPrice,
            'count' => $this->count,
            'name' => $this->name,
            'price' => $this->price
        ];
    }
}

class PossibleAdditionalChargeResponse implements JsonSerializable
{
    /** @var string $id */
    public $id;

    /** @var float $calculatedPrice */
    public $calculatedPrice;

    /** @var float $configuredPrice */
    public $configuredPrice;

    /** @var int $count */
    public $count;

    /** @var string $header */
    public $header;

    /** @var string $hint */
    public $hint;

    /** @var string $imgSrc */
    public $imgSrc;

    /** @var boolean $isCountDirty */
    public $isCountDirty;

    /** @var string $label */
    public $label;

    /** @var string $youtubeVideoCode */
    public $youtubeVideoCode;

    /** @var string $videoSrc */
    public $videoSrc;

    public function __construct($id, $calculatedPrice, $configuredPrice, $count, $isCountDirty, $header, $hint, $imgSrc, $label, $youtubeVideoCode, $videoSrc)
    {
        $this->id = $id;
        $this->calculatedPrice = $calculatedPrice ?? 0;
        $this->configuredPrice = $configuredPrice ?? 0;
        $this->count = $count ?? 0;
        $this->header = $header;
        $this->hint = $hint;
        $this->imgSrc = $imgSrc;
        $this->isCountDirty = $isCountDirty ?? false;
        $this->label = $label;
        $this->youtubeVideoCode = $youtubeVideoCode;
        $this->videoSrc = $videoSrc;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'calculatedPrice' => $this->calculatedPrice,
            'configuredPrice' => $this->configuredPrice,
            'count' => $this->count,
            'header' => $this->header,
            'hint' => $this->hint,
            'imgSrc' => $this->imgSrc,
            'isCountDirty' => $this->isCountDirty,
            'label' => $this->label,
            'youtubeVideoCode' => $this->youtubeVideoCode,
            'videoSrc' => $this->videoSrc
        ];
    }
}

class RosetteResponse implements JsonSerializable
{
    /** @var string $id */
    public $id;

    /** @var float $calculatedPrice */
    public $calculatedPrice;

    /** @var int $count */
    public $count;

    /** @var string $header */
    public $header;

    /** @var string $hint */
    public $hint;

    /** @var string $imgSrc */
    public $imgSrc;

    /** @var string $label */
    public $label;

    /** @var float $price */
    public $price;

    /** @var string $youtubeVideoCode */
    public $youtubeVideoCode;

    /** @var string $videoSrc */
    public $videoSrc;

    public function __construct($id, $calculatedPrice, $count, $header, $hint, $imgSrc, $label, $price, $youtubeVideoCode, $videoSrc)
    {
        $this->id = $id;
        $this->calculatedPrice = $calculatedPrice ?? 0;
        $this->count = $count ?? 0;
        $this->header = $header;
        $this->hint = $hint;
        $this->imgSrc = $imgSrc;
        $this->label = $label;
        $this->price = $price;
        $this->youtubeVideoCode = $youtubeVideoCode;
        $this->videoSrc = $videoSrc;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'calculatedPrice' => $this->calculatedPrice,
            'count' => $this->count,
            'header' => $this->header,
            'hint' => $this->hint,
            'imgSrc' => $this->imgSrc,
            'label' => $this->label,
            'price' => $this->price,
            'youtubeVideoCode' => $this->youtubeVideoCode,
            'videoSrc' => $this->videoSrc
        ];
    }
}

class SectionsCalculatedPriceResponse implements JsonSerializable
{
    /** @var float $doors */
    public $doors;

    /** @var float $handlesAndRosettes */
    public $handlesAndRosettes;

    /** @var float $delivery */
    public $delivery;

    /** @var float $assemblyDoors */
    public $assemblyDoors;

    /** @var float $specialAccessories */
    public $specialAccessories;

    /** @var float $possibleAdditionalCharges */
    public $possibleAdditionalCharges;

    /** @var float $specialSurcharges */
    public $specialSurcharges;

    public function __construct($doors, $handlesAndRosettes, $delivery, $assemblyDoors, $specialAccessories, $possibleAdditionalCharges, $specialSurcharges)
    {
        $this->doors = $doors ?? 0.0;
        $this->handlesAndRosettes = $handlesAndRosettes ?? 0.0;
        $this->delivery = $delivery ?? 0.0;
        $this->assemblyDoors = $assemblyDoors ?? 0.0;
        $this->specialAccessories = $specialAccessories ?? 0.0;
        $this->possibleAdditionalCharges = $possibleAdditionalCharges ?? 0.0;
        $this->specialSurcharges = $specialSurcharges ?? 0.0;
    }

    public function jsonSerialize(): array
    {
        return [
            'doors' => $this->doors,
            'handlesAndRosettes' => $this->handlesAndRosettes,
            'delivery' => $this->delivery,
            'assemblyDoors' => $this->assemblyDoors,
            'specialAccessories' => $this->specialAccessories,
            'possibleAdditionalCharges' => $this->possibleAdditionalCharges,
            'specialSurcharges' => $this->specialSurcharges
        ];
    }
}

class SpecialAccessoryResponse implements JsonSerializable
{
    /** @var string id */
    public $id;

    /** @var float $calculatedPrice */
    public $calculatedPrice;

    /** @var float $configuredPrice */
    public $configuredPrice;

    /** @var int $count */
    public $count;

    /** @var string $header */
    public $header;

    /** @var string $hint */
    public $hint;

    /** @var string $imgSrc */
    public $imgSrc;

    /** @var string $label */
    public $label;

    /** @var float $selectedPrice */
    public $selectedPrice;

    /** @var string $youtubeVideoCode */
    public $youtubeVideoCode;

    /** @var string $videoSrc */
    public $videoSrc;

    public function __construct($id, $calculatedPrice, $configuredPrice, $count, $header, $hint, $imgSrc, $label, $selectedPrice,
                                $youtubeVideoCode, $videoSrc)
    {
        $this->id = $id;
        $this->calculatedPrice = $calculatedPrice ?? 0;
        $this->count = $count ?? 0;
        $this->configuredPrice = $configuredPrice;
        $this->header = $header;
        $this->hint = $hint;
        $this->imgSrc = $imgSrc;
        $this->label = $label;
        $this->selectedPrice = $selectedPrice;
        $this->youtubeVideoCode = $youtubeVideoCode;
        $this->videoSrc = $videoSrc;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'calculatedPrice' => $this->calculatedPrice,
            'configuredPrice' => $this->configuredPrice,
            'count' => $this->count,
            'header' => $this->header,
            'hint' => $this->hint,
            'imgSrc' => $this->imgSrc,
            'label' => $this->label,
            'selectedPrice' => $this->selectedPrice,
            'youtubeVideoCode' => $this->youtubeVideoCode,
            'videoSrc' => $this->videoSrc
        ];
    }
}

class SpecialSurchargeResponse implements JsonSerializable
{
    /** @var string $id */
    public $id;

    /** @var float $calculatedPrice */
    public $calculatedPrice;

    /** @var float $configuredPrice */
    public $configuredPrice;

    /** @var int $count */
    public $count;

    /** @var string $header */
    public $header;

    /** @var string $hint */
    public $hint;

    /** @var string $imgSrc */
    public $imgSrc;

    /** @var boolean $isAssemblySelected */
    public $isAssemblySelected;

    /** @var boolean $isAssemblySelectedDirty */
    public $isAssemblySelectedDirty;

    /** @var string $label */
    public $label;

    /** @var string $labelAssembly */
    public $labelAssembly;

    /** @var string $youtubeVideoCode */
    public $youtubeVideoCode;

    /** @var string $videoSrc */
    public $videoSrc;

    public function __construct($id, $calculatedPrice, $configuredPrice, $count, $header, $hint, $imgSrc, $isAssemblySelected, $isAssemblySelectedDirty, $label, $labelAssembly, $youtubeVideoCode, $videoSrc)
    {
        $this->id = $id;
        $this->calculatedPrice = $calculatedPrice ?? 0;
        $this->configuredPrice = $configuredPrice ?? 0;
        $this->count = $count ?? 0;
        $this->header = $header;
        $this->hint = $hint;
        $this->imgSrc = $imgSrc;
        $this->isAssemblySelected = $isAssemblySelected ?? false;
        $this->isAssemblySelectedDirty = $isAssemblySelectedDirty ?? false;
        $this->label = $label;
        $this->labelAssembly = $labelAssembly;
        $this->youtubeVideoCode = $youtubeVideoCode;
        $this->videoSrc = $videoSrc;
    }

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'calculatedPrice' => $this->calculatedPrice,
            'configuredPrice' => $this->configuredPrice,
            'count' => $this->count,
            'header' => $this->header,
            'hint' => $this->hint,
            'imgSrc' => $this->imgSrc,
            'isAssemblySelected' => $this->isAssemblySelected,
            'isAssemblySelectedDirty' => $this->isAssemblySelectedDirty,
            'label' => $this->label,
            'labelAssembly' => $this->labelAssembly,
            'youtubeVideoCode' => $this->youtubeVideoCode,
            'videoSrc' => $this->videoSrc
        ];
    }
}

class PriceOfferResponse implements JsonSerializable
{
    /** @var AddressResponse $address */
    public $address;

    /** @var float $assemblyDoorsCalculatedPrice */
    public $assemblyDoorsCalculatedPrice;

    /** @var int $assemblyDoorsCount */
    public $assemblyDoorsCount;

    /** @var int $assemblyPriceHandlesRosettesCount */
    public $assemblyPriceHandlesRosettesCount;

    /** @var float $assemblyPriceHandlesRosettesCalculatedPrice */
    public $assemblyPriceHandlesRosettesCalculatedPrice;

    /** @var float $calculatedPrice */
    public $calculatedPrice;

    /** @var float $calculatedPriceVat */
    public $calculatedPriceVat;

    /** @var ContactResponse $contact */
    public $contact;

    /** @var float $deliveryPrice */
    public $deliveryPrice;

    /** @var DoorResponse[] $doors */
    public $doors;

    /** @var HandleResponse $handle */
    public $handle;

    /** @var boolean $isAssemblyDoorsCountDirty */
    public $isAssemblyDoorsCountDirty;

    /** @var string $note */
    public $note;

    /** @var PossibleAdditionalChargeResponse[] $possibleAdditionalCharges */
    public $possibleAdditionalCharges;

    /** @var LineItemResponse[] $possibleAdditionalChargesLineItems */
    public $possibleAdditionalChargesLineItems;

    /** @var RosetteResponse[] $rosettes */
    public $rosettes;

    /** @var LineItemResponse[] $rosettesLineItems */
    public $rosettesLineItems;

    /** @var SectionsCalculatedPriceResponse $sectionsCalculatedPrice */
    public $sectionsCalculatedPrice;

    /** @var SpecialAccessoryResponse[] $specialAccessories */
    public $specialAccessories;

    /** @var LineItemResponse[] $specialAccessoriesLineItems */
    public $specialAccessoriesLineItems;

    /** @var SpecialSurchargeResponse[] $specialSurcharges */
    public $specialSurcharges;

    /** @var LineItemResponse[] $specialSurchargesLineItems */
    public $specialSurchargesLineItems;

    public function __construct(
        $address,
        $assemblyDoorsCalculatedPrice,
        $assemblyDoorsCount,
        $assemblyPriceHandlesRosettesCount,
        $assemblyPriceHandlesRosettesCalculatedPrice,
        $calculatedPrice,
        $calculatedPriceVat,
        $contact,
        $deliveryPrice,
        $doors,
        $handle,
        $isAssemblyDoorsCountDirty,
        $note,
        $possibleAdditionalCharges,
        $possibleAdditionalChargesLineItems,
        $rosettes,
        $rosettesLineItems,
        $sectionsCalculatedPrice,
        $specialAccessories,
        $specialAccessoriesLineItems,
        $specialSurcharges,
        $specialSurchargesLineItems
    )
    {
        $this->address = $address;
        $this->assemblyDoorsCalculatedPrice = $assemblyDoorsCalculatedPrice;
        $this->assemblyDoorsCount = $assemblyDoorsCount ?? 0;
        $this->assemblyPriceHandlesRosettesCount = $assemblyPriceHandlesRosettesCount ?? 0;
        $this->assemblyPriceHandlesRosettesCalculatedPrice = $assemblyPriceHandlesRosettesCalculatedPrice ?? 0;
        $this->calculatedPrice = $calculatedPrice ?? 0;
        $this->calculatedPriceVat = $calculatedPriceVat ?? 0;
        $this->contact = $contact;
        $this->deliveryPrice = $deliveryPrice ?? 0;
        $this->doors = $doors;
        $this->handle = $handle;
        $this->isAssemblyDoorsCountDirty = $isAssemblyDoorsCountDirty;
        $this->note = $note;
        $this->possibleAdditionalCharges = $possibleAdditionalCharges;
        $this->possibleAdditionalChargesLineItems = $possibleAdditionalChargesLineItems ?? [];
        $this->rosettes = $rosettes;
        $this->rosettesLineItems = $rosettesLineItems ?? [];
        $this->sectionsCalculatedPrice = $sectionsCalculatedPrice;
        $this->specialAccessories = $specialAccessories;
        $this->specialAccessoriesLineItems = $specialAccessoriesLineItems ?? [];
        $this->specialSurcharges = $specialSurcharges;
        $this->specialSurchargesLineItems = $specialSurchargesLineItems ?? [];
    }

    public function jsonSerialize(): array
    {
        return [
            'address' => $this->address,
            'assemblyDoorsCalculatedPrice' => $this->assemblyDoorsCalculatedPrice,
            'assemblyDoorsCount' => $this->assemblyDoorsCount,
            'assemblyPriceHandlesRosettesCount' => $this->assemblyPriceHandlesRosettesCount,
            'assemblyPriceHandlesRosettesCalculatedPrice' => $this->assemblyPriceHandlesRosettesCalculatedPrice,
            'calculatedPrice' => $this->calculatedPrice,
            'calculatedPriceVat' => $this->calculatedPriceVat,
            'contact' => $this->contact,
            'deliveryPrice' => $this->deliveryPrice,
            'doors' => $this->doors,
            'handle' => $this->handle,
            'isAssemblyDoorsCountDirty' => $this->isAssemblyDoorsCountDirty,
            'note' => $this->note,
            'possibleAdditionalCharges' => $this->possibleAdditionalCharges,
            'possibleAdditionalChargesLineItems' => $this->possibleAdditionalChargesLineItems,
            'rosettes' => $this->rosettes,
            'rosettesLineItems' => $this->rosettesLineItems,
            'sectionsCalculatedPrice' => $this->sectionsCalculatedPrice,
            'specialAccessories' => $this->specialAccessories,
            'specialAccessoriesLineItems' => $this->specialAccessoriesLineItems,
            'specialSurcharges' => $this->specialSurcharges,
            'specialSurchargesLineItems' => $this->specialSurchargesLineItems
        ];
    }
}

class ApiResponse implements JsonSerializable
{
    public $districts;
    public $priceOffer;

    public function __construct($districts, $priceOffer)
    {
        $this->districts = $districts;
        $this->priceOffer = $priceOffer;
    }

    public function jsonSerialize(): array
    {
        return [
            'districts' => $this->districts,
            'priceOffer' => $this->priceOffer
        ];
    }
}

?>