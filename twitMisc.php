<?php

define('INT_SECOND', 1);
define('INT_MINUTE', 60);
define('INT_HOUR', 3600);
define('INT_DAY', 86400);
define('INT_WEEK', 604800);



 function get_formatted_timediff($then, $now = false)
{
    $now      = (!$now) ? time() : $now;
    $timediff = ($now - $then);
    $weeks    = (int) intval($timediff / INT_WEEK);
    $timediff = (int) intval($timediff - (INT_WEEK * $weeks));
    $days     = (int) intval($timediff / INT_DAY);
    $timediff = (int) intval($timediff - (INT_DAY * $days));
    $hours    = (int) intval($timediff / INT_HOUR);
    $timediff = (int) intval($timediff - (INT_HOUR * $hours));
    $mins     = (int) intval($timediff / INT_MINUTE);
    $timediff = (int) intval($timediff - (INT_MINUTE * $mins));
    $sec      = (int) intval($timediff / INT_SECOND);
    $timediff = (int) intval($timediff - ($sec * INT_SECOND));

    $str = '';
    if ( $weeks )
    {
        $str .= intval($weeks);
        $str .= ($weeks > 1) ? ' weeks' : ' week';
    }

    if ( $days )
    {
        $str .= ($str) ? ',' : '';
        $str .= intval($days);
        $str .= ($days > 1) ? ' days' : ' day';
    }

    if ( $hours )
    {
        $str .= ($str) ? ' ' : '';
        $str .= intval($hours);
        $str .= ($hours > 1) ? ' hours' : ' hour';
    }

    if ( $mins )
    {
        $str .= ($str) ? ',' : '';
        $str .= intval($mins);
        $str .= ($mins > 1) ? ' minutes' : ' minute';
    }

    if ( $sec )
    {
        $str .= ($str) ? ' and ' : '';
        $str .= intval($sec);
        $str .= ($sec > 1) ? ' seconds' : ' second';
    }
    
    if ( !$weeks && !$days && !$hours && !$mins && !$sec )
    {
        $str .= 'just a second';
    }

    
    return $str;
}
  
/**
 * This function calculates the timediffrence between that a message was
 * send to twitter and now and returns the diffrence in a string.
 *
 * @param string $sTimeString
 * @return string
 */
function twitme_gettimedistance ($sTimeString)
{
		/**
		 * For those developers reading this function saying uuhm ?
		 * Because of the diffrence in strtotime() between php4 and php5
		 * i had to do a litle hacking here.
		 */
		$aTmp = explode(' ',$sTimeString);
		$aTime= explode(':',$aTmp[3]);

		
		$day   = (int)$aTmp [2];
		$month = (int)date('m', strtotime ("%m", (int)$aTmp [1]));
		$year  = (int)$aTmp [5];
		$hour  = (int)$aTime [0];
		$min   = (int)$aTime [1];
		$sec   = (int)$aTime [2];
		$StartDate = mktime($hour, $min, $sec, $month, $day,$year);
		
		return get_formatted_timediff ($StartDate );
		
}
?>