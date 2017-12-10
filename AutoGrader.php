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
            $score = $this->gradeTestCases($answer,$questionInfo->functionName,$questionInfo->testCases, $questionInfo->testCaseMaxScore, $comment, $answerType);
        } elseif($this->scanner->containerType == ContainerType::Functions ) {
            // Attempt to replace function names
            $possible = [];
            foreach ($this->scanner->getFunctionNames() as $func){
                $passed = $this->gradeTestCases($answer,$func,$questionInfo->testCases,$questionInfo->testCaseMaxScore, $comment,$answerType);
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
            $score = $this->gradeTestCases($answer,null ,$questionInfo->testCases, $questionInfo->testCaseMaxScore, $comment,$answerType);
        }

        $gradedQuestion["testCase"] = $score * $questionInfo->testCaseMaxScore;

        $codeCheckScore = $this->scanner->checkCodeChecks($questionInfo, $answerType, $codeCheckComments);
        $comment .= "<br><hr><br>" . $codeCheckComments;

        $gradedQuestion["comment"] = $comment;
        $gradedQuestion["codeCheckScore"] = $codeCheckScore;

        return $gradedQuestion;
    }

    private function gradeTestCases($code, $func, $testCases, $totalTestCaseValue, &$comment, &$answerType) {
        $comment = "<pre>Test Cases: Total Points ($totalTestCaseValue) <br><br>";
        $answerType = 0;
        $counter = 1;
        $testsPassed = 0;
        $testCaseValue = (float) $totalTestCaseValue / count($testCases);
        foreach ($testCases as $testCase) {
            $testsPassed += $this->gradeTestCase($code, $func, $testCase->input, $testCase->output, $tempComment, $counter++, $testCaseValue, $tempAnswerType);
            $comment.= $tempComment;

            if(!is_null($tempAnswerType)) {
                $answerType |= $tempAnswerType;
                $tempAnswerType = null;
            }
        }

        $comment .= "Total points lost: " . (count($testCases) - $testsPassed) * $testCaseValue . "</pre>";

        if($answerType == 0) {
            $answerType = null;
        }

        return (float) $testsPassed / count($testCases);
    }

    private function gradeTestCase($code, $func, $in, $expectedOut, &$comment, $testCaseNumber, $testCaseValue, &$answerType = null) {
        $comment = "Test Case ". $testCaseNumber . "; Input: " . $in . " Expected output: " . $expectedOut . "<br>";

        if($this->scanner->containerType == ContainerType::Functions) {
            $exitCode = $this->runner->exec_pythonFunction($code, $func, $in, $outputBuffers);
        } else {
            $exitCode = $this->runner->exec_pythonScript($code,$outputBuffers);
        }

        $outputBuffers['stdout'] = trim($outputBuffers['stdout']);
        $expectedOut = trim($expectedOut);

        if($exitCode == STDOUT_BUFFER_OVERFLOW) {
            $comment .= "<span style=\"color: red\">Error Occurred: Exceeded code maximum allowable space</span><br><br>";
        } elseif($exitCode == PROCESS_TIMED_OUT) {
            $comment .= "<span style=\"color: red\">Error Occurred: Exceeded code run time limit</span><br><br>";
        } elseif($exitCode != 0) {
            $comment .= "<span style=\"color: red\">Error Occurred : " . $outputBuffers['stderr'] . "</span><br><br>";
        } else {
            // no errors, possibly correct answer
            if($outputBuffers['stdout'] == $expectedOut || $outputBuffers['returnedValue'] == $expectedOut) {
                $answerType = ($outputBuffers['stdout'] == $expectedOut) ? AnswerType::printed : AnswerType::returned;
                $comment .= "<span style=\"color: green\">Test Passed! output was: " . $expectedOut . ".</span><br><br>";
            } else {
                // incorrect answer
                $comment .= "<span style=\"color: red\">Test Failed output was: ";
                if($outputBuffers['returnedValue'] != "None") {
                    $comment .= $outputBuffers['returnedValue'];
                } elseif (!empty($outputBuffers['stdout'])) {
                    $comment .= $outputBuffers['stdout'];
                } else {
                    $comment .= "nothing";
                }

                $comment .= ".\t- " . $testCaseValue . " points</span><br><br>";
            }
        }

        return !is_null($answerType);
    }
}