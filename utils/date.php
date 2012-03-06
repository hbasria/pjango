<?

/**
 * Converts string date to date array by using date patterns.
 * 
 * Example usage : $sd->parseDate("Y-m-d H:i:s","2008-4-24 20:28:34");
 * Returns : array(	"Year"=>2008,
 * 					"Month"=>4,
 * 					"Day"=>24,
 * 					"Hour"=>20,
 *					"Minute"=>28,
 * 					"Second"=>34,
 * 					"Timezone"=>"+0300"
 * 					);
 * 
 */
class SimpleDate {
	
	var $date;
	var $tempdate;
	var $patrVal;
	var $timezone;
	private static $days3 = array("Mon","Tue","Wed","Thu","Fri","Sat","Sun");
	private static $days = array("Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday");
	private static $month3 = array("Jan","Feb","Mar","Apr","May","Jun","Jul","Aug","Sep","Oct","Nov","Dec");
	private static $month = array("January","February","March","April","May","June","July","August","September","October","November","December");
	private static $types=array(
		"d"=>"([0-9]{2})",
		"D"=>"([a-zA-z]{3})",
		"j"=>"([0-9]{1,2})",
		"l"=>"([A-Z][a-z]{4,7})",
		"N"=>"([1-7])",
		"S"=>"(st|nd|rd|th)",
		"w"=>"([0-6])",
		"z"=>"([0-9]{3})",
		"W"=>"([0-9]{2})",
		"F"=>"([A-Z][a-z]{2,8})",
		"m"=>"([0-9]{2})",
		"M"=>"([A-Za-z]{3})",
		"n"=>"([0-9]{1,2})",
		"t"=>"(28|29|30|31)",
		"L"=>"(1|0)",
		"o"=>"([0-9]{4})",
		"Y"=>"([0-9]{4})",
		"y"=>"([0-9]{2})",
		"a"=>"(am|pm)",
		"A"=>"(AM|PM)",
		"B"=>"([0-9]{3})",
		"g"=>"([1-12])",
		"G"=>"([0-23])",
		"h"=>"([0-9]{2})",
		"H"=>"([0-9]{2})",
		"i"=>"([0-9]{2})",
		"s"=>"([0-9]{2})",
		"u"=>"([0-9]{1,5})",
		"e"=>"([A-Za-z0-9_]{3,})",
		"I"=>"(1|0)",
		"O"=>"(+[0-9]{4})",
		"P"=>"(+[0-9]{2}:[0-9]{2})",
		"T"=>"([A-Z]{1,4})",
		"Z"=>"(-?[0-9]{1,5})",
		"c"=>"(\d\d\d\d)(?:-?(\d\d)(?:-?(\d\d)(?:[T](\d\d)(?::?(\d\d)(?::?(\d\d)(?:\.(\d+))?)?)?(?:Z|(?:([-+])(\d\d)(?::?(\d\d))?)?)?)?)?)?",
		"r"=>"([a-zA-Z]{2,}),\040(\d{1,})\040([a-zA-Z]{2,})\040([0-9]{4})\040([0-9]{2}):([0-9]{2}):([0-9]{2})\040([+-][0-9]{4})",
		"U"=>"(\d+)"
	);
	
	private $_hours = 0;
	private $_minutes = 0;
	private $_seconds = 0;
	private $_milliseconds = 0;	
	
	private $_year;
	private $_month;
	private $_day;
	private $_weekday;
	private $_hour;
	private $_minute;
	private $_second;
	private $_timezone;	
	



    public static function clearTime(){
        if (isset($this)) $retVal = $this;
        else $retVal = new SimpleDate();
                
        $retVal->_hours = 0;
        $retVal->_minutes = 0;
        $retVal->_seconds = 0;
        $retVal->_milliseconds = 0;   
        
        return $retVal;
    }
	
	public static function parse($date, $dateformat){
	    $newdate="";
	    $dateformat = str_replace(array("\\","\t"),array("@","@t"),$dateformat);
	    
	    $k=0;
	    $datearray = preg_split("//",$dateformat);
	    $patternkey = array();
	    $patrVal = false;
	    for($i=0;$i<count($datearray);$i++){
	        if($datearray[$i-1]=="@"){
	            $patternkey[$i]=$datearray[$i];
	        }
	        elseif($datearray[$i]=="@"){
	            $patternkey[$i]="";
	        }
	        elseif($datearray[$i]==" "){
	            $patternkey[$i]="\040";
	        }
	        elseif(in_array($datearray[$i],array_keys(self::$types))){
	            $patternkey[$i]=self::$types[$datearray[$i]];
	            $patrVal[$k] = array_search($datearray[$i],array_keys(self::$types));
	            $k++;
	        }else{$patternkey[$i]=$datearray[$i];
	        }
	    }
	    
	    $pattern = "/".implode("",$patternkey)."/";
	    preg_match_all($pattern,$date,$newdate);
	    $newdate = array_slice($newdate,1);
	    if($patrVal[0]==34){
	        $resultvar = array("Year"=>$newdate[0],
	    			"Year"=>$newdate[0][0],
	    			"Month"=>$newdate[1][0],
	    			"Day"=>$newdate[2][0],
	    			"Hour"=>$newdate[3][0],
	    			"Minute"=>$newdate[4][0],
	    			"Second"=>$newdate[5][0],
	    			"Timezone"=>$newdate[6][0].$newdate[7][0].$newdate[8][0]);
	    }elseif($patrVal[0]==35){
	        $resultvar = array("Year"=>$newdate[0],
	    			"Year"=>$newdate[3][0],
	    			"Month"=>(array_search($newdate[2][0],self::$month3)+1),
	    			"Day"=>$newdate[1][0],
	    			"Hour"=>$newdate[4][0],
	    			"Minute"=>$newdate[5][0],
	    			"Second"=>$newdate[6][0],
	    			"Timezone"=>$newdate[7][0]);
	    }elseif($patrVal[0]==36){
	        $result = getdate(mktime($newdate));
	        $resultvar = array(
	    			"Year"=>$result["year"],
	    			"Month"=>array_search($result["month"],self::$month)+1,
	    			"Day"=>$result["mday"],
	    			"Hour"=>$result["hours"],
	    			"Minute"=>$result["minutes"],
	    			"Second"=>$result["seconds"],
	    			"Timezone"=>date("O"));
	    }else{
	        $labels = array_keys(self::$types);
	        for($i=0;$i<count($newdate);$i++)$result[$labels[$patrVal[$i]]]=$newdate[$i][0];
	        if($result["F"]) $month = array_search($result["F"],self::$month)+1;
	        elseif($result["M"]) $month = array_search($result["M"],self::$month3)+1;
	        elseif($result["m"]) $month = $result["m"];
	        elseif($result["n"]) $month = $result["n"];
	        if($result["d"]) $day = $result["d"];
	        elseif($result["j"]) $day = $result["j"];
	        if($result["Y"]) $year = $result["Y"];
	        elseif($result["o"]) $year = $result["o"];
	        elseif($result["y"]) $year = ($result["y"]>substr(date("Y",time()),2,2))?(substr(date("Y",time()),0,2)-1).$result["y"]:substr(date("Y",time()),0,2).$result["y"];
	        if($result["l"]) $weekday = array_search($result["l"],self::$days)+1;
	        elseif($result["D"]) $weekday = array_search($result["D"],self::$days3)+1;
	        elseif($result["N"]) $weekday = $result["N"];
	        elseif($result["w"]) $weekday = $result["w"];
	        else $weekday = @date("w",mktime(0,0,0,$month,$day,$year));
	        if($result["H"]) $hour = $result["H"];
	        elseif ($result["G"]) $hour = $result["G"];
	        elseif ($result["h"]) $hour = ($result["A"]=="PM"|$result["a"]=="pm")?($result["h"]+12):($result["h"]);
	        elseif ($result["g"]) $hour = ($result["A"]=="PM"|$result["a"]=="pm")?($result["g"]+12):($result["g"]);
	        if($result["O"]) $timezone = $result["O"];
	        elseif ($result["Z"]) $timezone = ($result["Z"]/3600);
	        else $timezone = date("O");
	        $minutes = $result["i"];
	        $seconds = $result["s"];
	        $resultvar = array(
	    			"Year"=>$year,
	    			"Month"=>$month,
	    			"Day"=>$day,
	    			"WeekDay"=>$weekday,
	    			"Hour"=>$hour,
	    			"Minute"=>$minutes,
	    			"Second"=>$seconds,
	    			"Timezone"=>$timezone);
	    }	    
	    
	    $retVal = self::clearTime();
	    
	    $retVal->_year      = $resultvar['Year'];
	    $retVal->_month     = $resultvar['Month'];
	    $retVal->_day       = $resultvar['Day'];
	    $retVal->_weekday   = $resultvar['WeekDay'];
	    $retVal->_hour      = $resultvar['Hour'];
	    $retVal->_minute    = $resultvar['Minute'];
	    $retVal->_second    = $resultvar['Second'];
	    $retVal->_timezone  = $resultvar['Timezone']; 
	    
	    return $retVal;	    
	}
	
	function toString($pattern = 'Y-m-d H:i:s'){
	    return date($pattern, mktime($this->_hour ,$this->_minute, $this->_second, $this->_month, $this->_day, $this->_year));
	}
	
	/**
	 * Internal function which generates regex pattern from date pattern
	 *
	 * @param string $dateformat
	 * @return string
	 */
	function generatePattern($dateformat){
		$k=0;
		$datearray = preg_split("//",$dateformat);
		$patternkey = array();
		for($i=0;$i<count($datearray);$i++){ 
			if($datearray[$i-1]=="@"){ $patternkey[$i]=$datearray[$i];}
			elseif($datearray[$i]=="@"){$patternkey[$i]="";}
			elseif($datearray[$i]==" "){$patternkey[$i]="\040";}
			elseif(in_array($datearray[$i],array_keys($this->types))){
				$patternkey[$i]=$this->types[$datearray[$i]];
				$this->patrVal[$k] = array_search($datearray[$i],array_keys($this->types));
				$k++;
			}else{$patternkey[$i]=$datearray[$i];}
		}
		$patternkey = implode("",$patternkey);
		return "/".$patternkey."/";
	}
	
	function diffDate($pattern1,$date1,$pattern2,$date2){
		$pdate1 = $this->parseDate($pattern1,$date1);
		$pdate2 = $this->parseDate($pattern2,$date2);
		$compare = $this->Compare($pdate1,$pdate2);
		if($compare==1){
			$ndate = $pdate1;
			$odate = $pdate2;
		}elseif($compare==-1){
			$ndate = $pdate2;
			$odate = $pdate1;
		}else{
			return array("Year"=>0,"Month"=>0,"Day"=>0,"Hour"=>0,"Minute"=>0,"Second"=>0);
		}
		$hour = intval($ndate["Hour"])-intval($odate["Hour"]);
		$minute = intval($ndate["Minute"])-intval($odate["Minute"]);
		$second = intval($ndate["Second"])-intval($odate["Second"]);
		$month = intval($ndate["Month"])-intval($odate["Month"]);
		$day = intval($ndate["Day"])-intval($odate["Day"]);
		$year = intval($ndate["Year"])-intval($odate["Year"]);
		$difference = mktime($hour,$minute,$second,($month+1),($day+1),($year+1970));
		$result = getdate($difference);
			$resultvar = array(
			"Year"=>$result["year"]-1970,
			"Month"=>array_search($result["month"],$this->month),
			"Day"=>$result["mday"]-1,
			"Hour"=>$result["hours"],
			"Minute"=>$result["minutes"],
			"Second"=>$result["seconds"],
			"Timezone"=>$this->timezone);
		return $resultvar;
	}

	
	/**
	 * Not Completed localization function. Adds time to result which is defined by user in $this->timezone,
	 *
	 * @param array $date
	 * @return array
	 */
	function Localize($date){
		$zonehour = $date["Hour"] + intval(substr($this->timezone,0,1).substr($this->timezone,1,2));
		$zonemin = $date["Minute"] + intval(substr($this->timezone,3,2));
		$newdate = mktime($zonehour,$zonemin,intval($date["Second"]),intval($date["Month"]),intval($date["Day"]),intval($date["Year"])); //Burada bir hata oldu.
		$result = getdate($newdate);
			$resultvar = array(
			"Year"=>$result["year"],
			"Month"=>array_search($result["month"],$this->month)+1,
			"Day"=>$result["mday"],
			"Hour"=>$result["hours"],
			"Minute"=>$result["minutes"],
			"Second"=>$result["seconds"],
			"Timezone"=>"");
		return $resultvar;
	}
	/**
	 * Compares two date, returns 1 if first is bigger, -1 if second is bigger and 0 if they are same
	 *
	 * @param array $date1
	 * @param array $date2
	 * @return int
	 */
	function Compare($date1,$date2){
		if($date1["Year"]>$date2["Year"]) return 1;
		if($date1["Year"]<$date2["Year"]) return -1;
		if($date1["Month"]>$date2["Month"]) return 1;
		if($date1["Month"]<$date2["Month"]) return -1;
		if($date1["Day"]>$date2["Day"]) return 1;
		if($date1["Day"]<$date2["Day"]) return -1;
		if($date1["Hour"]>$date2["Hour"]) return 1;
		if($date1["Hour"]<$date2["Hour"]) return -1;
		if($date1["Minute"]>$date2["Minute"]) return 1;
		if($date1["Minute"]<$date2["Minute"]) return -1;
		if($date1["Second"]>$date2["Second"]) return 1;
		if($date1["Second"]<$date2["Second"]) return -1;
		return 0;
	}
}

?>