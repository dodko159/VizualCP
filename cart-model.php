<?php

include_once('constants.php');
include_once('functions.php');
include_once('cart-model-api-response-objects.php');
include_once('cart-model-api-request-objects.php');
include_once('json-data-manipulation.php');

const FEE_FRAME = 99;

abstract class Width
{
    const W60 = "W60";
    const W70 = "W70";
    const W80 = "W80";
    const W90 = "W90";

    public static function getWidthString($width): string
    {
        switch ($width) {
            case Width::W60:
                return "60";
            case Width::W70:
                return "70";
            case Width::W80:
                return "80";
            case Width::W90:
                return "90";
            default:
                return "";
        }
    }

    public static function getFee($width): int
    {
        switch ($width) {
            case Width::W70:
                return 3;
            case Width::W80:
                return 6;
            case Width::W90:
                return 9;
            default:
                return 0;
        }
    }
}

class Address
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

    public function __construct()
    {
        $this->city = null;
        $this->district = null;
        $this->street = null;
        $this->streetNumber = null;
        $this->zipCode = null;
    }

    public static function fromRequest(?AddressRequest $req): Address
    {
        $instance = new self();

        if ($req == null) {
            return $instance;
        }

        $instance->city = $req->city;
        $instance->district = $req->district;
        $instance->street = $req->street;
        $instance->streetNumber = $req->streetNumber;
        $instance->zipCode = $req->zipCode;

        return $instance;
    }

    public function toResponse(): AddressResponse
    {
        return new AddressResponse(
            $this->city,
            $this->district,
            $this->street,
            $this->streetNumber,
            $this->zipCode
        );
    }
}

class Contact
{
    /** @var string|null $email */
    public $email;

    /** @var string|null $fullName */
    public $fullName;

    /** @var string|null $phoneNumber */
    public $phoneNumber;

    public function __construct()
    {
        $this->email = null;
        $this->fullName = null;
        $this->phoneNumber = null;
    }

    public static function fromRequest(?ContactRequest $req): Contact
    {
        $instance = new self();

        if ($req == null) {
            return $instance;
        }

        $instance->email = $req->email;
        $instance->fullName = $req->fullName;
        $instance->phoneNumber = $req->phoneNumber;

        return $instance;
    }

    public function toResponse(): ContactResponse
    {
        return new ContactResponse(
            $this->email,
            $this->fullName,
            $this->phoneNumber
        );
    }
}

class Handle
{
    /** @var int|null $count */
    public $count;

    /** @var string|null $name */
    public $name;

    /** @var float|null $price */
    public $price;

    public function __construct()
    {
        $this->count = null;
        $this->name = null;
        $this->price = null;
    }

    public static function fromRequest(?HandleRequest $req): Handle
    {
        $instance = new self();

        if ($req == null) {
            return $instance;
        }

        $instance->count = $req->count;
        $instance->name = $req->name;
        $instance->price = $req->price;

        return $instance;
    }

    public function toResponse(): HandleResponse
    {
        return new HandleResponse(
            $this->calculatePrice(),
            $this->count,
            $this->name,
            $this->price
        );
    }

    function calculatePrice(): float
    {
        return ($this->count ?? 0) * ($this->price ?? 0.0);
    }
}

class PossibleAdditionalCharge
{
    public static $SILICONE_ID = "possible-additional-charges-silicone-wall-frame";
    public static $SILICONE_PRICE_MULTIPLIER = 0.7;

    /** @var string|null $id */
    public $id;

    /** @var int|null $count */
    public $count;

    /** @var bool|null $isCountDirty */
    public $isCountDirty;

    public function __construct(?string $id, ?int $count, ?bool $isCountDirty)
    {
        $this->id = $id;
        $this->count = $count;
        $this->isCountDirty = $isCountDirty;
    }

    public static function fromRequest(PossibleAdditionalChargeRequest $req): PossibleAdditionalCharge
    {
        return new self(
            $req->id,
            $req->count,
            $req->isCountDirty
        );
    }

    public function toResponse(?array $possibleAdditionalChargeDb, int $doorsCount): PossibleAdditionalChargeResponse
    {
        $effectiveCount = $this->getEffectiveCount($possibleAdditionalChargeDb, $doorsCount);
        return new PossibleAdditionalChargeResponse(
            $this->id,
            $this->calculatePrice($possibleAdditionalChargeDb, $effectiveCount),
            array_key_exists("price", $possibleAdditionalChargeDb) ? $possibleAdditionalChargeDb["price"] : null,
            $effectiveCount,
            $this->isCountDirty,
            array_key_exists("header", $possibleAdditionalChargeDb) ? $possibleAdditionalChargeDb["header"] : null,
            array_key_exists("hint", $possibleAdditionalChargeDb) ? $possibleAdditionalChargeDb["hint"] : null,
            array_key_exists("imgSrc", $possibleAdditionalChargeDb) ? $possibleAdditionalChargeDb["imgSrc"] : null,
            array_key_exists("label", $possibleAdditionalChargeDb) ? $possibleAdditionalChargeDb["label"] : null,
            array_key_exists("youtubeVideoCode", $possibleAdditionalChargeDb) ? $possibleAdditionalChargeDb["youtubeVideoCode"] : null,
            array_key_exists("videoSrc", $possibleAdditionalChargeDb) ? $possibleAdditionalChargeDb["videoSrc"] : null
        );
    }

    function getEffectiveCount(?array $possibleAdditionalChargeDb, int $doorsCount): int
    {
        $setCountBasedOnDoorsCount = is_array($possibleAdditionalChargeDb) && $possibleAdditionalChargeDb["setCountBasedOnDoorsCount"];
        if (!$this->isCountDirty && $setCountBasedOnDoorsCount) {
            return $doorsCount;
        }

        return $this->count ?? 0;
    }

    function calculatePrice(?array $itemJson, int $effectiveCount): float
    {
        $coefficient = $this->id == PossibleAdditionalCharge::$SILICONE_ID ? PossibleAdditionalCharge::$SILICONE_PRICE_MULTIPLIER : 1.0;
        $rawPrice = array_key_exists("price", $itemJson) ? $itemJson["price"] : 0.0;
        return $effectiveCount * $rawPrice * $coefficient;
    }
}

class Rosette
{
    /** @var string|null $id */
    public $id;

    /** @var int|null $count */
    public $count;

    public function __construct(?string $id, ?int $count)
    {
        $this->id = $id;
        $this->count = $count;
    }

    public static function fromRequest(RosetteRequest $req): Rosette
    {
        return new self(
            $req->id,
            $req->count
        );
    }

    public function toResponse(array $rosetteDb): RosetteResponse
    {
        return new RosetteResponse(
            $this->id,
            $this->calculatePrice($rosetteDb),
            $this->count,
            array_key_exists("header", $rosetteDb) ? $rosetteDb["header"] : null,
            array_key_exists("hint", $rosetteDb) ? $rosetteDb["hint"] : null,
            array_key_exists("imgSrc", $rosetteDb) ? $rosetteDb["imgSrc"] : null,
            array_key_exists("label", $rosetteDb) ? $rosetteDb["label"] : null,
            array_key_exists("price", $rosetteDb) ? $rosetteDb["price"] : null,
            array_key_exists("youtubeVideoCode", $rosetteDb) ? $rosetteDb["youtubeVideoCode"] : null,
            array_key_exists("videoSrc", $rosetteDb) ? $rosetteDb["videoSrc"] : null
        );
    }

    function calculatePrice(?array $rosetteDb): float
    {
        $rawPrice = array_key_exists("price", $rosetteDb) ? $rosetteDb["price"] : 0.0;
        return ($this->count ?? 0) * $rawPrice;
    }
}

class LineItem
{
    /** @var int|null $count */
    public $count;

    /** @var string|null $name */
    public $name;

    /** @var float|null $price */
    public $price;

    public function __construct(?int $count, ?string $name, ?float $price)
    {
        $this->count = $count;
        $this->name = $name;
        $this->price = $price;
    }

    public static function fromRequest(LineItemRequest $req): LineItem
    {
        return new self(
            $req->count,
            $req->name,
            $req->price
        );
    }

    public function toResponse(): LineItemResponse
    {
        return new LineItemResponse(
            $this->calculatePrice(),
            $this->count,
            $this->name,
            $this->price
        );
    }

    function calculatePrice(): float
    {
        return ($this->count ?? 0.0) * ($this->price ?? 0.0);
    }
}

class SpecialAccessory
{
    /** @var string|null $id */
    public $id;

    /** @var int|null $count */
    public $count;

    /** @var float|null $selectedPrice */
    public $selectedPrice;

    public function __construct(?string $id, ?int $count, ?float $selectedPrice)
    {
        $this->id = $id;
        $this->count = $count;
        $this->selectedPrice = $selectedPrice;
    }

    public static function fromRequest(SpecialAccessoryRequest $req): SpecialAccessory
    {
        return new self(
            $req->id,
            $req->count,
            $req->selectedPrice
        );
    }

    public function toResponse(array $specialAccessoryDb): SpecialAccessoryResponse
    {
        return new SpecialAccessoryResponse(
            $this->id,
            $this->calculatePrice($specialAccessoryDb),
            array_key_exists("price", $specialAccessoryDb) ? $specialAccessoryDb["price"] : null,
            $this->count,
            array_key_exists("header", $specialAccessoryDb) ? $specialAccessoryDb["header"] : null,
            array_key_exists("hint", $specialAccessoryDb) ? $specialAccessoryDb["hint"] : null,
            array_key_exists("imgSrc", $specialAccessoryDb) ? $specialAccessoryDb["imgSrc"] : null,
            array_key_exists("label", $specialAccessoryDb) ? $specialAccessoryDb["label"] : null,
            $this->selectedPrice,
            array_key_exists("youtubeVideoCode", $specialAccessoryDb) ? $specialAccessoryDb["youtubeVideoCode"] : null,
            array_key_exists("videoSrc", $specialAccessoryDb) ? $specialAccessoryDb["videoSrc"] : null
        );
    }

    function calculatePrice(array $specialAccessoryDb): float
    {
        $priceDb = array_key_exists("price", $specialAccessoryDb) ? $specialAccessoryDb["price"] : null;
        $price = $priceDb ?? $this->selectedPrice ?? 0.0;
        return ($this->count ?? 0) * $price;
    }
}

class SpecialSurcharge
{
    /** @var string|null $id */
    public $id;

    /** @var int|null $count */
    public $count;

    /** @var bool|null $isAssemblySelected */
    public $isAssemblySelected;

    /** @var bool|null $isAssemblySelectedDirty */
    public $isAssemblySelectedDirty;

    public function __construct(?string $id, ?int $count, ?bool $isAssemblySelected, ?bool $isAssemblySelectedDirty)
    {
        $this->id = $id;
        $this->count = $count;
        $this->isAssemblySelected = $isAssemblySelected;
        $this->isAssemblySelectedDirty = $isAssemblySelectedDirty;
    }

    public static function fromRequest(SpecialSurchargeRequest $req): SpecialSurcharge
    {
        return new self(
            $req->id,
            $req->count,
            SpecialSurcharge::getEffectiveIsAssemblySelected($req->count, $req->isAssemblySelected, $req->isAssemblySelectedDirty),
            $req->isAssemblySelectedDirty
        );
    }

    public function toResponse(?array $specialSurchargeDb): SpecialSurchargeResponse
    {
        return new SpecialSurchargeResponse(
            $this->id,
            $this->calculatePrice($specialSurchargeDb),
            array_key_exists("price", $specialSurchargeDb) ? $specialSurchargeDb["price"] : null,
            $this->count,
            array_key_exists("header", $specialSurchargeDb) ? $specialSurchargeDb["header"] : null,
            array_key_exists("hint", $specialSurchargeDb) ? $specialSurchargeDb["hint"] : null,
            array_key_exists("imgSrc", $specialSurchargeDb) ? $specialSurchargeDb["imgSrc"] : null,
            SpecialSurcharge::getEffectiveIsAssemblySelected($this->count, $this->isAssemblySelected, $this->isAssemblySelectedDirty),
            $this->isAssemblySelectedDirty,
            array_key_exists("label", $specialSurchargeDb) ? $specialSurchargeDb["label"] : null,
            array_key_exists("labelAssembly", $specialSurchargeDb) ? $specialSurchargeDb["labelAssembly"] : null,
            array_key_exists("youtubeVideoCode", $specialSurchargeDb) ? $specialSurchargeDb["youtubeVideoCode"] : null,
            array_key_exists("videoSrc", $specialSurchargeDb) ? $specialSurchargeDb["videoSrc"] : null
        );
    }

    function calculatePrice(array $specialSurchargeDb): float
    {
        $assemblyCosts = array_key_exists("assemblyCosts", $specialSurchargeDb) ? $specialSurchargeDb["assemblyCosts"] : 0.0;
        $assemblyPrice = $this->isAssemblySelected ? $assemblyCosts : 0.0;
        $price = array_key_exists("price", $specialSurchargeDb) ? $specialSurchargeDb["price"] : 0.0;
        return ($this->count ?? 0) * ($price + $assemblyPrice);
    }

    static function getEffectiveIsAssemblySelected(?int $count, ?bool $isAssemblySelected, ?bool $isAssemblySelectedDirty): bool
    {
        if ($count == null) {
            return false;
        }

        return $isAssemblySelectedDirty
            ? $isAssemblySelected
            : true;
    }
}

class Door
{
    const FEE_DTD = 30;
    const FEE_FRAME_OFFER = 80;

    /** @var string|null $category */
    public $category;

    /** @var string|null $type */
    public $type;

    /** @var string|null $material */
    public $material;

    /** @var string|null $width */
    public $width;

    /** @var int|null $count */
    public $count;

    /** @var float|null $price */
    public $price;

    /** @var string|null $info */
    public $info;

    /** @var boolean|null $frame */
    public $frame;

    /** @var boolean|null $assembly */
    public $assembly;

    /** @var boolean|null $isDtdSelected */
    public $isDtdSelected;

    function __construct(?string $c, ?string $t, ?string $m, ?string $w, ?int $cn, ?string $nfo, ?bool $frame,
                         ?bool   $assembly, ?bool $isDtdSelected)
    {
        $this->category = $c;
        $this->isDtdSelected = $isDtdSelected;
        $this->type = $t;
        $this->material = $m;
        $this->width = $w;
        $this->count = $cn;
        $this->info = $nfo;
        $this->price = $this->getRawPrice($t);
        $this->frame = $frame;
        $this->assembly = $assembly;
    }

    public static function fromRequest(DoorRequest $req): Door
    {
        return new self(
            $req->category,
            $req->type,
            $req->material,
            $req->width,
            1,
            null,
            $req->isDoorFrameEnabled,
            null,
            $req->isDtdSelected
        );
    }

    public function toResponse(): DoorResponse
    {
        return new DoorResponse(
            $this->calculatePrice(),
            $this->category,
            $this->frame,
            $this->isDtdAvailable(),
            $this->isDtdSelected,
            $this->material,
            $this->type,
            $this->width
        );
    }

    //deprecated after full rewrite
    function isWidthSelectedText(string $mWidth): string
    {
        if ($this->width == $mWidth) {
            return "selected";
        }

        return "";
    }

    //deprecated after full rewrite
    function getFullPrice()
    {
        global $cena_zarubne, $cena_zarubne_akcia, $cena_montaze;
        $frame = 0;
        $assembly = 0;
        if ($this->frame) {
            $frame = $cena_zarubne;
            if (strcasecmp($this->type, "v1") == 0) {
                $frame = $cena_zarubne_akcia;
            }
        }
        if ($this->assembly) {
            $assembly = $cena_montaze;
        }
        return $this->count * ($this->price + $frame + $assembly);
    }

    function calculatePrice(): float
    {
        $rawPrice = $this->getRawPrice($this->type);

        if ($this->frame) {
            $rawPrice = $rawPrice + ($this->type == "v1" ? Door::FEE_FRAME_OFFER : FEE_FRAME);//extra fee for frame
        }

        $rawPrice = $rawPrice + Width::getFee($this->width);//extra fee for width
        $rawPrice = $rawPrice + ($this->isDtdSelected ? Door::FEE_DTD : 0);//extra fee for DTD
        return ($this->count ?? 0) * $rawPrice;
    }

    function isDtdAvailable(): bool
    {
        return $this->type == "v1" || in_array($this->category, array("Petra", "Vanesa"));
    }

    function getWidthString(): string
    {
        return Width::getWidthString($this->width);
    }

    function getRawPrice(?string $type)
    {
        $price = 0;

        try {
            $prices = DoorsJsonDataManipulation::getAllByCategory($this->category);

            if (array_key_exists($type, $prices)) {
                $price = $prices[$type];
            }
        } catch (Exception $e) {
        }

        return $price;
    }

    //deprecated after full rewrite
    function changeValueOf($fc, $value): void
    {
        switch ($fc) {
            case "changeWidth":
                $this->width = $value;
                break;
            case "changeCount":
                $this->count = $value;
                break;
            case "changeInfo":
                $this->info = $value;
                break;
            case "changeFrame":
                $this->frame = (bool)$value;
                break;
            case "changeAssemble":
                $this->assembly = (bool)$value;
                break;
        }
    }

    //deprecated after full rewrite
    function getHTMLdoor($idx)
    {
        global $material_path, $doors_path, $povrchova_uprava, $typ_dveri, $currency;

        ob_start();
        include 'cart-item.php';
        $data = ob_get_contents();
        ob_end_clean();

        return $data;
    }

    //deprecated after full rewrite
    function getHTMLdoorRes($idx): array
    {
        $html = "";
        try {
            $html = $this->getHTMLdoor($idx);
        } catch (Exception $e) {
            return array(
                'sucess' => false,
                'message' => "Chyba zobrazenia. Prosím refreshujte stránku použitím tlačidla F5.",
                'result' => $e->getMessage()
            );
        }

        return array(
            'sucess' => true,
            'message' => "OK",
            'result' => $html
        );
    }
}

class SelectedDoorLineItem
{
    /** @var bool|null $isDoorFrameEnabled */
    public $isDoorFrameEnabled;

    /** @var string|null $name */
    public $name;

    /** @var float|null $price */
    public $price;

    /** @var string|null $width */
    public $width;

    function __construct(?bool $isDoorFrameEnabled, ?string $name, ?float $price, ?string $width)
    {
        $this->isDoorFrameEnabled = $isDoorFrameEnabled;
        $this->name = $name;
        $this->price = $price;
        $this->width = $width;
    }

    public static function fromRequest(SelectedDoorLineItemRequest $req): SelectedDoorLineItem
    {
        return new self(
            $req->isDoorFrameEnabled,
            $req->name,
            $req->price,
            $req->width
        );
    }

    public function toResponse(): SelectedDoorLineItemResponse
    {
        return new SelectedDoorLineItemResponse(
            $this->calculatePrice(),
            $this->isDoorFrameEnabled,
            $this->name,
            $this->price,
            $this->width
        );
    }

    function calculatePrice(): float
    {
        $effectivePrice = $this->price ?? 0.0;
        if ($this->isDoorFrameEnabled) {
            $effectivePrice = $effectivePrice + FEE_FRAME;//extra fee for frame
        }

        //extra fee for width
        return $effectivePrice + Width::getFee($this->width);
    }
}

class PriceOffer
{
    const ASSEMBLY_DOORS_COST = 35;
    const ASSEMBLY_DOORS_COST_MIN = 210;
    const ASSEMBLY_PRICE_HANDLES_ROSETTES = 5;
    const DELIVERY_PRICE_PER_KM = 0.5;
    const VAT = 1.23;

    /** @var Address $address */
    public $address;
    /** @var int|null $assemblyPriceHandlesRosettesCount */
    public $assemblyPriceHandlesRosettesCount;
    /** @var int|null $assemblyDoorsCount */
    public $assemblyDoorsCount;
    /** @var Door[] $doors */
    public $doors;
    /** @var Contact $contact */
    public $contact;
    /** @var Handle $handle */
    public $handle;
    /** @var bool|null $isAssemblyDoorsCountDirty */
    public $isAssemblyDoorsCountDirty;
    /** @var string|null $note */
    public $note;
    /** @var PossibleAdditionalCharge[] $possibleAdditionalCharges */
    public $possibleAdditionalCharges;
    /** @var LineItem[] $possibleAdditionalChargesLineItems */
    public $possibleAdditionalChargesLineItems;
    /** @var SelectedDoorLineItem[] $selectedDoorsLineItems */
    public $selectedDoorsLineItems;
    /** @var Rosette[] $selectedRosettes */
    public $selectedRosettes;
    /** @var LineItem[] $rosettesLineItems */
    public $rosettesLineItems;
    /** @var SpecialAccessory[] $specialAccessories */
    public $specialAccessories;
    /** @var LineItem[] $specialAccessoriesLineItems */
    public $specialAccessoriesLineItems;
    /** @var SpecialSurcharge[] $specialSurcharges */
    public $specialSurcharges;
    /** @var LineItem[] $specialSurchargesLineItems */
    public $specialSurchargesLineItems;
    public $assembly;   // deprecated after full rewrite
    public $seal;       // deprecated after full rewrite
    public $putty;      // deprecated after full rewrite
    public $ironFrame;  // deprecated after full rewrite
    public $floor3;     // deprecated after full rewrite
    public $thickerFrame; // deprecated after full rewrite
    public $higherFrame; // deprecated after full rewrite
    public $distance; // deprecated after full rewrite
    public $doorLiners; // deprecated after full rewrite

    function __construct()
    {
        $this->address = new Address();
        $this->assemblyDoorsCount = null;
        $this->assemblyPriceHandlesRosettesCount = null;
        $this->contact = new Contact();
        $this->doors = array();
        $this->handle = new Handle();
        $this->isAssemblyDoorsCountDirty = null;
        $this->note = null;
        $this->possibleAdditionalCharges = array();
        $this->possibleAdditionalChargesLineItems = array();
        $this->selectedDoorsLineItems = array();
        $this->selectedRosettes = array();
        $this->rosettesLineItems = array();
        $this->specialAccessories = array();
        $this->specialAccessoriesLineItems = array();
        $this->specialSurcharges = array();
        $this->specialSurchargesLineItems = array();
    }

    public static function fromSession(PriceOffer $session): PriceOffer
    {
        $instance = new self();
        $instance->address = $session->address ?? new Address();
        $instance->assemblyPriceHandlesRosettesCount = $session->assemblyPriceHandlesRosettesCount ?? null;
        $instance->assemblyDoorsCount = $session->assemblyDoorsCount ?? null;
        $instance->contact = $session->contact ?? new Contact();
        $instance->doors = $session->doors ?? array();
        $instance->handle = $session->handle ?? new Handle();
        $instance->isAssemblyDoorsCountDirty = $session->isAssemblyDoorsCountDirty ?? null;
        $instance->note = $session->note ?? null;
        $instance->possibleAdditionalCharges = $session->possibleAdditionalCharges ?? array();
        $instance->possibleAdditionalChargesLineItems = $session->possibleAdditionalChargesLineItems ?? array();
        $instance->selectedDoorsLineItems = $session->selectedDoorsLineItems ?? array();
        $instance->selectedRosettes = $session->selectedRosettes ?? array();
        $instance->rosettesLineItems = $session->rosettesLineItems ?? array();
        $instance->specialAccessories = $session->specialAccessories ?? array();
        $instance->specialAccessoriesLineItems = $session->specialAccessoriesLineItems ?? array();
        $instance->specialSurcharges = $session->specialSurcharges ?? array();
        $instance->specialSurchargesLineItems = $session->specialSurchargesLineItems ?? array();

        return $instance;
    }

    public static function fromRequest(PriceOfferRequest $req): PriceOffer
    {
        $instance = new self();
        $instance->address = Address::fromRequest($req->address);
        $instance->assemblyDoorsCount = $req->assemblyDoorsCount;
        $instance->assemblyPriceHandlesRosettesCount = $req->assemblyPriceHandlesRosettesCount;
        $instance->contact = Contact::fromRequest($req->contact);

        $instance->doors = is_array($req->doors) ? array_map(function (DoorRequest $value): Door {
            return Door::fromRequest($value);
        }, $req->doors) : array();

        $instance->handle = Handle::fromRequest($req->handle);
        $instance->isAssemblyDoorsCountDirty = $req->isAssemblyDoorsCountDirty;
        $instance->note = $req->note;

        $instance->possibleAdditionalCharges = is_array($req->possibleAdditionalCharges) ? array_map(function (PossibleAdditionalChargeRequest $value): PossibleAdditionalCharge {
            return PossibleAdditionalCharge::fromRequest($value);
        }, $req->possibleAdditionalCharges) : array();

        $instance->possibleAdditionalChargesLineItems = is_array($req->possibleAdditionalChargesLineItems) ? array_map(function (LineItemRequest $value): LineItem {
            return LineItem::fromRequest($value);
        }, $req->possibleAdditionalChargesLineItems) : array();

        $instance->selectedRosettes = is_array($req->rosettes) ? array_map(function (RosetteRequest $value): Rosette {
            return Rosette::fromRequest($value);
        }, $req->rosettes) : array();

        $instance->rosettesLineItems = is_array($req->rosettesLineItems) ? array_map(function (LineItemRequest $value): LineItem {
            return LineItem::fromRequest($value);
        }, $req->rosettesLineItems) : array();

        $instance->selectedDoorsLineItems = is_array($req->selectedDoorsLineItems) ? array_map(function (SelectedDoorLineItemRequest $value): SelectedDoorLineItem {
            return SelectedDoorLineItem::fromRequest($value);
        }, $req->selectedDoorsLineItems) : array();

        $instance->specialAccessories = is_array($req->specialAccessories) ? array_map(function (SpecialAccessoryRequest $value): SpecialAccessory {
            return SpecialAccessory::fromRequest($value);
        }, $req->specialAccessories) : array();

        $instance->specialAccessoriesLineItems = is_array($req->specialAccessoriesLineItems) ? array_map(function (LineItemRequest $value): LineItem {
            return LineItem::fromRequest($value);
        }, $req->specialAccessoriesLineItems) : array();

        $instance->specialSurcharges = is_array($req->specialSurcharges) ? array_map(function (SpecialSurchargeRequest $value): SpecialSurcharge {
            return SpecialSurcharge::fromRequest($value);
        }, $req->specialSurcharges) : array();

        $instance->specialSurchargesLineItems = is_array($req->specialSurchargesLineItems) ? array_map(function (LineItemRequest $value): LineItem {
            return LineItem::fromRequest($value);
        }, $req->specialSurchargesLineItems) : array();

        return $instance;
    }

    public function toResponse(): PriceOfferResponse
    {
        $doorsCount = $this->getDoorNumber();
        $assemblyDoorsEffectiveCount = $this->getEffectiveCountIsAssemblyDoorsCount($doorsCount);
        $assemblyDoorsCalculatedPrice = $this->calculateDoorsAssemblyCosts($assemblyDoorsEffectiveCount) ?? 0.0;
        $possibleAdditionalChargesCalculatedPrice = $this->calculatePossibleAdditionalChargesPrice($doorsCount);
        $price = $this->calculatePrice(
            $assemblyDoorsCalculatedPrice,
            $possibleAdditionalChargesCalculatedPrice
        );

        return new PriceOfferResponse(
            $this->address ? $this->address->toResponse() : AddressResponse::empty(),
            $assemblyDoorsCalculatedPrice,
            $assemblyDoorsEffectiveCount,
            $this->assemblyPriceHandlesRosettesCount,
            $this->calculateAssemblyPriceHandlesRosettes(),
            $price,
            $this->calculatePriceVat($price),
            $this->contact ? $this->contact->toResponse() : ContactResponse::empty(),
            $this->calculateDeliveryCosts(),
            array_map(function (Door $door): DoorResponse {
                return $door->toResponse();
            }, $this->doors),
            $this->handle ? $this->handle->toResponse() : HandleResponse::empty(),
            $this->isAssemblyDoorsCountDirty,
            $this->note,
            mapPossibleAdditionalChargesToResponse($this->possibleAdditionalCharges, $doorsCount),
            array_map(function (LineItem $it): LineItemResponse {
                return $it->toResponse();
            }, $this->possibleAdditionalChargesLineItems),
            mapRosettesToResponse($this->selectedRosettes),
            array_map(function (LineItem $it): LineItemResponse {
                return $it->toResponse();
            }, $this->rosettesLineItems),
            new SectionsCalculatedPriceResponse(
                $this->calculateDoorsPrice(),//doors
                $this->calculateHandlesAndRosettesPrice(),//handlesAndRosettes
                $this->calculateDeliveryCosts(),//delivery
                $assemblyDoorsCalculatedPrice,//assemblyDoors
                $this->calculateSpecialAccessoriesPrice(),//specialAccessories
                $possibleAdditionalChargesCalculatedPrice,//possibleAdditionalCharges
                $this->calculateSpecialSurchargesPrice()//specialSurcharges
            ),
            array_map(function (SelectedDoorLineItem $it): SelectedDoorLineItemResponse {
                return $it->toResponse();
            }, $this->selectedDoorsLineItems),
            mapSpecialAccessoriesToResponse($this->specialAccessories),
            array_map(function (LineItem $it): LineItemResponse {
                return $it->toResponse();
            }, $this->specialAccessoriesLineItems),
            mapSpecialSurchargesToResponse($this->specialSurcharges),
            array_map(function (LineItem $it): LineItemResponse {
                return $it->toResponse();
            }, $this->specialSurchargesLineItems),
        );
    }

    function getEffectiveCountIsAssemblyDoorsCount(?int $doorsCount): int
    {
        if (!$this->isAssemblyDoorsCountDirty) {
            return $doorsCount;
        }

        return $this->assemblyDoorsCount ?? 0;
    }

    //deprecated after full rewrite
    function getDistance(): float
    {
        $dist = 0;
        if ($this->distance != null && $this->distance > 0) {
            $dist = $this->distance;
        }
        return $dist;
    }

    //deprecated after full rewrite
    function getDoorLiners(): float
    {
        $dist = 0;
        if ($this->doorLiners != null && $this->doorLiners > 0) {
            $dist = $this->doorLiners;
        }
        return $dist;
    }

    //deprecated after full rewrite
    function getDoorNumber(): float
    {
        $count = 0;
        if (!empty($this->doors)) {
            foreach ($this->doors as $door) {
                $count += $door->count;
            }
        }
        return $count;
    }

    //deprecated after full rewrite
    function getFullPriceNoAdd(): float
    {
        $price = 0;
        if (!empty($this->doors)) {
            foreach ($this->doors as $door) {
                $price += $door->getFullPrice();
            }
        }
        return $price;
    }

    function calculateDoorsAssemblyCosts(?int $assemblyDoorsCount): float
    {
        if ($assemblyDoorsCount != null && $assemblyDoorsCount > 0) {
            return max(
                $assemblyDoorsCount * PriceOffer::ASSEMBLY_DOORS_COST,
                PriceOffer::ASSEMBLY_DOORS_COST_MIN
            );
        }

        return 0.0;
    }

    function calculateDeliveryCosts(): float
    {
        if ($this->address) {
            $config = DistrictsJsonDataManipulation::findByIdOrFalse($this->address->district);
            return $config ? (2 * $config["distance"] * PriceOffer::DELIVERY_PRICE_PER_KM) : 0.0;
        }

        return 0.0;
    }

    function calculateDoorsPrice(): float
    {
        $doorsPrice = array_sum(array_map(function ($door): float {
            return $door->calculatePrice();
        }, $this->doors ?? array()));

        $selectedDoorsLineItemsPrice = array_sum(array_map(function ($it): float {
            return $it->calculatePrice();
        }, $this->selectedDoorsLineItems ?? array()));

        return $doorsPrice + $selectedDoorsLineItemsPrice;
    }

    function calculateHandlesAndRosettesPrice(): float
    {
        $handlePrice = $this->handle->calculatePrice();

        $rosettesPrice = array_sum(array_map(function ($rosette): float {
            return $rosette->calculatePrice(RosettesJsonDataManipulation::findByIdOrFalse($rosette->id));
        }, $this->selectedRosettes));
        $rosettesLineItemsPrice = array_sum(array_map(function ($it): float {
            return $it->calculatePrice();
        }, $this->rosettesLineItems));

        $assemblyPriceHandlesRosettes = $this->calculateAssemblyPriceHandlesRosettes();

        return $handlePrice + $rosettesPrice + $rosettesLineItemsPrice + $assemblyPriceHandlesRosettes;
    }

    function calculatePossibleAdditionalChargesPrice(int $doorsCount): float
    {
        $chargesTotal = array_sum(array_map(
            function (PossibleAdditionalCharge $charge) use ($doorsCount): float {
                $possibleAdditionalChargeDb = PossibleAdditionalChargesJsonDataManipulation::findByIdOrFalse($charge->id);
                $effectiveCount = $charge->getEffectiveCount($possibleAdditionalChargeDb, $doorsCount);
                return $charge->calculatePrice(
                    $possibleAdditionalChargeDb,
                    $effectiveCount
                );
            }, $this->possibleAdditionalCharges ?? array()
        ));

        $lineItemsTotal = array_sum(array_map(
            function ($item): float {
                return $item->calculatePrice();
            }, $this->possibleAdditionalChargesLineItems ?? array()
        ));

        return $chargesTotal + $lineItemsTotal;
    }

    function calculateSpecialAccessoriesPrice(): float
    {
        $specialAccessoriesTotal = array_sum(array_map(
            function ($specialAccessory): float {
                return $specialAccessory->calculatePrice(
                    SpecialAccessoriesJsonDataManipulation::findByIdOrFalse($specialAccessory->id)
                );
            },
            $this->specialAccessories
        ));

        $lineItemsTotal = array_sum(array_map(
            function ($item): float {
                return $item->calculatePrice();
            },
            $this->specialAccessoriesLineItems
        ));

        return $specialAccessoriesTotal + $lineItemsTotal;
    }

    function calculateSpecialSurchargesPrice(): float
    {
        $specialSurchargesTotal = array_sum(array_map(
            function ($specialSurcharge): float {
                return $specialSurcharge->calculatePrice(
                    SpecialSurchargesJsonDataManipulation::findByIdOrFalse($specialSurcharge->id)
                );
            },
            $this->specialSurcharges
        ));

        $lineItemsTotal = array_sum(array_map(
            function ($item): float {
                return $item->calculatePrice();
            },
            $this->specialSurchargesLineItems
        ));

        return $specialSurchargesTotal + $lineItemsTotal;
    }

    function calculatePrice(
        float $assemblyDoorsCalculatedPrice,
        float $possibleAdditionalChargesCalculatedPrice
    ): float
    {
        return $this->calculateDoorsPrice() +
            $this->calculateHandlesAndRosettesPrice() +
            $assemblyDoorsCalculatedPrice +
            $this->calculateDeliveryCosts() +
            $this->calculateSpecialAccessoriesPrice() +
            $possibleAdditionalChargesCalculatedPrice +
            $this->calculateSpecialSurchargesPrice();
    }

    function calculatePriceVat(float $priceNoVat): float
    {
        return $priceNoVat * PriceOffer::VAT;
    }

    function calculateAssemblyPriceHandlesRosettes()
    {
        return ($this->assemblyPriceHandlesRosettesCount ?? 0) * PriceOffer::ASSEMBLY_PRICE_HANDLES_ROSETTES;
    }

    //deprecated after full rewrite
    function getAssemblyPrice(): float
    {
        global $cena_montaze;
        $price = 0;
        if ($this->assembly) {
            $price += $this->getDoorNumber() * $cena_montaze;
        }
        //return $price; zrusene
        return 0;
    }

    //deprecated after full rewrite
    function getSealPrice()
    {
        global $cena_tesnenia;
        $price = 0;
        if ($this->seal) {
            $price += $this->getDoorNumber() * $cena_tesnenia;
        }
        return $price;
    }

    //deprecated after full rewrite
    function getPuttyPrice()
    {
        global $cena_tmelenia;
        $price = 0;
        if ($this->putty) {
            $price += $this->getDoorNumber() * $cena_tmelenia;
        }
        return $price;
    }

    //deprecated after full rewrite
    function getIronPrice()
    {
        global $cena_obkladu_zarubne;
        $price = 0;
        if ($this->ironFrame) {
            $price += $this->getDoorNumber() * $cena_obkladu_zarubne;
        }
        return $price;
    }

    //deprecated after full rewrite
    function getFloor3Price()
    {
        global $cena_vynasania;
        $price = 0;
        if ($this->floor3) {
            $price += $this->getDoorNumber() * $cena_vynasania;
        }
        return $price;
    }

    //deprecated after full rewrite
    function getThickerFramePrice()
    {
        global $cena_priplatok_hrubsia_zaruben;
        $price = 0;
        if ($this->thickerFrame != null && $this->thickerFrame > 0) {
            $price += $this->thickerFrame * $cena_priplatok_hrubsia_zaruben;
        }
        return $price;
    }

    //deprecated after full rewrite
    function getHigherFramePrice()
    {
        global $cena_priplatok_vyssia_zaruben;
        $price = 0;
        if ($this->higherFrame != null && $this->higherFrame > 0) {
            $price += $this->higherFrame * $cena_priplatok_vyssia_zaruben;
        }
        return $price;
    }

    //deprecated after full rewrite
    function getDistancePrice()
    {
        global $cena_km;
        $price = 0;
        if ($this->distance != null && $this->distance > 0) {
            $price += $this->distance * $cena_km;
        }
        return $price;
    }

    //deprecated after full rewrite
    function getLinerPrice()
    { // oblozky
        global $cena_zarubne;
        $price = 0;
        if ($this->doorLiners != null && $this->doorLiners > 0) {
            $price += $this->doorLiners * $cena_zarubne;
        }
        return $price;
    }

    //deprecated after full rewrite
    function getFullPrice()
    {
        global $cena_montaze, $cena_tesnenia;

        $price = 0;
        if (!empty($this->doors)) {
            foreach ($this->doors as $door) {
                $price += $door->getFullPrice();
            }
            $price += $this->getAssemblyPrice();
            $price += $this->getSealPrice();
            $price += $this->getPuttyPrice();
            $price += $this->getIronPrice();
            $price += $this->getFloor3Price();
            $price += $this->getThickerFramePrice();
            $price += $this->getHigherFramePrice();
            $price += $this->getDistancePrice();
            $price += $this->getLinerPrice();
        }
        return $price;
    }

    //deprecated after full rewrite
    function getPriceOf($id): float
    {
        if (array_key_exists($id, $this->doors)) {
            $door = $this->doors[$id];
            return $door->getFullPrice();
        } else {
            throw new Exception("Bad ID.");
        }
    }

    //deprecated after full rewrite
    function addDoor($door)
    {
        $idx = 1;
        try {
            if (empty($this->doors)) {
                $this->doors = [$idx => $door];
            } else {
                $idx = max(array_keys($this->doors)) + 1;
                $this->doors[$idx] = $door;
            }
        } catch (Exception $e) {
            return array(
                'sucess' => false,
                'message' => "Chyba pri ukladaní. Dvere neboli uložené. Prosím refreshujte stránku použitím tlačidla F5.",
                'result' => $e->getMessage()
            );
        }

        return end($this->doors)->getHTMLdoorRes($idx);
    }

    //deprecated after full rewrite
    function removeDoorAtPosition($key): array
    {
        try {
            unset($this->doors[$key]);
        } catch (Exception $e) {
            return array(
                'sucess' => false,
                'message' => "Chyba pri mazaní položky",
                'result' => $e->getMessage()
            );
        }
        return sucessResult();
    }

    //deprecated after full rewrite
    function cloneDoorAtPosition($key)
    {
        try {
            $door = $this->doors[$key];
            $cloneDoor = clone $door;
        } catch (Exception $e) {
            return array(
                'sucess' => false,
                'message' => "Chyba pri kopírovaní",
                'result' => $e->getMessage()
            );
        }

        return $this->addDoor($cloneDoor);
    }

    //deprecated after full rewrite
    function changeValueOf($fc, $id, $value): array
    {
        try {
            $door = $this->doors[$id];
            $door->changeValueOf($fc, $value);
        } catch (Exception $e) {
            return array(
                'sucess' => false,
                'message' => "Chyba pri úprave hodnôt",
                'result' => $e->getMessage()
            );
        }

        return sucessResult();
    }

    //deprecated after full rewrite
    function setAssemblyRes($value): array
    {
        try {
            $this->assembly = $value;
        } catch (Exception $e) {
            return array(
                'sucess' => false,
                'message' => "Chyba pri úprave hodnôt",
                'result' => $e->getMessage()
            );
        }

        return sucessResult();
    }

    //deprecated after full rewrite
    function setSealRes($value): array
    {
        try {
            $this->seal = $value;
        } catch (Exception $e) {
            return array(
                'sucess' => false,
                'message' => "Chyba pri úprave hodnôt",
                'result' => $e->getMessage()
            );
        }

        return sucessResult();
    }

    //deprecated after full rewrite
    function setPuttyRes($value): array
    {
        try {
            $this->putty = $value;
        } catch (Exception $e) {
            return array(
                'sucess' => false,
                'message' => "Chyba pri úprave hodnôt",
                'result' => $e->getMessage()
            );
        }

        return sucessResult();
    }

    //deprecated after full rewrite
    function setIronFrameRes($value): array
    {
        try {
            $this->ironFrame = $value;
        } catch (Exception $e) {
            return array(
                'sucess' => false,
                'message' => "Chyba pri úprave hodnôt",
                'result' => $e->getMessage()
            );
        }

        return sucessResult();
    }

    //deprecated after full rewrite
    function setFloor3Res($value): array
    {
        try {
            $this->floor3 = $value;
        } catch (Exception $e) {
            return array(
                'sucess' => false,
                'message' => "Chyba pri úprave hodnôt",
                'result' => $e->getMessage()
            );
        }

        return sucessResult();
    }

    //deprecated after full rewrite
    function setThickerFrameRes($value): array
    {
        try {
            $this->thickerFrame = $value;
        } catch (Exception $e) {
            return array(
                'sucess' => false,
                'message' => "Chyba pri úprave hodnôt",
                'result' => $e->getMessage()
            );
        }

        return sucessResult();
    }

    //deprecated after full rewrite
    function setHigherFrameRes($value): array
    {
        try {
            $this->higherFrame = $value;
        } catch (Exception $e) {
            return array(
                'sucess' => false,
                'message' => "Chyba pri úprave hodnôt",
                'result' => $e->getMessage()
            );
        }

        return sucessResult();
    }

    //deprecated after full rewrite
    function setDistanceRes($value): array
    {
        try {
            $this->distance = $value;
        } catch (Exception $e) {
            return array(
                'sucess' => false,
                'message' => "Chyba pri úprave hodnôt",
                'result' => $e->getMessage()
            );
        }

        return sucessResult();
    }

    //deprecated after full rewrite
    function setDoorLinersRes($value): array
    {
        try {
            $this->doorLiners = $value;
        } catch (Exception $e) {
            return array(
                'sucess' => false,
                'message' => "Chyba pri úprave hodnôt",
                'result' => $e->getMessage()
            );
        }

        return sucessResult();
    }

    //deprecated after full rewrite
    function doPostFunction($POST)
    {
        switch ($POST['function']) {
            case "remove":
                if (isset($POST['position'])) {
                    return $this->removeDoorAtPosition($POST['position']);
                }
                break;
            case "clone":
                if (isset($POST['position'])) {
                    return $this->cloneDoorAtPosition($POST['position']);
                }
                break;
            case "changeWidth":
            case "changeCount":
            case "changeInfo":
            case "changeFrame":
            case "changeAssemble":
                if (isset($POST['position']) && isset($POST['newValue'])) {
                    return $this->changeValueOf($POST['function'], $POST['position'], $POST['newValue']);
                }
                break;
            case "setAssembly":
                if (isset($POST['newValue'])) {
                    return $this->setAssemblyRes($POST['newValue']);
                }
                break;
            case "setSeal":
                if (isset($POST['newValue'])) {
                    return $this->setSealRes($POST['newValue']);
                }
                break;
            case "setPutty":
                if (isset($POST['newValue'])) {
                    return $this->setPuttyRes($POST['newValue']);
                }
                break;
            case "setIronFrame":
                if (isset($POST['newValue'])) {
                    return $this->setIronFrameRes($POST['newValue']);
                }
                break;
            case "setFloor3":
                if (isset($POST['newValue'])) {
                    return $this->setFloor3Res($POST['newValue']);
                }
                break;
            case "setThickerFrame":
                if (isset($POST['newValue'])) {
                    return $this->setThickerFrameRes($POST['newValue']);
                }
                break;
            case "setHigherFrame":
                if (isset($POST['newValue'])) {
                    return $this->setHigherFrameRes($POST['newValue']);
                }
                break;
            case "setDistance":
                if (isset($POST['newValue'])) {
                    return $this->setDistanceRes($POST['newValue']);
                }
                break;
            case "setDoorLiners":
                if (isset($POST['newValue'])) {
                    return $this->setDoorLinersRes($POST['newValue']);
                }
                break;
        }
        //ak nic nevolalo
        return array(
            'sucess' => false,
            'message' => "Chyba volania",
            'result' => "Error. Unknown function or out of index."
        );
    }
}

//deprecated after full rewrite
function sucessResult(): array
{
    return array(
        'sucess' => true,
        'message' => "OK",
        'result' => "OK"
    );
}

//deprecated after full rewrite
function fixObject(&$object)
{
    if (!is_object($object) && gettype($object) == 'object')
        return ($object = unserialize(serialize($object)));
    return $object;
}

//deprecated after full rewrite
function getCategoryFromDoorType(?string $typ): string
{
    if ($typ === null || $typ === '') {
        return "";
    }

    $typ = strtoupper($typ);

    // 1️⃣ Wien detection (W1, W2, W3 anywhere in the ID)
    if (preg_match('/W([123])/', $typ, $matches)) {
        return 'Wien_' . $matches[1];
    }

    // 2️⃣ Fallback: first-letter category mapping
    switch ($typ[0]) {
        case "A":
            return "Alica";
        case "K":
            return "Kristina";
        case "O":
            return "Ornela";
        case "P":
            return "Petra";
        case "S":
            return "Simona";
        case "V":
            return "Vanesa";
        case "Z":
            return "Zuzana";
        case "R":
            return "Renata";
        case "B":
            return "Barbora";
        case "G":
            return "Greta";
        default:
            return "";
    }
}

function mapPossibleAdditionalChargesToResponse(array $possibleAdditionalCharges, int $doorsCount): array
{
    return array_map(function ($itemJson) use ($doorsCount, $possibleAdditionalCharges): PossibleAdditionalChargeResponse {
        $id = $itemJson["id"];
        $possibleAdditionalCharge = array_filter($possibleAdditionalCharges, function ($r) use ($id) {
            return $r->id === $id;
        });

        /** @var PossibleAdditionalCharge|false $possibleAdditionalCharge */
        $possibleAdditionalCharge = reset($possibleAdditionalCharge);
        return $possibleAdditionalCharge
            ? $possibleAdditionalCharge->toResponse($itemJson, $doorsCount)
            : (new PossibleAdditionalCharge($id, null, null))->toResponse($itemJson, $doorsCount);
    }, PossibleAdditionalChargesJsonDataManipulation::getAll());
}

function mapRosettesToResponse(array $selectedRosettes): array
{
    return array_map(function ($rosetteDb) use ($selectedRosettes): RosetteResponse {
        $id = $rosetteDb["id"];

        $selectedRosette = array_filter($selectedRosettes, function ($r) use ($id) {
            return $r->id === $id;
        });
        /** @var Rosette|false $selectedRosette */
        $selectedRosette = reset($selectedRosette);
        return $selectedRosette
            ? $selectedRosette->toResponse($rosetteDb)
            : (new Rosette($id, null))->toResponse($rosetteDb);
    }, RosettesJsonDataManipulation::getAll());
}

function mapSpecialAccessoriesToResponse(array $selectedSpecialAccessories): array
{
    return array_map(function ($specialAccessoryDb) use ($selectedSpecialAccessories): SpecialAccessoryResponse {
        $id = $specialAccessoryDb["id"];

        $specialAccessory = array_filter($selectedSpecialAccessories, function ($r) use ($id) {
            return $r->id === $id;
        });
        /** @var SpecialAccessory|false $specialAccessory */
        $specialAccessory = reset($specialAccessory);
        return $specialAccessory
            ? $specialAccessory->toResponse($specialAccessoryDb)
            : (new SpecialAccessory($id, 0, 0))->toResponse($specialAccessoryDb);
    }, SpecialAccessoriesJsonDataManipulation::getAll());
}

function mapSpecialSurchargesToResponse(array $selectedSpecialSurcharges): array
{
    return array_map(function ($specialSurchargeDb) use ($selectedSpecialSurcharges): SpecialSurchargeResponse {
        $id = $specialSurchargeDb["id"];

        $specialSurcharge = array_filter($selectedSpecialSurcharges, function ($r) use ($id) {
            return $r->id === $id;
        });
        /** @var SpecialSurcharge|false $specialSurcharge */
        $specialSurcharge = reset($specialSurcharge);
        return $specialSurcharge
            ? $specialSurcharge->toResponse($specialSurchargeDb)
            : (new SpecialSurcharge($id, 0, false, false))->toResponse($specialSurchargeDb);
    }, SpecialSurchargesJsonDataManipulation::getAll());
}

?>