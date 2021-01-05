# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [0.0.34] - 2021-01-05
### Changed
 - Renamed command: (html) => (body)
 - Renamed file: ".page/.html" => ".page/.body"
	You can convert your data directory like this:
	cd data/ && find . -type f -print0 | xargs -0 rename 's/\/.html$/\/.body/'


## [0.0.33] - 2020-12-22
### Changed
 - Renamed command: (base) => (site)
### Added
 - Added two new commands:
	(and {operand})
	(string {operand})

## [0.0.32] - 2020-12-04
### Added
 - Add a new command:
	(menu-up)

## [0.0.31] - 2020-07-10
### Changed
 - No changes

## [0.0.30] - 2020-07-10
### Added
 - In your "index.php" file, add the "['errors']['level']" setting:
	$settings = [
		'errors' => [
			'level' => E_ALL,
			'display' => true,
			'log' => "{$projectDirectory}/error.log"
		],
		...

## [0.0.29] - 2020-07-03
### Added
 - Added two new commands:
	(equal $a $b)
	(html5-node "a" {"href": $url} "link text")

## [0.0.28] - 2020-06-25
### Added
 - In your "index.php" file, add the "['url']['scheme']" and "['url']['host']" settings:
	$settings = [
		'data' => $dataDirectory,
		'errors' => [
			'display' => true,
			'log' => $logDirectory . '/error.log'
		],
		'url' => [
			'scheme' => $_SERVER['REQUEST_SCHEME'],
			'host' => $_SERVER['HTTP_HOST'],
			'base' => $_SERVER['SYNERGA_BASE'],
			'path' => $_SERVER['SYNERGA_PATH']
		]
	];

## [0.0.27] - 2020-06-17
### Added
 - In your "index.php" file, make these changes:
	$settings = [
		'data' => $dataDirectory,
		'errors' => [
			'display' => true,
			'log' => $logDirectory . '/error.log'
		],
		'url' => [
			'base' => $_SERVER['SYNERGA_BASE'],
			'path' => $_SERVER['SYNERGA_PATH']
		]
	];

	$synerga = new *Factory($settings);
	$synerga->errorHandling;
	$synerga->interpreter->interpret('<:(include ".config/boot/"):>');

## [0.0.26] - 2020-06-17
### Added
- Added a new dependency, for future error handling

## [0.0.25] - 2020-06-16
### Changed
- All keys (e.g. ".title/_") have become files (e.g. ".title"):
	In the "bin/convert" script, edit the data directory path, then run the script to convert your data directory to the new format

## [0.0.24] - 2020-06-16
### Changed
- In your "*Factory" class:
	Extend "SynergaFactory" (to include the Synerga commands)
	Remove the "getSettings" method (because this is now inferred)
	Convert any "get" method calls to object properties. For example:
		Before: $data = $this->get('data');
		After: $this->data;
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
### Changed
- In your data directory, most instances of "(include _path_)" should be converted to "(read _path_)":
  There are now two ways to include a file:
    (read _path_): read an unstructured text value from the _path_ file, evaluating any Synerga markup contained inside, and return the final string value
    (include _path_): include a structured Synerga source code file, which cannot contain any "<::>" markup; this is mainly used in the bootloading

## [0.0.22] - 2020-06-09
### Changed
- MIME types are now handled by the source code, so you should remove your ".config/mime" directory.
