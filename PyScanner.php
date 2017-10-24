<?php

class PyScanner
{

    public static function correctFunctionName($code, $functionName) {
        return preg_match("/def +$functionName\(.*\) *:\s+/", $code);
    }

    public static function correctFunctionParams($code, $functionName, $functionParams) {
        $parameters = join(", ",$functionParams);
        return preg_match("/def +$functionName\($parameters\) *:\s+/", $code);
    }

    public static function hasForLoopUse($code) {
        return preg_match("/for +[a-zA-Z_]+[a-zA-Z0-9_]* +in +[a-zA-Z_]+[a-zA-Z0-9_\(,\) .]*:\s+/", $code);
    }

    public static function hasWhileLoopUse($code) {
        return preg_match("/while +\(?[a-zA-Z_][a-zA-Z0-9_]* +[><!=]+ +[a-zA-Z0-9_]*\)? *:\s+/", $code);
    }

    public static function hasReturnStatement($code) {
        return preg_match("/return *\(?(([a-zA-Z_][a-zA-Z0-9_]*)|([0-9]+))\)?\s+/", $code);
    }

    public static function hasDictionaryUse($code) {
        //TODO: add regex
        return false;
    }
}