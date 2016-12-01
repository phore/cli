<?php

    namespace Phore\Cli;

	use gis\core\di\DiContainer;

	class CliCommand {
		
		
		private $myCliSelectStr;
		private $myDescription;
		/**
		 * 
		 * Enter description here ...
		 * @var CliArgumentContainer
		 */
		private $myArgumentContainer;
		
		
		private $myTargetFunction;

		/**
		 * CliCommand constructor.
		 * @param $cliSelectStr
		 * @param $description
		 * @param $args array|CliArgumentContainer
		 * @param $function
		 */
		public function __construct ($cliSelectStr, $description, $args, callable $function) {
			$this->myCliSelectStr = $cliSelectStr;
			$this->myDescription = $description;

			if (is_array($args)) {
				$tmpArgs = new CliArgumentContainer();
				foreach ($args as $arg)
					$tmpArgs->addArgument($arg);
				$args = $tmpArgs;
			}
			if ( ! $args instanceof CliArgumentContainer)
				throw new \InvalidArgumentException("Parameter 3 of CliCommand::__constructor must be array or CliArgumentContainer");

			/**
			 * 
			 * Enter description here ...
			 * @var CliArgumentContainer
			 */
			$this->myArgumentContainer = $args;
			$this->myTargetFunction = $function;
		}
		
		public function isMySelectStr ($str) {
			if ("--".$this->myCliSelectStr == $str)
				return true;
			return false;
		}
		
		
		
		public function printHelp () {
			
			printf ("\n %-30s %s", "  --".$this->myCliSelectStr, $this->myDescription);
			
			$this->myArgumentContainer->printDescription();
			
		}
		
		
		public function dispatch ($args, DiContainer $diContainer) {
			if (@$args[0] == "-h") {
				$this->printHelp();
				return;
			}
			//print_r($this->myTargetFunction);
			$this->myArgumentContainer->parseArguments($args);

			$overrideInterface = [CliArgumentContainer::class=>$this->myArgumentContainer];
			foreach ($this->myArgumentContainer->getArgumentNames() as $curName) {
				$overrideInterface["§{$curName}"] = $this->myArgumentContainer->getArgumentByName($curName);
			}

			$diContainer->callArr($this->myTargetFunction, [], $overrideInterface);
		}

	}
