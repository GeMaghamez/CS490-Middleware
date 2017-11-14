<?php

use Models\WrittenFunction;

class PyScanner {

    public $containerType;
    private $container;

    public function scanCode($code) {
        //replace all tabs with 2 spaces
        $code = str_replace("\t","  ",$code);

        //find all function signatures
        if(preg_match_all("/def.*:$/m", $code, $matches)) {
            $this->containerType = ContainerType::Functions;
            foreach ($matches[0] as $match) {
                $indentSpaceCount = strpos($match, "def");
                $bodyStart = strpos($code,"\n", strpos($code, $match) + strlen($match));

                if(preg_match('/\n {' . $indentSpaceCount . '}\n/', $code, $blockCloseMatches, null, $bodyStart)){
                    $blockClose = $blockCloseMatches[0];
                    $bodyEnd = strpos($code, $blockClose, $bodyStart);
                } elseif(!preg_match("/\s/",substr($code, -1))) {
                    //end of file
                    $bodyEnd = strlen($code);
                } else {
                    throw new Exception("Malformed Code");
                }

                if(preg_match("/\(.*\)/", $match, $tempMatches)) { //has parameters
                    $functionParams = array_map(function($str){
                        return trim($str," ()");
                    }, explode(",", $tempMatches[0]));

                    if(preg_match("/(?<=def ).*(?=\()/", $match, $tempMatches)){
                        $functionName = $tempMatches[0];
                    } else {
                        throw new Exception("Did not find function name");
                    }
                } else {
                    if(preg_match("/(?<=def ).*(?=:)/", $match, $tempMatches)){
                        $functionName = $tempMatches[0];
                    } else {
                        throw new Exception("Did not find function name");
                    }
                }

                $functionBody = substr($code, $bodyStart, $bodyEnd - $bodyStart);
                $this->container[] = new WrittenFunction($functionName,$functionParams, $functionBody);
            }
        } else {
            $this->containerType = ContainerType::Script;
            $this->container = $code;
        }
    }

    public function checkCodeChecks($questionInfo, $answerType = null, &$comment) {
        $comment = "Code Checks: \n\n";
        $totalScore = 0;
        $counter = 1;
        foreach ($questionInfo->codeChecks as $codeCheck) {
            if($this->checkCodeCheck($codeCheck, $questionInfo, $answerType)) {
                $totalScore += $codeCheck->codeCheckMaxScore;
                $comment .= "Code Check " . $counter . ": " . $codeCheck->name . " Passed. Points earned : " . $codeCheck->codeCheckMaxScore . "\n";
            } else {
                $comment .= "Code Check " . $counter . ": " . $codeCheck->name . " Failed. Points earned : 0\n";
            }
            $counter++;
        }

        return $totalScore;
    }

    private function checkCodeCheck($codeCheck, $questionInfo = null, $answerType = null) {
        if ($codeCheck->name == "correctFunctionName") {
            return $this->hasFunction($questionInfo->functionName);
        } elseif ($codeCheck->name == "correctFunctionParams") {
            return $this->hasCorrectFunctionParameters($questionInfo->functionName, $questionInfo->parameters);
        } elseif ($codeCheck->name == "hasDictionaryUse") {
            return $this->hasDictionaryUse();
        } elseif ($codeCheck->name == "hasForLoopUse") {
            return $this->hasForLoopUse();
        } elseif ($codeCheck->name == "hasWhileLoopUse") {
            return $this->hasWhileLoopUse();
        } elseif ($codeCheck->name == "usesRecursion") {
            return $this->usesRecursion();
        } elseif ($codeCheck->name == "valuePrinted") {
            return $answerType == AnswerType::printed;
        } elseif ($codeCheck->name == "valueReturned") {
            return $answerType == AnswerType::returned;
        }
    }

    public function hasFunction($funcName) {
        foreach ($this->container as $writtenFunction) {
            if ($writtenFunction->functionName === $funcName) {
                return true;
            }
        }

        return false;
    }

    public function getFunctionNames() {
        $names = [];
        foreach ($this->container as $writtenFunction) {
            $names[] = $writtenFunction->functionName;
        }

        return $names;
    }

    public function hasCorrectFunctionParameters($functionName, $functionParams) {
        foreach ($this->container as $writtenFunction) {
            if ($writtenFunction->functionParameters == $functionParams) {
                return true;
            }
        }
        return false;
    }

    public function hasForLoopUse() {
        if ($this->containerType == ContainerType::Script){
            return preg_match("/for.*in.*:/", $this->container);
        } else {
            foreach ($this->container as $writtenFunction) {
                if(preg_match("/for.*in.*:/", $writtenFunction->functionBody)){
                    return true;
                }
            }
        }
    }

    public function usesRecursion() {
        foreach ($this->container as $writtenFunction) {
            if(preg_match('/'. $writtenFunction->functionName . '\(.*\)/', $writtenFunction->functionBody, $matches)){
                return true;
            }
        }
    }

    public function hasWhileLoopUse() {
        if ($this->containerType == ContainerType::Script) {
            return preg_match('/while.*:/', $this->container);
        } else {
            foreach ($this->container as $writtenFunction) {
                if(preg_match('/while.*:/', $writtenFunction->functionBody)){
                    return true;
                }
            }
        }
    }

    public function hasDictionaryUse() {
        if ($this->containerType == ContainerType::Script) {
            return preg_match('/{.*}/', $this->container);
        } else {
            foreach ($this->container as $writtenFunction) {
                if(preg_match('/{.*}/', $writtenFunction->functionBody)) {
                    return true;
                }
            }
        }
    }
}

abstract class ContainerType {
    const Script = 0;
    const Functions = 1;
}