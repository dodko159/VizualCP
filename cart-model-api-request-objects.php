<?php

include_once('constants.php');
include_once('functions.php');

//Requests
class AddressRequest
{
    /** @var string|null $city */
    public $city;

    /** @var string|null $district */
    public $district;

    /** @var string|null $street */
    public $street;

    /** @var string|null $streetNumber */
    public $streetNumber;

    /** @var string|null $zipCode */
    public $zipCode;

    public function __construct(array $json)
    {
        $this->city = $json['city'] ?? null;
        $this->district = $json['district'] ?? null;
        $this->street = $json['street'] ?? null;
        $this->streetNumber = $json['streetNumber'] ?? null;
        $this->zipCode = $json['zipCode'] ?? null;
    }
}

class ContactRequest
{
    /** @var string|null $email */
    public $email;

    /** @var string|null $fullName */
    public $fullName;

    /** @var string|null $phoneNumber */
    public $phoneNumber;

    public function __construct(array $json)
    {
        $this->email = $json['email'] ?? null;
        $this->fullName = $json['fullName'] ?? null;
        $this->phoneNumber = $json['phoneNumber'] ?? null;
    }
}

class HandleRequest
{
    /** @var int|null $count */
    public $count;

    /** @var string|null $name */
    public $name;

    /** @var float|null $price */
    public $price;

    public function __construct(array $json)
    {
        $this->count = $json['count'] ?? null;
        $this->name = $json['name'] ?? null;
        $this->price = $json['price'] ?? null;
    }
}

class LineItemRequest
{
    /** @var int|null $count */
    public $count;

    /** @var string|null $name */
    public $name;

    /** @var float|null $price */
    public $price;

    public function __construct(array $json)
    {
        $this->count = $json['count'] ?? null;
        $this->name = $json['name'] ?? null;
        $this->price = $json['price'] ?? null;
    }
}

class PossibleAdditionalChargeRequest
{
    /** @var string|null $id */
    public $id;

    /** @var int|null $count */
    public $count;

    /** @var boolean|null $isCountDirty */
    public $isCountDirty;

    public function __construct(array $json)
    {
        $this->id = $json['id'] ?? null;
        $this->count = $json['count'] ?? null;
        $this->isCountDirty = $json['isCountDirty'] ?? null;
    }
}

class RosetteRequest
{
    /** @var string|null $id */
    public $id;

    /** @var int|null $count */
    public $count;

    public function __construct(array $json)
    {
        $this->id = $json['id'] ?? null;
        $this->count = $json['count'] ?? null;
    }
}

class SpecialAccessoryRequest
{
    /** @var string|null $id */
    public $id;

    /** @var int|null $count */
    public $count;

    /** @var float|null $selectedPrice */
    public $selectedPrice;

    public function __construct(array $json)
    {
        $this->id = $json['id'] ?? null;
        $this->count = $json['count'] ?? null;
        $this->selectedPrice = $json['selectedPrice'] ?? null;
    }
}

class SpecialSurchargeRequest
{
    /** @var string|null $id */
    public $id;

    /** @var int|null $count */
    public $count;

    /** @var boolean|null $isAssemblySelected */
    public $isAssemblySelected;

    /** @var boolean|null $isAssemblySelectedDirty */
    public $isAssemblySelectedDirty;

    public function __construct(array $json)
    {
        $this->id = $json['id'] ?? null;
        $this->count = $json['count'] ?? null;
        $this->isAssemblySelected = $json['isAssemblySelected'] ?? null;
        $this->isAssemblySelectedDirty = $json['isAssemblySelectedDirty'] ?? null;
    }
}

class DoorRequest
{
    /** @var string|null $category */
    public $category;

    /** @var boolean|null $isDoorFrameEnabled */
    public $isDoorFrameEnabled;

    /** @var boolean|null $isDtdSelected */
    public $isDtdSelected;

    /** @var string|null $material */
    public $material;

    /** @var string|null $type */
    public $type;

    /** @var string|null $width */
    public $width;

    public function __construct(array $json)
    {
        $this->category = $json['category'] ?? null;
        $this->isDoorFrameEnabled = $json['isDoorFrameEnabled'] ?? null;
        $this->isDtdSelected = $json['isDtdSelected'] ?? null;
        $this->material = $json['material'] ?? null;
        $this->type = $json['type'] ?? null;
        $this->width = $json['width'] ?? null;
    }
}

class SelectedDoorLineItemRequest
{
    /** @var boolean|null $isDoorFrameEnabled */
    public $isDoorFrameEnabled;

    /** @var string|null $name */
    public $name;

    /** @var float|null $price */
    public $price;

    /** @var string|null $width */
    public $width;

    /**
     * @param array $json
     */
    public function __construct(array $json)
    {
        $this->isDoorFrameEnabled = $json['isDoorFrameEnabled'] ?? null;
        $this->name = $json['name'] ?? null;
        $this->price = $json['price'] ?? null;
        $this->width = $json['width'] ?? null;
    }
}

class PriceOfferRequest
{
    /** @var AddressRequest|null $address */
    public $address;

    /** @var int|null $assemblyDoorsCount */
    public $assemblyDoorsCount;

    /** @var int|null $assemblyPriceHandlesRosettesCount */
    public $assemblyPriceHandlesRosettesCount;

    /** @var ContactRequest|null $contact */
    public $contact;

    /** @var DoorRequest[]|null $doors */
    public $doors;

    /** @var HandleRequest|null $handle */
    public $handle;

    /** @var boolean|null $isAssemblyDoorsCountDirty */
    public $isAssemblyDoorsCountDirty;

    /** @var string|null $note */
    public $note;

    /** @var PossibleAdditionalChargeRequest[]|null $possibleAdditionalCharges */
    public $possibleAdditionalCharges = array();

    /** @var LineItemRequest[]|null $possibleAdditionalChargesLineItems */
    public $possibleAdditionalChargesLineItems = array();

    /** @var RosetteRequest[]|null $rosettes */
    public $rosettes = array();

    /** @var LineItemRequest[]|null $rosettesLineItems */
    public $rosettesLineItems = array();

    /** @var SelectedDoorLineItemRequest[]|null $selectedDoorsLineItems */
    public $selectedDoorsLineItems = array();

    /** @var SpecialAccessoryRequest[]|null $specialAccessories */
    public $specialAccessories = array();

    /** @var LineItemRequest[]|null $specialAccessoriesLineItems */
    public $specialAccessoriesLineItems = array();

    /** @var SpecialSurchargeRequest[]|null $specialSurcharges */
    public $specialSurcharges = array();

    /** @var LineItemRequest[]|null $specialSurchargesLineItems */
    public $specialSurchargesLineItems = array();

    public function __construct(array $json)
    {
        $this->address = $json['address'] ? new AddressRequest($json['address']) : null;
        $this->assemblyDoorsCount = $json['assemblyDoorsCount'] ?? null;
        $this->assemblyPriceHandlesRosettesCount = $json['assemblyPriceHandlesRosettesCount'] ?? null;
        $this->contact = $json['contact'] ? new ContactRequest($json['contact']) : null;
        $this->doors = is_array($json['doors']) ? array_map(function ($doorData) {
            return new DoorRequest($doorData);
        }, $json['doors']) : null;
        $this->handle = $json['handle'] ? new HandleRequest($json['handle']) : null;
        $this->isAssemblyDoorsCountDirty = $json['isAssemblyDoorsCountDirty'] ?? null;
        $this->note = $json['note'] ?? null;
        $this->possibleAdditionalCharges = is_array($json['possibleAdditionalCharges']) ? array_map(function ($p) {
            return new PossibleAdditionalChargeRequest($p);
        }, $json['possibleAdditionalCharges']) : null;
        $this->possibleAdditionalChargesLineItems = is_array($json['possibleAdditionalChargesLineItems']) ? array_map(function ($p) {
            return new LineItemRequest($p);
        }, $json['possibleAdditionalChargesLineItems']) : null;
        $this->rosettes = is_array($json['rosettes']) ? array_map(function ($rosetteData) {
            return new RosetteRequest($rosetteData);
        }, $json['rosettes']) : null;
        $this->rosettesLineItems = is_array($json['rosettesLineItems']) ? array_map(function ($it) {
            return new LineItemRequest($it);
        }, $json['rosettesLineItems']) : null;
        $this->selectedDoorsLineItems = is_array($json['selectedDoorsLineItems']) ? array_map(function ($it) {
            return new SelectedDoorLineItemRequest($it);
        }, $json['selectedDoorsLineItems']) : null;
        $this->specialAccessories = is_array($json['specialAccessories']) ? array_map(function ($specialAccessoryData) {
            return new SpecialAccessoryRequest($specialAccessoryData);
        }, $json['specialAccessories']) : null;
        $this->specialAccessoriesLineItems = is_array($json['specialAccessoriesLineItems']) ? array_map(function ($it) {
            return new LineItemRequest($it);
        }, $json['specialAccessoriesLineItems']) : null;
        $this->specialSurcharges = is_array($json['specialSurcharges']) ? array_map(function ($specialSurchargeData) {
            return new SpecialSurchargeRequest($specialSurchargeData);
        }, $json['specialSurcharges']) : null;
        $this->specialSurchargesLineItems = is_array($json['specialSurchargesLineItems']) ? array_map(function ($it) {
            return new LineItemRequest($it);
        }, $json['specialSurchargesLineItems']) : null;
    }
}

class ApiRequest
{
    /** @var PriceOfferRequest|null $priceOffer */
    public $priceOffer;

    public function __construct(array $json)
    {
        $this->priceOffer = $json['priceOffer'] ? new PriceOfferRequest($json['priceOffer']) : null;
    }
}
?>