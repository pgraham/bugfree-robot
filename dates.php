<?php
namespace bugfree\robot;

use \DateTime;

$months = [
	'january', 'jan',
	'february', 'feb',
	'march', 'mar',
	'april', 'apr',
	'may',
	'june', 'jun',
	'july', 'jul',
	'august', 'aug',
	'september', 'sep',
	'october', 'oct',
	'november', 'nov',
	'december', 'dec'
];

$monthsReFrag = '(' . implode('|', $months) . ')';
$monthRe = "/$monthsReFrag( \d{4})?|\d{4}-\d{2}/";

/**
 * Return true is the given date string only specifies the month portion of a
 * date.
 */
function isMonth($dateStr) {
	global $monthRe;
	return preg_match($monthRe, $dateStr);
}

function getMonthRange($month = null) {

	if ($month === null) {
		$month = new DateTime();
	} else if (is_string($month)) {
		$month = new DateTime($month);
	}

	$start = clone $month;
	$end = clone $month;

	$start->modify('first day of this month');
	$end->modify('last day of this month');

	return [ $start, $end ];
}

function parseDateRange($start, $end) {

	if (isMonth($start)) {
		$start = new DateTime($start);
		$start->modify('first day of this month');
	} else {
		$start = new DateTime($start);
	}

	if (isMonth($end)) {
		$end = new DateTime($end);
		$end->modify('last day of this month');
	} else {
		$end = new DateTime($end);
	}

	return [ $start, $end ];
}
