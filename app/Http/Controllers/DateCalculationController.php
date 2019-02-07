<?php

namespace App\Http\Controllers;

use App\DateCalculation;
use Illuminate\Http\Request;

class DateCalculationController extends Controller
{
    /**
     * Calculate between two dates
     *
     * @param array
     */
    public function calculate(Request $request)
    {
    	$this->validate($request, [
    		'start_date' => 'required',
    		'end_date' => 'required',
    	]);

    	// get monthly days array
    	$monthlyDays = getMonthlyDays();

    	$startArray = explode('/', $request->start_date);
		$endArray = explode('/', $request->end_date);

		$startDate = (int)$startArray[0];
		$endDate = (int)$endArray[0];
		$startMonth = (int)$startArray[1];
		$endMonth = (int)$endArray[1];
		$startYear = (int)$startArray[2];
		$endYear = (int)$endArray[2];

		// get yearly days array
		$totalDaysInYears = [];
		$leap_years = [];
		for ($i = $startYear; $i < $endYear + 1; $i++) {
			$totalDaysInYears[$i] = getYearDays($i);
			if ($totalDaysInYears[$i]==366) {
				$leap_years[] = $i;
			}
		}

		// If both year and months are same
		if (($startYear == $endYear) && ($startMonth == $endMonth)) {
			$finalDays = $endDate - $startDate;
		// If only year is same
		} elseif ($startYear == $endYear) {
			// get total monthly days
			$finalDays = 0;
			for ($i = $startMonth; $i < $endMonth + 1; $i++) {
				if ($i == $startMonth) {
					$finalDays += $monthlyDays[$i] - $startDate;
				} elseif ($i == $endMonth) {
					$finalDays += $endDate;
				} else {
					$finalDays += $monthlyDays[$i];
				}
			}
			// reduce 1 day from total days of year if year isn't leap and start month is 1 or 2 or end month is not 1 or 2
			if (($totalDaysInYears[$startYear] == 365) && (in_array($startMonth, [1, 2])) && (!in_array($endMonth, [1, 2]))) {
				$finalDays -= 1;
			}
		} else {
			// ========== Start years days count ========== //
			// get total monthly days
			$totalStartYearsDays = 0;
			for ($i = $startMonth + 1; $i < 13; $i++) {
				$totalStartYearsDays += $monthlyDays[$i];
			}

			// get start months current days and add to total days in start year
			$totalStartYearsDays += $monthlyDays[$startMonth] - $startDate;

			// reduce 1 day from total days of year if year isn't leap and start month is 1 or 2
			if (($totalDaysInYears[$startYear] == 365) && (in_array($startMonth, [1, 2]))) {
				$totalStartYearsDays -= 1;
			}

			// ========== End years days count ========== //
			// get total monthly days
			$totalEndYearsDays = 0;
			for ($i = 1; $i < $endMonth; $i++) {
				$totalEndYearsDays += $monthlyDays[$i];
			}

			// get start months current days and add to total days in start year
			$totalEndYearsDays += $endDate;

			// reduce 1 day from total days of year if year isn't leap and end month is not 1 or 2
			if (($totalDaysInYears[$endYear] == 365) && (!in_array($endMonth, [1, 2]))) {
				$totalEndYearsDays -= 1;
			};
			// print_r($totalDaysInYears[0] - $totalStartYearsDays $totalEndYearsDays);exit();

			// total days between two dates
			$finalDays = 0;
			foreach ($totalDaysInYears as $year => $days) {
				if ($year == $startYear) {
					$finalDays += $totalStartYearsDays;
				} elseif ($year == $endYear) {
					$finalDays += $totalEndYearsDays;
				} else {
					$finalDays += $days;
				}
			}
		}

		// if end date option included or not
		if ($request->end_date_included == true) {
			$end_date_included = 1;
			$finalDays += 1;
		} else {
			$end_date_included = 0;
		}

		// save to database
		$dateCalculation = new DateCalculation;
		$dateCalculation->start_date = $request->start_date;
		$dateCalculation->end_date = $request->end_date;
		$dateCalculation->end_date_included = $end_date_included;
		$dateCalculation->difference = $finalDays;
		$dateCalculation->created_at = now();
		$dateCalculation->save();

		$msg = 'Total Days difference: '.$finalDays;
		if (!empty($leap_years)) {
			$msg = 'Total Days difference: '.$finalDays.' Leap year '.sizeof($leap_years).' detected :'.implode(',', $leap_years);
		}
    	return response()->json(['success' => true, 'message' => $msg]);
    }
}
