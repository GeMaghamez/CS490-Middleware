<?php

define("STDOUT_BUFFER_OVERFLOW", 131);
define("PROCESS_TIMED_OUT", 132);

class PyRunner {

    public $timeout = 1; // seconds
    public $maxSpace = 1024; // bytes

    public function exec_pythonScript($code, &$outputBuffers, $input = null) {
        if($script = tmpfile()) {
            fwrite($script, $code);
            $path = stream_get_meta_data($script)['uri'];
            $exitCode = $this->exec_timeout('python ' . $path, $outputBuffers, $input);
            fclose($script);
            return $exitCode;
        }
    }

    public function exec_pythonFunction($code, $functionName, $functionArguments, &$outputBuffers, $input = null) {
         if($script = tmpfile()) {
            fwrite($script, $code);
            $parameters = $functionArguments;
            if(is_array($functionArguments)){
                $parameters = join(",", $functionArguments);
            }
            fwrite($script, "\nprint(\"returned value: \" + str(" . $functionName . "(" . $parameters . ")))" );
            $path = stream_get_meta_data($script)['uri'];
            $exitCode = $this->exec_timeout('python ' . $path, $outputBuffers, $input);
            fclose($script);

            if(preg_match('/(?<=returned value: ).*(?=\n)/', $outputBuffers['stdout'],$matches)) {
                $match = $matches[0];
                $outputBuffers['returnedValue'] = $match;
                $outputBuffers['stdout'] = preg_replace("/^returned value: .*$/sm", "", $outputBuffers['stdout']);
            }

            return $exitCode;
        }
    }

    private function exec_timeout($cmd, &$outputBuffers, $input = null) {
        // clear buffers
        $outputBuffers['stdout'] = "";
        $outputBuffers['stderr'] = "";
        $outputBuffers['returnedValue'] = "";

        // File descriptors passed to the process.
        $descriptors = array(
            0 => array('pipe', 'r'),  // stdin
            1 => array('pipe', 'w'),  // stdout
            2 => array('pipe', 'w'),   // stderr
            3 => array('pipe', 'w')
        );

        $process = proc_open($cmd . "; echo $? >&3", $descriptors, $pipes);

        if (!is_resource($process)) {
            throw new \Exception('Could not execute process');
        }

        // write input to stdin and close it
        if(!is_null($input)){
            fwrite($pipes[0], $input);
        }

        fclose($pipes[0]);

        stream_set_blocking($pipes[1], 0);
        stream_set_blocking($pipes[2], 0);
        stream_set_blocking($pipes[3], 0);

        // Turn the timeout into microseconds.
        $timeout = $this->timeout * 1000000;

        // Holds special exitCodes that correspond to time out or too much memory usage.
        $specialExitCode = null;


        while ($timeout > 0) {
            $start = microtime(true);

            $read = array($pipes[1], $pipes[2], $pipes[3]);
            $write = NULL;
            $except = NULL;
            stream_select($read, $write, $except, 0, $timeout);

            $outputBuffers['stdout'] .= stream_get_contents($pipes[1], $this->maxSpace);

            if(!proc_get_status($process)['running']) {
                // finished before timeout
                break;
            }

            if(strlen($outputBuffers['stdout']) >= $this->maxSpace) {
                $specialExitCode = STDOUT_BUFFER_OVERFLOW;
                break;
            }

            // Subtract the number of microseconds that we waited.
            $timeout -= (microtime(true) - $start) * 1000000;
        }

        $outputBuffers['stderr'] .= stream_get_contents($pipes[2]);
        proc_terminate($process, 9);

        if($timeout <= 0) {
            $specialExitCode = PROCESS_TIMED_OUT;
        }

        $exitCode = rtrim(stream_get_contents($pipes[3],3));

        fclose($pipes[1]);
        fclose($pipes[2]);
        fclose($pipes[3]);

        $this->cleanStrerr($outputBuffers);

        proc_close($process);
        if(!is_null($specialExitCode)) {
            return $specialExitCode;
        } else {
            return (int)$exitCode;
        }
    }

    private function cleanStrerr(&$outbuffer) {
        $outbuffer['stderr'] = preg_replace('/.*(?=line)/m', '',$outbuffer['stderr']);
    }
}