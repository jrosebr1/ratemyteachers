<?php

/**
 * @ingroup core
 *
 * @brief
 * Helper used to calculate the human readable time from a supplied
 * date.
 *
 * This class is used to calculate the human readable time from a
 * supplied date such as on Facebook and Twitter. A returned value
 * from this class would look like '4 minutes, 58 seconds ago' or
 * '1 year, 6 months ago'.
 *
 * @author Adrian Rosebrock
 */

class TimeDifference
{
	/**
	 * Calculates the human readable time, such as '12 seconds ago'
	 * or '1 year, 3 months ago'.
	 *
	 * @param $date
	 *  The date that is going to be converted to a human readable
	 *  time, such as the date from a SQL database. The date will
	 *  be converted to a UNIX timestamp via strtotime().
	 * @param $precision
	 *  The precision in which to extend the human readable date.
	 *
	 * @return
	 *  The human readable time.
	 */	
	public static function ago($date, $precision = 2)
	{
		// initialize the string used to store the readable time and
		// the current precision count
		$ago = "";
		$curPrecision = 0;
		
		// define the intervals dictionary (in seconds)
		$intervals = array(
			"year" => 31556926,
			"month" => 2629744,
			"week" => 604800,
			"day" => 86400,
			"hour" => 3600,
			"minute" => 60);
		
		// grab the current time, convert the date to a UNIX timestamp,
		// and calculate the difference between the two times
		$now = time();
		$date = strtotime($date);
		$diff = $now - $date;
		
		// if the difference is less than sixty, then the event just
		// happened
		if ($diff < 60)
		{
			return "just now";
		}
		
		foreach ($intervals as $label => $seconds)
		{
			// if the current precision is equal to the precision needed,
			// then break from the loop
			if ($curPrecision == $precision)
			{
				break;
			}
			
			// count the number of the total interval and then subtract
			// the integer value from the difference
			$total = $diff / $seconds;
			$diff -= (intval($total) * $seconds);
			
			// check if the total is greater than or equal to one
			if ($total >= 1)
			{
				// round the total to the nearest whole number and
				// initialize the plural value
				$total = round($total);
				$plural = "";
				
				// if the total is greater than one, then update
				// the plural value
				if ($total > 1)
				{
					$plural = "s";
				}
				
				// update the readable string and increment the current
				// precision
				$ago .= $total . " " . $label . $plural . ", ";
				$curPrecision++;
			}
		}
		
		// remove the trailing comma and spacen, then finish off the
		// readable string
		$ago = substr($ago, 0, -2);
		$ago .= " ago";
		
		// return the readable string
		return $ago;
	}
}

?>