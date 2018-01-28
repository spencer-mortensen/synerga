<?php

namespace Synerga;

call_user_func(function () {
	$projectDirectory = __DIR__;
	$vendorDirectory = "{$projectDirectory}/vendor";

	$classes = array(
		'Synerga' => "{$projectDirectory}/src",
		'SpencerMortensen\\Html5' => "{$vendorDirectory}/spencer-mortensen/html5/src",
	);

	foreach ($classes as $namespacePrefix => $libraryPath) {
		$namespacePrefix .= '\\';
		$namespacePrefixLength = strlen($namespacePrefix);

		$autoloader = function ($class) use ($namespacePrefix, $namespacePrefixLength, $libraryPath) {
			if (strncmp($class, $namespacePrefix, $namespacePrefixLength) !== 0) {
				return;
			}

			$relativeClassName = substr($class, $namespacePrefixLength);
			$relativeFilePath = strtr($relativeClassName, '\\', '/') . '.php';
			$absoluteFilePath = "{$libraryPath}/{$relativeFilePath}";

			if (is_file($absoluteFilePath)) {
				include $absoluteFilePath;
			}
		};

		spl_autoload_register($autoloader);
	}
});

$synerga = new Synerga();
$synerga->run('<:router:>');
