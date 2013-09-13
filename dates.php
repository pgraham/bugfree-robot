<?php
namespace bugfree\robot;

use \DateTime;

function getMonthRange($month = null) {

	if ($month === null) {
		$month = new DateTime();
	}

	$start = clone $month;
	$end = clone $month;

	$start->modify('first day of this month');
	$end->modify('last day of this month');

	return [ $start, $end ];
}
