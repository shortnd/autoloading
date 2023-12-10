<?php

namespace App\Core\Lava;

class LavaLogger extends LavaAbstractLogger
{
	private $file;
	public function __construct(string $file, string $mask = LavaLogLevel::DEBUG)
	{
		$this->file = $file;
		$this->setLogMask($mask);
	}
	public function log($level, string $message, array $context = [])
	{
		$fh = fopen($this->file, 'ab');
		if ($this->canBeLogged($level)) {
			fwrite(
			$fh,
			strtr("%datetime% [%level%] %message% %context%\n", [
				"%datetime%" => date('M d H:i:s'),
				"%level%" => $level,
				"%message%" => $message,
				"%context%" => count($context) ? json_encode($context) : '',
			])
		);
		}
		fclose($fh);
	}
}