<?php

class AutoGrader {
    public $lastStderr;
    public $lastStdout;

    public function executeCode($code) {
        if (is_resource($f = tmpfile()) &&
            is_resource($stderr = tmpfile()) &&
            is_resource($stdout = tmpfile())) {
            $this->lastStderr = $this->lastStderr = "";
            fwrite($f, $code);
            $fileName = stream_get_meta_data($f)['uri'];
            $stderrName = stream_get_meta_data($stderr)['uri'];
            $stdoutName = stream_get_meta_data($stdout)['uri'];
            $command = "python " . $fileName . " 2> " . $stderrName . " >> " . $stdoutName;
            exec($command);
            $this->lastStderr = file_get_contents($stderrName);
            $this->lastStdout = file_get_contents($stdoutName);
        }
    }
}