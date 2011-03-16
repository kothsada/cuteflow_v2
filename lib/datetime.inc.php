<?php
/*******************************************************************************
  *** Module: datetime.inc.php
  *** Creator: Timo Haberkern
  *** Description: Various helper functions for date handling
  ******************************************************************************
  *** @Version: 1.4.1
  ******************************************************************************
  *** Version history:
  *** 1.0.0     10.07.00    Haberkern   First version
  *** 1.1.0     12.12.00    Haberkern   Date validation
  *** 1.2.0     13.12.00    Haberkern   Fix bug in IsDate
  *** 1.3.0     28.12.00    Haberkern   New function "cmpDate"
  *** 1.4.0     02.07.03    Haberkern   New functions: "dateDiff", 
  ***                                   "addDaysToDate", "incMonth", "incDay", 
  ***                                   "decMonth", "decDay", "isLeapYear",
  ***                                   "getDaysOfMonth", "de2en"
  *** 1.4.1     22.07.03    Haberkern   New: "subtractDaysFromDate"
  ******************************************************************************
*/

/*
***********************************************************
* isLeapYear: Checks whether a year is a leap year or not
* @param strYearToCheck - The 4 digit year to check
* @return true if a leap year otherwise false
***********************************************************
*/
function isLeapYear ($strYearToCheck)
{
    $bIsLeapYear = false;
    if ($strYearToCheck % 4 == 0)
    {
        if ($strYearToCheck % 100 == 0)
        {
            if ($strYearToCheck % 400 == 0)
            {
                $bIsLeapYear = true;
            }   
        }
        else
        {
            $bIsLeapYear = true;
        }
    }
    
    return $bIsLeapYear;
}

/*
***********************************************************
* getDaysOfMonth: returns the day-count of a month
* @param strDate - The date
* @return The day-count of the month
***********************************************************
*/
function getDaysOfMonth ($strDate)
{
    //--- split the date
    $arrDate = split("[/.-]", $strDate);
    
    $strMonth = $arrDate[1];
    $strYear = $arrdate[2];
    
    //--- get the days of the month
    $nMonthLength = 0;    
    if ( ($strMonth == 1) || ($strMonth == 3)  || ($strMonth == 5) || ($strMonth == 7) || ($strMonth == 8) || ($strMonth == 10) || ($strMonth == 12))
    {
        $nMonthLength = 31;
    }
    else
    {
        if ($strMonth == 2)
        {
            if (isLeapYear($strYear))
            {
                $nMonthLength = 29;
            }
            else
            {
                $nMonthLength = 28;
            }
        }
        else
        {
            $nMonthLength = 30;
        }
    }
    
    return $nMonthLength;
}

function de2en($strDate)
{
    $arrDate = split("[/.-]", $strDate);
    return $arrDate[2]."-".$arrDate[1]."-".$arrDate[0];        
}


function dateDiff ($strDate1, $strDate2)
{
    $first = strtotime(de2en($strDate1));
    $second = strtotime(de2en($strDate2));
    $diff = abs(($second - $first) / 3600 / 24);

    return $diff;
}

function subtractDaysFromDate ($date, $nDays)
{
    $first = strtotime(de2en($date));
    $newDate = $first - ($nDays * 24 * 3600);
    return date("d.m.Y", $newDate);    
}


function addDaysToDate ($date, $nDays)
{
    $first = strtotime(de2en($date));
    $newDate = $first + ($nDays * 24 * 3600);
    return date("d.m.Y", $newDate);    
}

function incDay ($strDate)
{
    $arrDate = split("[/.-]", $strDate);
    
    $strDay = $arrDate[0];
    $strMonth = $arrDate[1];
    $strYear = $arrDate[2];
    
    $strDay++;
    if ($strDay > getDaysOfMonth($strDate))
    {
        $strDay = 1;
        $arrDateNewMonth = split("[/.-]", incMonth($strDate));
        
        $strMonth = $arrDateNewMonth[1];
        $strYear = $arrDateNewMonth[2];
    }
    return $strDay.".".$strMonth.".".$strYear;    
}

function decDay ($strDate)
{
    $arrDate = split("[/.-]", $strDate);
    
    $strDay = $arrDate[0];
    $strMonth = $arrDate[1];
    $strYear = $arrDate[2];
    
    $strDay--;
    if ($strDay < 1)
    {
        $strNewMonthDate = decMonth($strDate);
        $arrDateNewMonth = split("[/.-]", $strNewMonthDate);
        
        $strDay = getDaysOfMonth($strNewMonthDate);
        $strMonth = $arrDateNewMonth[1];
        $strYear = $arrDateNewMonth[2];
    }
    return $strDay.".".$strMonth.".".$strYear;            
}


function incMonth ($strDate)
{
    $arrDate = split("[/.-]", $strDate);
    
    $strDay = $arrDate[0];
    $strMonth = $arrDate[1];
    $strYear = $arrDate[2];
    
    $strMonth++;
    if ($strMonth > 12)
    {
        $strMonth = 1;
        $strYear++;
    }
    
    return $strDay.".".$strMonth.".".$strYear;
}


function decMonth ($strDate)
{
    $arrDate = split("[/.-]", $strDate);
    
    $strDay = $arrDate[0];
    $strMonth = $arrDate[1];
    $strYear = $arrDate[2];
    
    $strMonth--;
    if ($strMonth < 1)
    {
        $strMonth = 12;
        $strYear--;
    }
    
    return $strDay.".".$strMonth.".".$strYear;
}

//-------------------------------------------
//--- Check if the given string is a valid
//--- date
//-------------------------------------------
//--- returns: true or false
//-------------------------------------------
function isDate($strDate)
{
	$dateElements = split("\.", $strDate);
	
	return checkdate($dateElements[1],$dateElements[0], $dateElements[2]);
}

//--------------------------------------------
//--- Convert a given date from yyyy-mm-dd to
//--- dd.mm.yyyy 
//--------------------------------------------
//--- returns: the converted date
//--------------------------------------------
function convertDateFromDB($date)
{
	if ($date=="")
	{
		return "..";	
	}
	else
	{
		return date($GLOBALS['DATE_FORMAT'],$date);	
	}
}


//--------------------------------------------
//--- returns: timestamp
//--------------------------------------------
function convertOldDateToTimestamp($date)
{
	$dateElements = explode("-", $date);
	
	(int)$dateElements[0] = (int)$date[5].(int)$date[6];
	(int)$dateElements[1] = (int)$date[8].(int)$date[9];
	(int)$dateElements[2] = (int)$date[0].(int)$date[1].(int)$date[2].(int)$date[3];
	
	return mktime(0,0,0, $dateElements[0],$dateElements[1],$dateElements[2]);	
}

function convertMyDateToTimestamp($date)
{
	$dateElements = explode("-", $date);
		
	return mktime(0,0,0, date("$dateElements[1]"),date("$dateElements[0]"),date("$dateElements[2]"));	
}


//--------------------------------------------
//--- Compares two dates
//--------------------------------------------
//--- returns:  0 - the dates are identical
//---           1 - $date2 > $date1
//---          -1 - $date2 < $date1
//--------------------------------------------
function cmpDate($date1, $date2)
{
	$firstdate = split("\.", $date1);
	$seconddate = split("\.", $date2);
	
	$nYear = 0;
	$nMonth = 0;
	$nDay = 0;
	
	//--- check day
	if ($firstdate[0] == $seconddate[0])
	{
		$nDay = 0;	
	}
	else
	{
		if ($firstdate[0] > $seconddate[0])
		{
			$nDay = 0-1;
		}
		else
		{
			$nDay = 1;
		}
	}
	
	//--- check month
	if ($firstdate[1] == $seconddate[1])
	{
		$nMonth = 0;	
	}
	else
	{
		if ($firstdate[1] > $seconddate[1])
		{
			$nMonth = 0-1;
		}
		else
		{
			$nMonth = 1;
		}
	}
	
	//--- check year
	if ($firstdate[2] == $seconddate[2])
	{
		$nYear = 0;	
	}
	else
	{
		if ($firstdate[2] > $seconddate[2])
		{
			$nYear = 0-1;
		}
		else
		{
			$nYear = 1;
		}
	}
	
	
	//--- decide which date is newer
	if ($nYear == 0)
	{
		//--- identical years
		if ($nMonth == 0)
		{
			//--- identical years and month
			if ($nDay == 0)
			{
				//--- complete identical dates
				return 0;
			}	
			else
			{
				return $nDay;
			}
		}
		else
		{
			return $nMonth;
		}
	}	
	else
	{
		return $nYear;
	}
	
	return 0;		
}
?>