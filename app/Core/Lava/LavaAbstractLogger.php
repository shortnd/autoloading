<?php

namespace App\Core\Lava;

abstract class LavaAbstractLogger implements LavaLoggerInterface
{
	use LavaLoggerTrait;

	private $mask;

	private $logLevels = [
		LavaLogLevel::EMERGENCY,
		LavaLogLevel::ALERT,
		LavaLogLevel::CRITICAL,
		LavaLogLevel::ERROR,
		LavaLogLevel::WARNING,
		LavaLogLevel::NOTICE,
		LavaLogLevel::INFO,
		LavaLogLevel::DEBUG
	];

	public function setLogMask(string $level)
	{
		$this->mask = $level;
	}

	public function getLogMask()
	{
		return $this->mask;
	}

	/**
	 * @param string $level
	 * @return bool
	 */
	public function canBeLogged(string $level)
	{
		$index = array_search($this->getLogMask(), $this->logLevels, true);
		return in_array($level, array_splice($this->logLevels, 0, $index + 1), true);
	}
}