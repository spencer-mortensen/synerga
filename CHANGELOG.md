# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [0.0.24] - 2020-06-16
### Changed
- In your "*Factory" class, remove the "getSettings" method and extend "SynergaFactory" to include the Synerga commands
- In your "index.php" file:
	$settings = [
		'data' => $dataDirectory,
		'url' => [
			'base' => $_SERVER['SYNERGA_BASE'],
			'path' => $_SERVER['SYNERGA_PATH']
		]
	];

	$my = new *Factory($settings);
	$my->interpreter->interpret('<:(include ".config/boot/"):>');

## [0.0.23] - 2020-06-09
### Added
- In your data directory, most instances of "(include _path_)" should be converted to "(read _path_)":
  There are now two ways to include a file:
    (read _path_): read an unstructured text value from the _path_ file, evaluating any Synerga markup contained inside, and return the final string value
    (include _path_): include a structured Synerga source code file, which cannot contain any "<::>" markup; this is mainly used in the bootloading

## [0.0.22] - 2020-06-09
### Changed
- MIME types are now handled by the source code, so you should remove your ".config/mime" directory.
