<?php
/**
 * Created by PhpStorm.
 * User: laurenz
 * Date: 23.05.14
 * Time: 10:57
 */

    namespace Phore\Cli;
use gis\core\exception\GisException;


/**
 * @package gis\net\ssh
 *
 *
 * SshRemoteCommand generally outputs following syntax:
 * [COMMAND] [OPTIONS (maybe key-value-pair or a single option)] [ARGUMENTS]
 */
class ShellCommand {

    private $mCommand;
    private $mOptions = [];
    private $mArguments = [];
    private $mWriteCommandExitCode = FALSE;


    /**
     * @param string $cmd
     * @return ShellCommand
     */
    public static function newInstance($cmd) {
        return new self($cmd);
    }

    /**
     * @param string $cmd
     */
    public function __construct($cmd) {
        $this->mCommand = $cmd;
    }

    /**
     * @param string $arg
     * @return string
     */
    private function escape($arg) {
        return escapeshellarg($arg);
    }


    /**
     * @param string $logFilePath
     * @return $this
     */
    public function writeCommandExitCode($logFilePath) {
        $this->mWriteCommandExitCode = $logFilePath;
        return $this;
    }

    /**
     * @param $argument
     * @param bool $escape
     * @return $this
     */
    public function addArgument($argument, $escape=TRUE) {
        if ($escape) {
            $argument = $this->escape($argument);
        }
        $this->mArguments[] = $argument;
        return $this;
    }

    /**
     * @param $option
     * @param $argument
     * @return $this
     */
    public function addOption($option, $argument=NULL) {
        if ($argument !== NULL) {
            $argument = $this->escape($argument);
        }
        $this->mOptions[$option] = $argument;
        return $this;
    }
    
    /**
     * @return string
     * @throws GisException
     */
    public function execAndReturnOnLocalhost() {
        $ret = -1;
        $out = "";
        exec($this->__toString(), $out, $ret);
        if ($ret != 0) {
            throw new GisException("Error ({$ret}): {$out}");
        }
        return $out;
    }

    /**
     * @return string
     */
    public function __toString() {
        $options = "";
        foreach($this->mOptions as $singleOption => $optionArgument) {
            if ($optionArgument !== NULL) {
                $singleOption .= " {$optionArgument}";
            }
            $options .= " {$singleOption}";
        }

        $commandStr = "{$this->mCommand} {$options} " . implode(" ", $this->mArguments);
        if ($this->mWriteCommandExitCode !== FALSE) {
            $commandStr .= "; echo -en $? > {$this->mWriteCommandExitCode}";
        }
        return $commandStr;
    }

}
