<?php
namespace bugfree\robot;

use \Symfony\Component\Yaml\Parser as YamlParser;

function loadProperties() {
	$parser = new YamlParser;

	$localPath = $_SERVER['HOME'] . '/.my-hg-log.yaml';
	$localProps = $parser->parse(file_get_contents($localPath));

	return $localProps;
}
