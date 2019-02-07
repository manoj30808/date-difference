<?php

/// ===============================================
/* method : getYearDays
* @param  : $year int
* @description : get year's days vi
*/// ==============================================
function getYearDays($year)
{
	if (($year % 4 == 0) && ($year % 100 != 0) || $year % 400 == 0) {
		return 366;
	} else {
		return 365;
	}
}

/// ===============================================
/* method : getMonthlyDays
* @param  : 
* @description : get months days
*/// ==============================================
function getMonthlyDays()
{
	$monthlyDays = [
		1 => 31,
		2 => 29,
		3 => 31,
		4 => 30,
		5 => 31,
		6 => 30,
		7 => 31,
		8 => 31,
		9 => 30,
		10 => 31,
		11 => 30,
		12 => 31,
	];
	return $monthlyDays;
}