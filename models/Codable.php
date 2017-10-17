<?php namespace models;

class Codable {

    public static function fromJSON($jsonString) {
        return self::fromAssociativeArray(json_decode($jsonString, true));
    }

    public static function fromAssociativeArray($array) {
        if (!is_array($array)) {
            throw new \InvalidArgumentException("expected argument of type array got : " . gettype($array));
        }

        $obj = new static();
        foreach (get_object_vars($obj) as $property) {
            if (!array_key_exists($property->getName(), $array)) {
                throw new \Exception($property->getName() . " is missing");
            }

            $property->setValue($array[$property->getName()]);
        }

        return $obj;
    }

    public function toJSON() {
        $array = [];
        foreach (get_object_vars($this) as $property) {
            $array[$property->getName()] = $property->getValue();
        }

        return json_encode($array);
    }

}