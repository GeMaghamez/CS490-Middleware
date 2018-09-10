<?php namespace Models;

abstract class Decodable {

    public abstract function __construct($JSONObject);

    protected function validateTypeRequired($expectedType, $name, $json) {
        $value = $json->{$name};
        $type = gettype($value);
        if($type != $expectedType){
            throw new \InvalidArgumentException("expected type $expectedType for $name but instead got $type");
        }

        return $value;
    }

    protected function validateTypeOptional($expectedType, $name, $json) {
        try {
            return $this->validateTypeRequired($expectedType, $name, $json);
        } catch (\InvalidArgumentException $exception) {
            return null;
        }
    }

    protected function validateType($expectedType, &$var) {
        $type = gettype($var);
        if($type != $expectedType) {
            return null;
        } else {
            return $var;
        }
    }
}