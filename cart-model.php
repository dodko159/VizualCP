<?php

include_once('constants.php');
include_once('functions.php');
include_once('cart-model-api.php');
include_once('json-data-manipulation.php');

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

    public function __construct(string $city, string $district, string $street, string $streetNumber, string $zipCode)
    {
        $this->city = $city;
        $this->district = $district;
        $this->street = $street;
        $this->streetNumber = $streetNumber;
        $this->zipCode = $zipCode;
    }

    public static function fromRequest($req): Address
    {
        return new self(
            $req->city ?? "",
            $req->district ?? "",
            $req->street ?? "",
            $req->streetNumber ?? "",
            $req->zipCode ?? ""
        );
    }

    public function toResponse(): AddressResponse
    {
        return new AddressResponse(
            $this->city ?? "",
            $this->district ?? "",
            $this->street ?? "",
            $this->streetNumber ?? "",
            $this->zipCode ?? ""
        );
    }

    public function getFullAddress(): string
    {
        $parts = array_filter([
            trim($this->street . ' ' . $this->streetNumber),
            trim($this->zipCode . ' ' . $this->city),
        ]);

        return implode(', ', $parts);
    }
}

class Contact
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

    public static function fromRequest($req): Contact
    {
        return new self(
            $req->email ?? "",
            $req->fullName ?? "",
            $req->phoneNumber ?? ""
        );
    }

    public function toResponse(): ContactResponse
    {
        return new ContactResponse(
            $this->email ?? "",
            $this->fullName ?? "",
            $this->phoneNumber ?? ""
        );
    }
}

class Handle
{
    /** @var int $count */
    public $count;

    /** @var string $name */
    public $name;

    /** @var float $price */
    public $price;

    public function __construct($count, $name, $price)
    {
        $this->count = $count;
        $this->name = $name;
        $this->price = $price;
    }

    public static function fromRequest($req): Handle
    {
        return new self(
            $req->count,
            $req->name,
            $req->price
        );
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
        return $this->count * $this->price;
    }
}

class PossibleAdditionalCharge
{
    public static $SILICONE_ID = "possible-additional-charges-silicone-wall-frame";
    public static $SILICONE_PRICE_MULTIPLIER = 0.7;

    /** @var string $id */
    public $id;

    /** @var int $count */
    public $count;

    /** @var bool $isCountDirty */
    public $isCountDirty;

    public function __construct($id, $count, $isCountDirty)
    {
        $this->id = $id;
        $this->count = $count;
        $this->isCountDirty = $isCountDirty;
    }

    public static function fromRequest($req): PossibleAdditionalCharge
    {
        return new self(
            $req->id,
            $req->count,
            $req->isCountDirty
        );
    }

    public function toResponse($possibleAdditionalChargeDb, $doorsCount): PossibleAdditionalChargeResponse
    {
        $effectiveCount = $this->getEffectiveCount($possibleAdditionalChargeDb, $doorsCount);
        return new PossibleAdditionalChargeResponse(
            $this->id,
            $this->calculatePrice($possibleAdditionalChargeDb, $effectiveCount),
            $possibleAdditionalChargeDb["price"] ?? 0.0,
            $effectiveCount,
            $this->isCountDirty ?? false,
            $possibleAdditionalChargeDb["header"] ?? null,
            $possibleAdditionalChargeDb["hint"] ?? "",
            $possibleAdditionalChargeDb["imgSrc"],
            $possibleAdditionalChargeDb["label"] ?? "",
            $possibleAdditionalChargeDb["youtubeVideoCode"],
            $possibleAdditionalChargeDb["videoSrc"]
        );
    }

    function getEffectiveCount(array $possibleAdditionalChargeDb, int $doorsCount)
    {
        $setCountBasedOnDoorsCount = $possibleAdditionalChargeDb["setCountBasedOnDoorsCount"] ?? false;
        if (!$this->isCountDirty && $setCountBasedOnDoorsCount) {
            return $doorsCount;
        }

        return $this->count ?? 0.0;
    }

    function calculatePrice(array $itemJson, int $effectiveCount): float
    {
        $coefficient = $this->id == PossibleAdditionalCharge::$SILICONE_ID ? PossibleAdditionalCharge::$SILICONE_PRICE_MULTIPLIER : 1.0;
        $rawPrice = $itemJson["price"] ?? 0.0;
        return $effectiveCount * $rawPrice * $coefficient;
    }
}

class Rosette
{
    /** @var string $id */
    public $id;

    /** @var int $count */
    public $count;

    public function __construct($id, $count)
    {
        $this->id = $id;
        $this->count = $count;
    }

    public static function fromRequest($req): Rosette
    {
        return new self(
            $req->id,
            $req->count
        );
    }

    public function toResponse($rosetteDb): RosetteResponse
    {
        return new RosetteResponse(
            $this->id,
            $this->calculatePrice($rosetteDb),
            $this->count ?? 0.0,
            $rosetteDb["header"] ?? null,
            $rosetteDb["hint"] ?? "",
            $rosetteDb["imgSrc"],
            $rosetteDb["label"] ?? "",
            $rosetteDb["price"] ?? 0.0,
            $rosetteDb["youtubeVideoCode"],
            $rosetteDb["videoSrc"]
        );
    }

    function calculatePrice($rosetteDb): float
    {
        $rawPrice = $rosetteDb["price"] ?? 0.0;
        return $this->count * $rawPrice;
    }
}

class LineItem
{
    /** @var int $count */
    public $count;

    /** @var string $name */
    public $name;

    /** @var float $price */
    public $price;

    public function __construct($count, $name, $price)
    {
        $this->count = $count;
        $this->name = $name;
        $this->price = $price;
    }

    public static function fromRequest($req): LineItem
    {
        return new self(
            $req->count ?? 0.0,
            $req->name,
            $req->price ?? 0.0
        );
    }

    public function toResponse(): LineItemResponse
    {
        return new LineItemResponse(
            $this->calculatePrice(),
            $this->count ?? 0.0,
            $this->name,
            $this->price ?? 0.0,
        );
    }

    function calculatePrice(): float
    {
        return $this->count * $this->price;
    }
}

class SpecialAccessory
{
    /** @var string $id */
    public $id;

    /** @var int $count */
    public $count;

    /** @var float $selectedPrice */
    public $selectedPrice;

    public function __construct($id, $count, $selectedPrice)
    {
        $this->id = $id;
        $this->count = $count;
        $this->selectedPrice = $selectedPrice;
    }

    public static function fromRequest($req): SpecialAccessory
    {
        return new self(
            $req->id,
            $req->count,
            $req->selectedPrice
        );
    }

    public function toResponse($specialAccessoryDb): SpecialAccessoryResponse
    {
        return new SpecialAccessoryResponse(
            $this->id,
            $this->calculatePrice($specialAccessoryDb),
            $specialAccessoryDb["price"],
            $this->count ?? 0.0,
            $specialAccessoryDb["header"] ?? null,
            $specialAccessoryDb["hint"] ?? "",
            $specialAccessoryDb["imgSrc"],
            $specialAccessoryDb["label"] ?? "",
            $this->selectedPrice,
            $specialAccessoryDb["youtubeVideoCode"],
            $specialAccessoryDb["videoSrc"]
        );
    }

    function calculatePrice($specialAccessoryDb): float
    {
        $price = $specialAccessoryDb["price"] ?? $this->selectedPrice ?? 0.0;
        return $this->count * $price;
    }
}

class SpecialSurcharge
{
    /** @var string $id */
    public $id;

    /** @var int $count */
    public $count;

    /** @var bool $isAssemblySelected */
    public $isAssemblySelected;

    /** @var bool $isAssemblySelectedDirty */
    public $isAssemblySelectedDirty;

    public function __construct($id, $count, $isAssemblySelected, $isAssemblySelectedDirty)
    {
        $this->id = $id;
        $this->count = $count;
        $this->isAssemblySelected = $isAssemblySelected;
        $this->isAssemblySelectedDirty = $isAssemblySelectedDirty;
    }

    public static function fromRequest($req): SpecialSurcharge
    {
        return new self(
            $req->id,
            $req->count,
            SpecialSurcharge::getEffectiveIsAssemblySelected($req->count, $req->isAssemblySelected, $req->isAssemblySelectedDirty),
            $req->isAssemblySelectedDirty
        );
    }

    public function toResponse($specialSurchargeDb): SpecialSurchargeResponse
    {
        return new SpecialSurchargeResponse(
            $this->id,
            $this->calculatePrice($specialSurchargeDb),
            $specialSurchargeDb["price"],
            $this->count ?? 0.0,
            $specialSurchargeDb["header"] ?? null,
            $specialSurchargeDb["hint"] ?? "",
            $specialSurchargeDb["imgSrc"],
            SpecialSurcharge::getEffectiveIsAssemblySelected($this->count, $this->isAssemblySelected, $this->isAssemblySelectedDirty),
            $this->isAssemblySelectedDirty,
            $specialSurchargeDb["label"] ?? "",
            $specialSurchargeDb["labelAssembly"] ?? "",
            $specialSurchargeDb["youtubeVideoCode"],
            $specialSurchargeDb["videoSrc"]
        );
    }

    function calculatePrice($specialSurchargeDb): float
    {
        $assemblyCosts = $specialSurchargeDb["assemblyCosts"] ?? 0.0;
        $assemblyPrice = $this->isAssemblySelected ? $assemblyCosts : 0.0;
        $price = $specialSurchargeDb["price"] ?? 0.0;
        return $this->count * ($price + $assemblyPrice);
    }

    static function getEffectiveIsAssemblySelected($count, $isAssemblySelected, $isAssemblySelectedDirty): bool
    {
        if ($count === 0) {
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
    const FEE_FRAME = 93;
    const FEE_FRAME_OFFER = 79;

    /** @var string $category */
    public $category;

    /** @var string $type */
    public $type;

    /** @var string $material */
    public $material;

    /** @var string $width */
    public $width;

    /** @var int $count*/
    public $count;

    /** @var float $price*/
    public $price;

    /** @var string $info*/
    public $info;

    /** @var boolean $frame*/
    public $frame;

    /** @var boolean $assembly */
    public $assembly;

    /** @var boolean $isDtdSelected */
    public $isDtdSelected;

    function __construct($c, $t, $m, $w, $cn, $nfo, $frame, $assembly, $isDtdSelected)
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

    public static function fromRequest($req): Door
    {
        return new self(
            $req->category,
            $req->type,
            $req->material,
            $req->width,
            1,
            "",
            $req->isDoorFrameEnabled,
            false,
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

    //old price offer
    function isWidthSelectedText($mWidth): string
    {
        if ($this->width == $mWidth) {
            return "selected";
        }
        return "";
    }

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

    function calculatePrice()
    {
        $rawPrice = $this->getRawPrice($this->type);

        if ($this->frame) {
            $rawPrice = $rawPrice + ($this->type == "v1" ? Door::FEE_FRAME_OFFER : Door::FEE_FRAME);//extra fee for frame
        }

        $rawPrice = $rawPrice + Width::class::getFee($this->width);//extra fee for width
        $rawPrice = $rawPrice + ($this->isDtdSelected ? Door::FEE_DTD : 0);//extra fee for DTD
        return $this->count * $rawPrice;
    }

    function isDtdAvailable(): bool
    {
        return $this->type == "v1" || in_array($this->category, array("Petra", "Vanesa"));
    }

    function getWidthString(): string
    {
        return Width::getWidthString($this->width);
    }

    function getRawPrice($type)
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

    //old price offer
    function getHTMLdoor($idx)
    {
        global $material_path, $doors_path, $povrchova_uprava, $typ_dveri, $currency;

        ob_start();
        include 'cart-item.php';
        $data = ob_get_contents();
        ob_end_clean();

        return $data;
    }

    //old price offer
    //volane len z CP
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

    //updated
}

class PriceOffer
{
    const ASSEMBLY_DOORS_COST = 25;
    const ASSEMBLY_DOORS_COST_MIN = 150;
    const ASSEMBLY_PRICE_HANDLES_ROSETTES = 5;
    const DELIVERY_PRICE_PER_KM = 0.5;
    const VAT = 1.23;

    /** @var Address $address */
    public $address;
    /** @var int $assemblyPriceHandlesRosettesCount */
    public $assemblyPriceHandlesRosettesCount;
    /** @var array $doors */
    public $doors;
    /** @var Handle $handle */
    public $handle;
    /** @var bool $isAssemblyDoorsCountDirty */
    public $isAssemblyDoorsCountDirty;
    /** @var array<PossibleAdditionalCharge> $possibleAdditionalCharges */
    public $possibleAdditionalCharges = array();
    /** @var array<LineItem> $possibleAdditionalChargesLineItems */
    public $possibleAdditionalChargesLineItems = array();
    /** @var array<Rosette> $selectedRosettes */
    public $selectedRosettes = array();
    /** @var array<LineItem> $rosettesLineItems */
    public $rosettesLineItems = array();
    /** @var array<SpecialAccessory> $specialAccessories */
    public $specialAccessories = array();
    /** @var array<LineItem> $specialAccessoriesLineItems */
    public $specialAccessoriesLineItems = array();
    /** @var array<SpecialSurcharge> $specialSurcharges */
    public $specialSurcharges = array();
    /** @var array<LineItem> $specialSurchargesLineItems */
    public $specialSurchargesLineItems = array();
    public $assembly;   // obsolete - nepouziva sa - pre kazde dvere zvlast
    /** @var int $specialSurchargesLineItems */
    public $assemblyDoorsCount;
    /** @var Contact $contact */
    public $contact;
    public $seal;       // bool
    public $putty;      // bool
    public $ironFrame;  // bool
    public $floor3;     // bool
    public $thickerFrame; // integer
    public $higherFrame; // integer
    public $distance; // integer
    public $doorLiners; // integer oblozka
    public $note; //string

    function __construct()
    {
    }

    public static function fromRequest($req): PriceOffer
    {
        $instance = new self();
        $instance->address = Address::fromRequest($req->address);
        $instance->assemblyDoorsCount = $req->assemblyDoorsCount ?? 0;
        $instance->assemblyPriceHandlesRosettesCount = $req->assemblyPriceHandlesRosettesCount ?? 0;
        $instance->contact = Contact::fromRequest($req->contact);
        $instance->doors = array_map(function ($value): Door {
            return Door::fromRequest($value);
        }, $req->doors ?? array());

        $instance->handle = Handle::fromRequest($req->handle);
        $instance->isAssemblyDoorsCountDirty = $req->isAssemblyDoorsCountDirty ?? false;
        $instance->note = $req->note ?? "";
        $instance->possibleAdditionalCharges = array_map(function ($value): PossibleAdditionalCharge {
            return PossibleAdditionalCharge::fromRequest($value);
        }, $req->possibleAdditionalCharges ?? array());
        $instance->possibleAdditionalChargesLineItems = array_map(function ($value): LineItem {
            return LineItem::fromRequest($value);
        }, $req->possibleAdditionalChargesLineItems ?? array());
        $instance->selectedRosettes = array_map(function ($value): Rosette {
            return Rosette::fromRequest($value);
        }, $req->rosettes ?? array());
        $instance->rosettesLineItems = array_map(function ($value): LineItem {
            return LineItem::fromRequest($value);
        }, $req->rosettesLineItems ?? array());
        $instance->specialAccessories = array_map(function ($value): SpecialAccessory {
            return SpecialAccessory::fromRequest($value);
        }, $req->specialAccessories ?? array());
        $instance->specialAccessoriesLineItems = array_map(function ($value): LineItem {
            return LineItem::fromRequest($value);
        }, $req->specialAccessoriesLineItems ?? array());
        $instance->specialSurcharges = array_map(function ($value): SpecialSurcharge {
            return SpecialSurcharge::fromRequest($value);
        }, $req->specialSurcharges ?? array());
        $instance->specialSurchargesLineItems = array_map(function ($value): LineItem {
            return LineItem::fromRequest($value);
        }, $req->specialSurchargesLineItems ?? array());

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
            $this->address ? $this->address->toResponse() : new AddressResponse("", "", "", "", ""),
            $assemblyDoorsCalculatedPrice,
            $assemblyDoorsEffectiveCount,
            $this->assemblyPriceHandlesRosettesCount,
            $this->calculateAssemblyPriceHandlesRosettes(),
            $price,
            $this->calculatePriceVat($price),
            $this->contact ? $this->contact->toResponse() : new ContactResponse("", "", ""),
            $this->calculateDeliveryCosts(),
            array_map(function (Door $door): DoorResponse {
                return $door->toResponse();
            }, $this->doors ?? array()),
            $this->handle ? $this->handle->toResponse() : null,
            $this->isAssemblyDoorsCountDirty,
            $this->note,
            mapPossibleAdditionalChargesToResponse($this->possibleAdditionalCharges, $doorsCount),
            array_map(function ($it): LineItemResponse {
                return $it->toResponse();
            }, $this->possibleAdditionalChargesLineItems ?? array()),
            mapRosettesToResponse($this->selectedRosettes),
            array_map(function ($it): LineItemResponse {
                return $it->toResponse();
            }, $this->rosettesLineItems ?? array()),
            new SectionsCalculatedPriceResponse(
                $this->calculateDoorsPrice(),//doors
                $this->calculateHandlesAndRosettesPrice(),//handlesAndRosettes
                $this->calculateDeliveryCosts(),//delivery
                $assemblyDoorsCalculatedPrice,//assemblyDoors
                $this->calculateSpecialAccessoriesPrice(),//specialAccessories
                $possibleAdditionalChargesCalculatedPrice,//possibleAdditionalCharges
                $this->calculateSpecialSurchargesPrice()//specialSurcharges
            ),
            mapSpecialAccessoriesToResponse($this->specialAccessories),
            array_map(function ($it): LineItemResponse {
                return $it->toResponse();
            }, $this->specialAccessoriesLineItems ?? array()),
            mapSpecialSurchargesToResponse($this->specialSurcharges),
            array_map(function ($it): LineItemResponse {
                return $it->toResponse();
            }, $this->specialSurchargesLineItems ?? array()),
        );
    }

    function getEffectiveCountIsAssemblyDoorsCount($doorsCount)
    {
        if (!$this->isAssemblyDoorsCountDirty) {
            return $doorsCount;
        }

        return $this->assemblyDoorsCount ?? 0;
    }

    //gettre
    function getDistance(): float
    {
        $dist = 0;
        if ($this->distance != null && $this->distance > 0) {
            $dist = $this->distance;
        }
        return $dist;
    }

    function getDoorLiners(): float
    {
        $dist = 0;
        if ($this->doorLiners != null && $this->doorLiners > 0) {
            $dist = $this->doorLiners;
        }
        return $dist;
    }

    //funkcie

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

    function calculateDoorsAssemblyCosts(int $assemblyDoorsCount): float
    {
        if ($assemblyDoorsCount > 0) {
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
        return array_sum(array_map(function ($door): float {
            return $door->calculatePrice();
        }, $this->doors ?? array()));
    }

    function calculateHandlesAndRosettesPrice(): float
    {
        $handlePrice = $this->handle ? $this->handle->calculatePrice() : 0.0;

        $rosettesPrice = array_sum(array_map(function ($rosette): float {
            return $rosette->calculatePrice(RosettesJsonDataManipulation::findByIdOrFalse($rosette->id));
        }, $this->selectedRosettes ?? array()));
        $rosettesLineItemsPrice = array_sum(array_map(function ($it): float {
            return $it->calculatePrice();
        }, $this->rosettesLineItems ?? array()));

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
            }, $this->possibleAdditionalCharges ?? []
        ));

        $lineItemsTotal = array_sum(array_map(
            function ($item): float {
                return $item->calculatePrice();
            }, $this->possibleAdditionalChargesLineItems ?? []
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
            $this->specialAccessories ?? []
        ));

        $lineItemsTotal = array_sum(array_map(
            function ($item): float {
                return $item->calculatePrice();
            },
            $this->specialAccessoriesLineItems ?? []
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
            $this->specialSurcharges ?? []
        ));

        $lineItemsTotal = array_sum(array_map(
            function ($item): float {
                return $item->calculatePrice();
            },
            $this->specialSurchargesLineItems ?? []
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
        return $this->assemblyPriceHandlesRosettesCount * PriceOffer::ASSEMBLY_PRICE_HANDLES_ROSETTES;
    }

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

    function getSealPrice()
    {
        global $cena_tesnenia;
        $price = 0;
        if ($this->seal) {
            $price += $this->getDoorNumber() * $cena_tesnenia;
        }
        return $price;
    }

    function getPuttyPrice()
    {
        global $cena_tmelenia;
        $price = 0;
        if ($this->putty) {
            $price += $this->getDoorNumber() * $cena_tmelenia;
        }
        return $price;
    }

    function getIronPrice()
    {
        global $cena_obkladu_zarubne;
        $price = 0;
        if ($this->ironFrame) {
            $price += $this->getDoorNumber() * $cena_obkladu_zarubne;
        }
        return $price;
    }

    function getFloor3Price()
    {
        global $cena_vynasania;
        $price = 0;
        if ($this->floor3) {
            $price += $this->getDoorNumber() * $cena_vynasania;
        }
        return $price;
    }

    function getThickerFramePrice()
    {
        global $cena_priplatok_hrubsia_zaruben;
        $price = 0;
        if ($this->thickerFrame != null && $this->thickerFrame > 0) {
            $price += $this->thickerFrame * $cena_priplatok_hrubsia_zaruben;
        }
        return $price;
    }

    function getHigherFramePrice()
    {
        global $cena_priplatok_vyssia_zaruben;
        $price = 0;
        if ($this->higherFrame != null && $this->higherFrame > 0) {
            $price += $this->higherFrame * $cena_priplatok_vyssia_zaruben;
        }
        return $price;
    }

    function getDistancePrice()
    {
        global $cena_km;
        $price = 0;
        if ($this->distance != null && $this->distance > 0) {
            $price += $this->distance * $cena_km;
        }
        return $price;
    }

    function getLinerPrice()
    { // oblozky
        global $cena_zarubne;
        $price = 0;
        if ($this->doorLiners != null && $this->doorLiners > 0) {
            $price += $this->doorLiners * $cena_zarubne;
        }
        return $price;
    }

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

    function getPriceOf($id): float
    {
        if (array_key_exists($id, $this->doors)) {
            $door = $this->doors[$id];
            return $door->getFullPrice();
        } else {
            throw new Exception("Bad ID.");
        }
    }

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

    function changeValueOf($fc, $id, $value): array
    {
        try {
            /** @var Door $door */
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

function sucessResult(): array
{
    return array(
        'sucess' => true,
        'message' => "OK",
        'result' => "OK"
    );
}

function fixObject(&$object)
{
    if (!is_object($object) && gettype($object) == 'object')
        return ($object = unserialize(serialize($object)));
    return $object;
}

function getCategoryFromDoorType($typ): string
{
    if ($typ != null) {
        switch (strtoupper(substr($typ, 0, 1))) {
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
            default:
                return "";
        }
    }

    return "";
}

function mapPossibleAdditionalChargesToResponse($possibleAdditionalCharges, $doorsCount): array
{
    return array_map(function ($itemJson) use ($doorsCount, $possibleAdditionalCharges): PossibleAdditionalChargeResponse {
        $id = $itemJson["id"];
        $possibleAdditionalCharge = array_filter($possibleAdditionalCharges ?? [], function ($r) use ($id) {
            return $r->id === $id;
        });

        /** @var PossibleAdditionalCharge $possibleAdditionalCharge */
        $possibleAdditionalCharge = reset($possibleAdditionalCharge);
        return $possibleAdditionalCharge
            ? $possibleAdditionalCharge->toResponse($itemJson, $doorsCount)
            : (new PossibleAdditionalCharge($id, 0, false))->toResponse($itemJson, $doorsCount);
    }, PossibleAdditionalChargesJsonDataManipulation::getAll() ?? array());
}

function mapRosettesToResponse($rosettes): array
{
    return array_map(function ($rosetteDb) use ($rosettes): RosetteResponse {
        $id = $rosetteDb["id"];

        $selectedRosette = array_filter($rosettes ?? [], function ($r) use ($id) {
            return $r->id === $id;
        });
        /** @var Rosette $selectedRosette */
        $selectedRosette = reset($selectedRosette);
        return $selectedRosette
            ? $selectedRosette->toResponse($rosetteDb)
            : (new Rosette($id, 0))->toResponse($rosetteDb);
    }, RosettesJsonDataManipulation::getAll() ?? array());
}

function mapSpecialAccessoriesToResponse($specialAccessories): array
{
    return array_map(function ($specialAccessoryDb) use ($specialAccessories): SpecialAccessoryResponse {
        $id = $specialAccessoryDb["id"];

        $specialAccessory = array_filter($specialAccessories ?? array(), function ($r) use ($id) {
            return $r->id === $id;
        });
        /** @var SpecialAccessory $specialAccessory */
        $specialAccessory = reset($specialAccessory);
        return $specialAccessory
            ? $specialAccessory->toResponse($specialAccessoryDb)
            : (new SpecialAccessory($id, 0, 0))->toResponse($specialAccessoryDb);
    }, SpecialAccessoriesJsonDataManipulation::getAll() ?? array());
}

function mapSpecialSurchargesToResponse($specialSurcharges): array
{
    return array_map(function ($specialSurchargeDb) use ($specialSurcharges): SpecialSurchargeResponse {
        $id = $specialSurchargeDb["id"];

        $specialSurcharge = array_filter($specialSurcharges ?? array(), function ($r) use ($id) {
            return $r->id === $id;
        });
        /** @var SpecialSurcharge $specialSurcharge */
        $specialSurcharge = reset($specialSurcharge);
        return $specialSurcharge
            ? $specialSurcharge->toResponse($specialSurchargeDb)
            : (new SpecialSurcharge($id, 0, false, false))->toResponse($specialSurchargeDb);
    }, SpecialSurchargesJsonDataManipulation::getAll() ?? array());
}

?>