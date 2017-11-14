<?php

define("ERROR_TRUNCATE_LENGTH", 200);

abstract class AnswerType {
    const printed = 1;
    const returned = 2;
    const both = 4;
}

class AutoGrader {

    private $scanner;
    private $runner;

    public function __construct() {
        $this->scanner = new PyScanner();
        $this->runner = new PyRunner();
    }

    public function grade($questionInfo, $answer) {
        $gradedQuestion = [
            "questID" => $questionInfo->questId,
            "answer" => $answer,
            "testCase" => 0,
            "comment" => "",
            "codeCheckScore" => 0,
            "maxScore" => $questionInfo->maxScore
        ];

        $this->scanner->scanCode($answer);
        if($this->scanner->containerType == ContainerType::Functions && $this->scanner->hasFunction($questionInfo->functionName)){
            $score = $this->gradeTestCases($answer,$questionInfo->functionName,$questionInfo->testCases, $comment, $answerType);
        } elseif($this->scanner->containerType == ContainerType::Functions ) {
            // Attempt to replace function names
            $possible = [];
            foreach ($this->scanner->getFunctionNames() as $func){
                $passed = $this->gradeTestCases($answer,$func,$questionInfo->testCases,$comment,$answerType);
                $possible[$passed] = [
                    "replacedFunc" => $func,
                    "comment" => $comment,
                    "answerType" => $answerType,
                    "passed" => $passed
                ];
            }

            $bestScore = max($possible);
            $comment = $bestScore['comment'];
            $answerType = $bestScore['answerType'];
            $score = $bestScore['passed'];
        } elseif ($this->scanner->containerType == ContainerType::Script ) {
            $score = $this->gradeTestCases($answer,null ,$questionInfo->testCases,$comment,$answerType);
        }

        $gradedQuestion["testCase"] = round($score * $questionInfo->testCaseMaxScore);

        $codeCheckScore = $this->scanner->checkCodeChecks($questionInfo, $answerType, $codeCheckComments);
        $comment .= $codeCheckComments;

        $gradedQuestion["comment"] = $comment;
        $gradedQuestion["codeCheckScore"] = $codeCheckScore;

        return $gradedQuestion;
    }

    private function gradeTestCases($code, $func, $testCases, &$comment, &$answerType) {
        $comment = "Test Cases :\n\n";
        $answerType = 0;
        $counter = 1;
        $testsPassed = 0;
        foreach ($testCases as $testCase) {
            $testsPassed += $this->gradeTestCase($code, $func, $testCase->input, $testCase->output, $tempComment, $counter++,$tempAnswerType);
            $comment.= $tempComment;

            if(!is_null($tempAnswerType)) {
                $answerType |= $tempAnswerType;
            }
        }

        if($answerType == 0) {
            $answerType = null;
        }

        return (float) $testsPassed / count($testCases);
    }

    private function gradeTestCase($code, $func, $in, $expectedOut, &$comment, $testCaseNumber, &$answerType = null) {
        $comment = "Test Case ". $testCaseNumber . ": Input : " . $in . " Expected output : " . $expectedOut . "\n";

        if($this->scanner->containerType == ContainerType::Functions) {
            $exitCode = $this->runner->exec_pythonFunction($code, $func, $in, $outputBuffers);
        } else {
            $exitCode = $this->runner->exec_pythonScript($code,$outputBuffers);
        }

        $outputBuffers['stdout'] = trim($outputBuffers['stdout']);
        $expectedOut = trim($expectedOut);

        if($exitCode == STDOUT_BUFFER_OVERFLOW) {
            $comment .= "Error Occurred : Exceeded code maximum allowable space\n";
        } elseif($exitCode == PROCESS_TIMED_OUT) {
            $comment .= "Error Occurred : Exceeded code run time limit\n";
        } elseif($exitCode != 0) {
            $comment .= "Error Occurred : " . $outputBuffers['stderr'];
        } else {
            // no errors, possibly correct answer
            if($outputBuffers['stdout'] == $expectedOut) {
                $answerType = AnswerType::printed;
                $comment .= "Test Passed! got answer : " . $expectedOut . "\n";
            } elseif($outputBuffers['returnedValue'] == $expectedOut) {
                $answerType = AnswerType::returned;
                $comment .= "Test Passed! got answer : " . $expectedOut . "\n";
            } else {
                // incorrect answer

                if($outputBuffers['returnedValue'] != "None") {
                    $comment .= "Test Failed got answer : " . $outputBuffers['returnedValue'] . "\n";
                } elseif (!empty($outputBuffers['stdout'])) {
                    $comment .= "Test Failed got answer : " . $outputBuffers['stdout'] . "\n";
                } else {
                    $comment .= "Test Failed got nothing \n";
                }
            }
        }

        return !is_null($answerType);
    }
}