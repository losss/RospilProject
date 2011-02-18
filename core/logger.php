<?php

class Logger {
    const DEBUG = 1;
    const PROFILING = 2;
    const WARNING = 8;
    const LOGIC = 9;
    const SEVERE = 10;
    public static $debug = '';

    private static $instance;

    private $out;
    private $level;

    function __construct() {
        if (Settings::$LOG_FILE) {
            $fileName = Settings::$LOG_FILE;
            //if ((SERVERENV == 'dev' || SERVERENV == 'stage') && isset($_SERVER['REMOTE_ADDR'])) {
            //    $fileName = $fileName.$_SERVER['REMOTE_ADDR'];
            //}
            if (!is_file($fileName)) {
                $path = pathinfo($fileName, PATHINFO_DIRNAME);
                if (!is_dir($path)) {
                    mkdir($path, 0777, true);
                }
            } else if (filesize($fileName) >= Settings::$LOG_FILE_MAX_SIZE) {
                @unlink($fileName.'save');
                @rename($fileName, $fileName.'.save');
            }
            $this->out = @fopen($fileName, 'a');
        }
        $this->level = Settings::$LOG_LEVEL;
    }

    function __destruct() {
    }

    public static function log() {
        if (!self::$instance) {
            self::$instance = new Logger();
        }
        self::$instance->logThis(func_get_args());
    }

    protected function logThis($args) {
        $message = '';
        //$args = func_get_args();

        $len = count($args);
        $severity = self::DEBUG;
        if ($len) {
            $indx = 0;
            if ($len > 1 && is_int($args[0])) {
                $severity = $args[$indx++];
            }
            $stack = null;
            $i = $indx;
            for (; $i < $len; $i++) {
                if (is_a($args[$i], 'Exception')) {
                    $severity = self::SEVERE;
                    $stack = $args[$i]->getTrace();
                    $args[$i] = $args[$i]->getMessage();
                    break;
                }
            }

            if ($severity >= $this->level) {
                if ($severity == self::SEVERE) {
                    if (!$stack) {
                        $stack = debug_backtrace();
                    }
                    $div = '';
                    for ($i = count($stack) - 1; $i >= 0; $i--) {
                        $trace = $stack[$i];
                        $message .= $div.(isset($trace['file']) ? $trace['file'] : '')
                                         .(isset($trace['line']) ? ' at '.$trace['line'].'' : '');
                        $div = "\n";
                    }
                    $message .= "\n";
                }
                $div = '';
                for (; $indx < $len; $indx++) {
                    if (is_array($args[$indx]) || is_object($args[$indx])) {
                        $msg = self::getValue($args[$indx]);
                    } else {
                        $msg = $args[$indx];
                    }

                    $message .= $div.$msg;
                    $div = ' ';
                }

                if ($this->out) {
                    $mark = ' ';
                    if ($severity == self::WARNING) {
                        $mark = '?';
                    } else if ($severity >= self::LOGIC) {
                        $mark = '!';
                    }
                    @fwrite($this->out, date("m/d/Y H:i:s").(isset($_SERVER['REMOTE_ADDR']) ? ' '.$_SERVER['REMOTE_ADDR'].':'.str_pad($_SERVER['REMOTE_PORT'], 5, ' ') : '')." $mark $message\n");
                } else {
                    self::$debug .= $message."\n";
                }
            }
        }
    }
    
    private function getValue($val) {
        $result = '';
        if (is_array($val)) {
            $div = '';
            foreach ($val as $k => $v) {
                $result .= $div.$k.' => '.self::getValue($v);
                $div = "\n";
            }
        } else if (is_object($val) && !method_exists($val, '__toString')) {
            $result = print_r($val, true);
        } else {
            $result = $val;
        }

        return $result;
    }
} 

?>
