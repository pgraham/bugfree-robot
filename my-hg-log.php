#!/usr/bin/env php
<?php
namespace bugfree\robot;

require 'vendor/autoload.php';
require 'dates.php';
require 'props.php';

$props = loadProperties();

/*
 * =============================================================================
 * Validate config
 * =============================================================================
 */

if (!isset($props['hg']['users'])) {
	echo "You must specify hg.users in ~/.my-hg-log.yaml\n";
	exit;
}
$hgUsers = $props['hg']['users'];

if (!isset($props['hg']['repos'])) {
	echo "You must specify hg.repos in ~/.my-hg-log.yaml\n";
	exit;
}
$hgRepos = $props['hg']['repos'];

/*
 * =============================================================================
 * Normalize config
 * =============================================================================
 */

if (!is_array($hgUsers)) {
	$hgUsers = [ $hgUsers ];
}
if (!is_array($hgRepos)) {
	$hgRepos = [ $hgRepos ];
}

/*
 * =============================================================================
 * Parse date argument
 * =============================================================================
 */

if ($argc <= 1) {
	$dates = getMonthRange();
} else {
	$date = implode(' ', array_slice($argv, 1));

	if (strpos($date, ' to ') !== false) {
		$dates = explode(' to ', $date);

		$start = $dates[0];
		$end = $dates[1];

		$dates = parseDateRange($start, $end);
	} else if (isMonth($date)) {
		$dates = getMonthRange($date);
	} else {
		$dates = new DateTime($date);
	}
}

/*
 * =============================================================================
 * Build hg command to execute remotely.
 * =============================================================================
 */

$hgUsersStr = implode(' ', array_map(function ($user) {
	return "--user \"$user\"";
}, $hgUsers));

if (is_array($dates)) {
	$hgDateStr = '--date "' . $dates[0]->format('Y-m-d') . ' to ' . $dates[1]->format('Y-m-d') . '"';
} else {
	$hgDateStr = '--date "' . $dates->format('Y-m-d') . '"';
}

$hgCmd = "hg log $hgUsersStr $hgDateStr";

/*
 * =============================================================================
 * Get remote log from each repo.
 * =============================================================================
 */

foreach ($hgRepos as $repo) {
	if (preg_match('/^ssh:\/\/(\w+)@([\w.]+)\/(.+)$/', $repo, $matches)) {
		$sshUser = $matches[1];
		$sshHost = $matches[2];
		$sshPath = $matches[3];

		$sshCmd = "ssh $sshUser@$sshHost 'cd $sshPath; $hgCmd'";
		unset($sshOutput, $sshReturn);
		exec($sshCmd, $sshOutput, $sshReturn);

		echo "\n##\n## $repo\n##\n\n";

		if ($sshReturn) {
			echo "Warning: ssh command exited with status: $sshReturn\n";
		}

		if (!empty($sshOutput)) {
			echo implode("\n", $sshOutput) . "\n";
		} else {
			echo "No commits.\n";
		}

	} else {
		echo "Warning: $repo is not a recognized repo URL\n";
	}
}
