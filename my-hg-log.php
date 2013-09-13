#!/usr/bin/env php
<?php
namespace bugfree\robot;

require 'vendor/autoload.php';
require 'dates.php';
require 'props.php';

$props = loadProperties();

if (!isset($props['hg']['users'])) {
	echo "You must specify hg.users in ~/.my-hg-log.yaml\n";
	exit; }
$hgUsers = $props['hg']['users'];

if (!isset($props['hg']['repos'])) {
	echo "You must specify hg.repos in ~/.my-hg-log.yaml\n";
}
$hgRepos = $props['hg']['repos'];

if (!is_array($hgUsers)) {
	$hgUsers = [ $hgUsers ];
}
if (!is_array($hgRepos)) {
	$hgRepos = [ $hgRepos ];
}

if ($argc <= 1) {
	$dates = getMonthRange();
} else {
	$dates = implode(' ', array_slice($argv, 1));

	if (strpos($dates, ' to ') !== false) {
		$dates = explode(' to ', $dates);

		// TODO
	}
}

$hgUsersStr = implode(' ', array_map(function ($user) {
	return "--user \"$user\"";
}, $hgUsers));
$hgDateStr = '--date "' . $dates[0]->format('Y-m-d') . ' to ' . $dates[1]->format('Y-m-d') . '"';

$hgCmd = "hg log $hgUsersStr $hgDateStr";

foreach ($hgRepos as $repo) {
	if (preg_match('/^ssh:\/\/(\w+)@([\w.]+)\/(.+)$/', $repo, $matches)) {
		$sshUser = $matches[1];
		$sshHost = $matches[2];
		$sshPath = $matches[3];

		$sshCmd = "ssh $sshUser@$sshHost 'cd $sshPath; $hgCmd'";
		echo "Executing $sshCmd\n";
		unset($sshOutput, $sshReturn);
		exec($sshCmd, $sshOutput, $sshReturn);

		if ($sshReturn) {
			echo "Warning: ssh command exited with status: $sshReturn\n";
		}

		echo implode("\n", $sshOutput) . "\n";

	} else {
		echo "Warning: $repo is not a recognized repo URL\n";
	}
}
