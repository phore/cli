<?php
    /**
     * Created by PhpStorm.
     * User: matthes
     * Date: 30.06.16
     * Time: 22:07
     */

    namespace gis\cli;


    use gis\core\exception\GisException;

    class ProcLock {

        private $mLockFile;

        private $pid;

		function __construct($lockfile) {
            $this->mLockFile = $lockfile;
			$this->lock();
        }


		private function isRunning($pid) {
			$pids = explode(PHP_EOL, `ps -e | awk '{print $1}'`);
			if(in_array($pid, $pids))
				return TRUE;
			return FALSE;
		}

		private function lock() {
			$lock_file = $this->mLockFile;

			if(file_exists($lock_file)) {
				$pid = file_get_contents($lock_file);
				if($this->isRunning($pid)) {
					throw new GisException("ProcLock: Process already running (Lockfile: '$lock_file')");
    	    	} else {

				}
			}

			$this->pid = getmypid();
			file_put_contents($lock_file, $this->pid);
			return $this->pid;
		}

		public function unlock() {
			$lock_file = $this->mLockFile;

			if(file_exists($lock_file))
				unlink($lock_file);
			return TRUE;
		}

    }