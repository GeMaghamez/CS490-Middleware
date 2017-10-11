<?php namespace util;


class TypedProperty {

    private $name;
    private $type;
    private $value;

    public function __construct($name, $type) {
        $this->name = $name;
        $this->type = $type;
    }

    public function getType() {
        return $this->type;
    }

    public function getValue() {
        return $this->value;
    }

    public function getName() {
        return $this->name;
    }

    public function setValue($value) {
        if (gettype($value) != $this->type) {
            throw new \InvalidArgumentException("expected type of " . $this->name . " to be " . $this->type . " but got " . gettype($value) . " instead");
        }

        $this->value = $value;
    }
}