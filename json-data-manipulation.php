<?php
include_once "functions.php";
include_once "constants.php";

class DistrictsJsonDataManipulation
{
    public static function getAll(): array
    {
        return getArrayFromJsonFile("./assets/json/districts.json");
    }

    public static function findByIdOrFalse($id)
    {
        $list = DistrictsJsonDataManipulation::getAll();
        $item = array_filter($list, function ($r) use ($id) {
            return $r["id"] == $id;
        });
        return reset($item);
    }
}

class DoorsJsonDataManipulation
{
    public static function getAllByCategory($category): array
    {
        global $doors_path;
        return getArrayFromJsonFile($doors_path . "/" . $category . "/price.json");
    }

    public static function getMaterialTranslation(string $translationKey)
    {
        return getArrayFromJsonFile("images/materials/conf.json")["Druhy laminátov"][$translationKey];
    }
}

class PossibleAdditionalChargesJsonDataManipulation
{
    public static function getAll(): array
    {
        return getArrayFromJsonFile("./assets/json/possibleAdditionalCharges.json");
    }

    public static function findByIdOrFalse($id)
    {
        $list = PossibleAdditionalChargesJsonDataManipulation::getAll();
        $item = array_filter($list, function ($r) use ($id) {
            return $r["id"] == $id;
        });
        return reset($item);
    }
}

class RosettesJsonDataManipulation
{
    public static function getAll(): array
    {
        return getArrayFromJsonFile("./assets/json/rosettes.json");
    }

    public static function findByIdOrFalse($id)
    {
        $list = RosettesJsonDataManipulation::getAll();
        $item = array_filter($list, function ($r) use ($id) {
            return $r["id"] == $id;
        });
        return reset($item);
    }
}

class SpecialAccessoriesJsonDataManipulation
{
    public static function getAll(): array
    {
        return getArrayFromJsonFile("./assets/json/specialAccessories.json");
    }

    public static function findByIdOrFalse($id)
    {
        $list = SpecialAccessoriesJsonDataManipulation::getAll();
        $item = array_filter($list, function ($r) use ($id) {
            return $r["id"] == $id;
        });
        return reset($item);
    }
}

class SpecialSurchargesJsonDataManipulation
{
    public static function getAll(): array
    {
        return getArrayFromJsonFile("./assets/json/specialSurcharges.json");
    }

    public static function findByIdOrFalse($id)
    {
        $list = SpecialSurchargesJsonDataManipulation::getAll();
        $item = array_filter($list, function ($r) use ($id) {
            return $r["id"] == $id;
        });
        return reset($item);
    }
}

class AppConfigJsonDataManipulation
{
    public static function getAll(): array
    {
        $json = getArrayFromJsonFile("./app-config.json");
        $profile = $json["profile"];
        return $json[$profile];
    }
}

?>