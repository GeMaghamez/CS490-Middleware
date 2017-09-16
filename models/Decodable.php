<?php namespace models;

class Decodable {

    public static function fromJSON($jsonString) {}

    // TODO: review function and make sure it work for all cases.
    public static function fromXML($xmlString) {
        if (!is_string($xmlString)) {
            throw new \InvalidArgumentException("expected argument of type string got : " . gettype($xmlString));
        }

        $xmlparser = xml_parser_create();
        xml_parse_into_struct($xmlparser, $xmlString, $values, $index);
        $obj = new static();

        foreach (get_object_vars($obj) as $property) {
            $tag = strtoupper($property->getName());
            if (!array_key_exists($tag, $index)) {
                throw new \Exception($property->getName() . " is missing");
            }

            $property->setValue($values[$index[$tag][0]]["value"]);
        }

        xml_parser_free($xmlparser);

        return $obj;
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
}