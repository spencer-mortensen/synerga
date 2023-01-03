# Changelog

All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/en/1.0.0/)
and this project adheres to [Semantic Versioning](http://semver.org/spec/v2.0.0.html).

## [0.0.48] - 2023-01-03
### Changed
 - Updated the "spencer-mortensen/exceptions" dependency

## [0.0.47] - 2022-01-31
### Changed
 - In the "link" command, the title paths are now:
	(1) "{$path}.page/.title/"
	(2) "{$path}.title/" (new)

## [0.0.46] - 2021-05-26
### Changed
 - Fixed a bug in the error logging.

## [0.0.45] - 2021-05-26
### Added
 - Added $settings['cache']:
	true: files are immutable (must use file versioning) (for production)
	false: files are never cached (for development)
	null: allow client/server to use their own caching heuristics

## [0.0.44] - 2021-05-26
### Changed
 - $settings['url']['page'] has been renamed to $settings['url']['path']

## [0.0.43] - 2021-05-25
### Changed
 - In the "url" command: the "path" argument is now optional.

## [0.0.42] - 2021-05-10
### Removed
 - Removed the "math-line" command. Use '(math "tex" true)' instead.

## [0.0.41] - 2021-05-10
### Changed
 - Files are now treated as immutable: use versioning (e.g. "style.2.css") when updating files.

## [0.0.40] - 2021-05-03
### Changed
 - Fonts are no longer preloaded by the MathJax library: you should preload the actual resources you'll need

## [0.0.39] - 2021-05-02
### Changed
 - The MathJax library now loads faster

## [0.0.38] - 2021-04-29
### Added
 - Added the "link" command, which creates an HTML link to a page using the page title as the text:
	(link path)
 - Added the "up" command, which returns the ancestor of any given path (optionally going up a given number of directories)
	(up path)
	(up path 2)
### Changed
 - In the "match" command, the first two arguments have been flipped
### Removed
 - Removed the "menu-up" comand. Use this instead:
	(link (up path))

## [0.0.37] - 2021-03-01
### Added
 - Added a new command, which returns the current UNIX time:
	(time)
### Changed
 - In the command "(file path alternative)", the "alternative" parameter was removed.
     Add an "(exists path)" condition to your code!

## [0.0.36] - 2021-02-11
### Added
 - Added two new commands:
	(js "path/script.js" "defer")
	(css "path/style.css")
### Changed
 - Merged the ".js" and ".css" files (which contained paths) into a new ".head" file (which contains head HTML)

## [0.0.35] - 2021-01-29
### Added
 - Added an optional "timeZone" argument to the "date" command:
	(date 0 "Y-m-d" "GMT")


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
