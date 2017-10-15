<?php

namespace Synerga;

spl_autoload_register(
	function ($class)
	{
		$namespacePrefix = 'Synerga\\';
		$namespacePrefixLength = strlen($namespacePrefix);

		if (strncmp($class, $namespacePrefix, $namespacePrefixLength) !== 0) {
			return;
		}

		$relativeClassName = substr($class, $namespacePrefixLength);
		$relativeFilePath = strtr($relativeClassName, '\\', '/') . '.php';
		$absoluteFilePath = __DIR__ . "/src/{$relativeFilePath}";

		if (is_file($absoluteFilePath)) {
			include $absoluteFilePath;
		}
	}
);

$synerga = new Synerga();
$synerga->run('<:router:>');
