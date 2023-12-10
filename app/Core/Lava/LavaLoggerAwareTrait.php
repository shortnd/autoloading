<?php

namespace App\Core\Lava;

use LavaLoggerInterface;

trait LavaLoggerAwareTrait
{
	/**
	 * The logger instance.
	 *
	 * @var LavaLoggerInterface|null;
	 */
	protected $logger = null;

	/**
	 * @param LavaLoggerInterface $logger
	 * @return void
	 */
	public function setLogger(LavaLoggerInterface $logger)
	{
		$this->logger = $logger;
	}
}