<?php

namespace SpencerMortensen\Autoloader;

$project = __DIR__;

$classes = array(
	'Synerga' => 'src',
	'SpencerMortensen\\Html5' => 'vendor/spencer-mortensen/html5/src'
);

require "{$project}/vendor/spencer-mortensen/autoloader/src/Autoloader.php";

new Autoloader($project, $classes);
