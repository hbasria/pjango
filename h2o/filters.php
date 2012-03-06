<?php

$aylarIng=array("January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December");
$aylarKisaIng=array("Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec");
$gunlerIng=array("Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday", "Sunday");
$gunlerKisaIng=array("Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun");
$aylar=array("Ocak", "Şubat", "Mart", "Nisan", "Mayıs", "Haziran", "Temmuz", "Ağustos", "Eylül", "Ekim", "Kasım", "Aralık");
$aylarKisa=array("Oca", "Şub", "Mar", "Nis", "May", "Haz", "Tem", "Ağu", "Eyl", "Eki", "Kas", "Ara");
$gunler=array("Pazartesi", "Salı", "Çarşamba", "Perşembe", "Cuma", "Cumartesi", "Pazar");
$gunlerKisa=array("Pt", "Sa", "Ça", "Pe", "Cu", "Ct", "Pa");

class FilterCollection {};


class CoreFilters extends FilterCollection {
    static function first($value) {
        return $value[0];
    }
    
    static function last($value) {
        return $value[count($value) - 1];
    }
    
    static function join($value, $delimiter = ', ') {
        return join($delimiter, $value);
    }
    
    static function urlencode($data) {
        if (is_array($data)) {
            $result;
            foreach ($data as $name => $value) {
                $result .= $name.'='.urlencode($value).'&'.$querystring;
            }
            $querystring = substr($result, 0, strlen($result)-1);
            return htmlspecialchars($result);
        } else {
            return urlencode($data);
        }
    }
    
    static function hyphenize ($string) {
        $rules = array('/[^\w\s-]+/'=>'','/\s+/'=>'-', '/-{2,}/'=>'-');
        $string = preg_replace(array_keys($rules), $rules, trim($string));
        return $string = trim(strtolower($string));
    }
 
    static function urlize($url, $truncate = false) {
        if (preg_match('/^(http|https|ftp:\/\/([^\s"\']+))/i', $url, $match))
            $url = "<a href='{$url}'>". ($truncate ? truncate($url,$truncate): $url).'</a>';
        return $url;
    }

    static function set_default($object, $default) {
        return !$object ? $default : $object;
    }
}

class StringFilters extends FilterCollection {

    static function humanize($string) {
        $string = preg_replace('/\s+/', ' ', trim(preg_replace('/[^A-Za-z0-9()!,?$]+/', ' ', $string)));
        return capfirst($string);
    }
    
    static function capitalize($string) {
        return ucwords(strtolower($string)) ;
    }
    
    static function titlize($string) {
        return self::capitalize($string);
    }
    
    static function capfirst($string) {
        $string = strtolower($string);
        return strtoupper($string{0}). substr($string, 1, strlen($string));
    }
    
    static function tighten_space($value) {
        return preg_replace("/\s{2,}/", ' ', $value);
    }
    
    static function escape($value, $attribute = false) {
        return htmlspecialchars($value, $attribute ? ENT_QUOTES : ENT_NOQUOTES);
    }
    
    static function force_escape($value, $attribute = false) {
        return self::escape($value, $attribute);
    }
    
    static function e($value, $attribute = false) {
        return self::escape($value, $attribute);
    }
    
    static function safe($value) {
        return $value;
    }
    
    static function truncate ($string, $max = 50, $ends = '...') {
		return (strlen($string) > $max ? substr($string, 0, $max).$ends : $string);
    }
    
    static function limitwords($text, $limit = 50, $ends = '...') {
        if (strlen($text) > $limit) {
            $words = str_word_count($text, 2);
            $pos = array_keys($words);

            if (isset($pos[$limit])) {
                $text = substr($text, 0, $pos[$limit]) . $ends;
            }
        }
        return $text;
    }
    
    static function trans($arr, $key) {
        $retVal = "";
        
        if (is_object($arr)){
        	if (isset($arr->Translation[$GLOBALS['LANGUAGE_CODE']])){
        		$retVal = $arr->Translation[$GLOBALS['LANGUAGE_CODE']]->$key;
        	}
        }
        
        if (is_array($arr)){        
			foreach ($arr['Translation'] as $value) {
				if ($value['lang'] == $GLOBALS['LANGUAGE_CODE']){
					$retVal = $value[$key];
				}
			}
        }
        
    	return $retVal;
    }    
    
    static function mask_email($string) {
    	$retVal = explode('@', $string);
    	return $retVal[0];
    }    
}

class NumberFilters extends FilterCollection {
    static function filesize ($bytes, $round = 1) {
        if ($bytes === 0)
            return '0 bytes';
        elseif ($bytes === 1)
            return '1 byte';
    
        $units = array(
            'bytes' => pow(2, 0), 'kB' => pow(2, 10),
            'BM' => pow(2, 20), 'GB' => pow(2, 30),
            'TB' => pow(2, 40), 'PB' => pow(2, 50),
            'EB' => pow(2, 60), 'ZB' => pow(2, 70)
        );

        $lastUnit = 'bytes';
        foreach ($units as $unitName => $unitFactor) {
            if ($bytes >= $unitFactor) {
                $lastUnit = $unitName;
            } else {
                $number = round( $bytes / $units[$lastUnit], $round );
                return number_format($number) . ' ' . $lastUnit;
            }
        }
    }

    static function currency($amount, $currency = 'USD', $precision = 2, $negateWithParentheses = false) {
        $definition = array(
            'EUR' => array('�','.',','), 'GBP' => '�', 'JPY' => '�', 
            'USD'=>'$', 'AU' => '$', 'CAN' => '$'
        );
        $negative = false;
        $separator = ',';
        $decimals = '.';
        $currency = strtoupper($currency);
    
        // Is negative
        if (strpos('-', $amount) !== false) {
            $negative = true;
            $amount = str_replace("-","",$amount);
        }
        $amount = (float) $amount;

        if (!$negative) {
            $negative = $amount < 0;
        }
        if ($negateWithParentheses) {
            $amount = abs($amount);
        }

        // Get rid of negative zero
        $zero = round(0, $precision);
        if (round($amount, $precision) === $zero) {
            $amount = $zero;
        }
    
        if (isset($definition[$currency])) {
            $symbol = $definition[$currency];
            if (is_array($symbol))
                @list($symbol, $separator, $decimals) = $symbol;
        } else {
            $symbol = $currency;
        }
        $amount = number_format($amount, $precision, $decimals, $separator);

        return $negateWithParentheses ? "({$symbol}{$amount})" : "{$symbol}{$amount}";
    }
}

class HtmlFilters extends FilterCollection {
    static function base_url($url, $options = array()) {
        return $url;
    }
    
    static function asset_url($url, $options = array()) {
        return self::base_url($url, $options);
    }
    
    static function image_tag($url, $options = array()) {
        $attr = self::htmlAttribute(array('alt','width','height','border'), $options);
        return sprintf('<img src="%s" %s/>', $url, $attr);
    }
    
    static function video_tag($url, $options = array()) {
        $retVal = '';
        $attr = self::htmlAttribute(array('width','height'), $options);
        $match = preg_match('/src=["|\'](.*?)"/', $url, $params);

        if($match){
            $retVal = sprintf('<iframe src="%s?modestbranding=1&controls=0" frameborder="0" allowfullscreen %s></iframe>', $params[1], $attr);    
        }
        return sprintf($retVal);
    }    

    static function css_tag($url, $options = array()) {
        $attr = self::htmlAttribute(array('media'), $options);
        return sprintf('<link rel="stylesheet" href="%s" type="text/css" %s />', $url, $attr);
    }

    static function script_tag($url, $options = array()) {
        return sprintf('<script src="%s" type="text/javascript"></script>', $url);
    }
    
    static function links_to($text, $url, $options = array()) {
        $attrs = self::htmlAttribute(array('ref'), $options);
        $url = self::base_url($url, $options);
        return sprintf('<a href="%s" %s>%s</a>', $url, $attrs, $text);
    }
    
    static function links_with ($url, $text, $options = array()) {
        return self::links_to($text, $url, $options);
    }
    
    static function strip_tags($text) {
        $text = preg_replace(array('/</', '/>/'), array(' <', '> '),$text);
        return strip_tags($text);
    }

    static function linebreaks($value, $format = 'p') {
        if ($format === 'br')
            return HtmlFilters::nl2br($value);
        return HtmlFilters::nl2pbr($value);
    }
    
    static function nl2br($value) {
        return str_replace("\n", "<br />\n", $value);
    }
    
    static function nl2pbr($value) {
        $result = array();
        $parts = preg_split('/(\r?\n){2,}/m', $value);
        foreach ($parts as $part) {
            array_push($result, '<p>' . HtmlFilters::nl2br($part) . '</p>');
        }
        return implode("\n", $result);
    }
    
    static function html_entity_decode($value) {
        $result = html_entity_decode($value);
        return $result;
    }    

    protected static function htmlAttribute($attrs = array(), $data = array()) {
        $attrs = self::extract(array_merge(array('id', 'class', 'title', "style"), $attrs), $data);
        
        $result = array();
        foreach ($attrs as $name => $value) {
            $result[] = "{$name}=\"{$value}\"";
        }
        return join(' ', $result);
    }

    protected static function extract($attrs = array(), $data=array()) {
        $result = array();
        if (empty($data)) return array();
        foreach($data as $k => $e) {
            if (in_array($k, $attrs)) $result[$k] = $e;
        }
        return $result;
    }
}

class DatetimeFilters extends FilterCollection {
    static function date($time, $format = 'jS F Y H:i') {
        if ($time instanceof DateTime) 
            $time  = (int) $time->format('U');
        if (!is_numeric($time)) 
          $time = strtotime($time);
          
        return date($format, $time);
    }
    
    static function localdate($time, $format = 'j F Y l') {
        if ($time instanceof DateTime) 
            $time  = (int) $time->format('U');
        if (!is_numeric($time)) 
          $time = strtotime($time);
          
        $tarihStr = date($format, $time);
        
	    if(strpos($format, "F")!==false){
	        global $aylarIng, $aylar;
	        $tarihStr=str_replace($aylarIng, $aylar, $tarihStr);
	    }
	    if(strpos($format, "l")!==false){
	        global $gunlerIng, $gunler;
	        $tarihStr=str_replace($gunlerIng, $gunler, $tarihStr);
	    }
	    if(strpos($format, "M")!==false){
	        global $aylarKisaIng, $aylarKisa;
	        $tarihStr=str_replace($aylarKisaIng, $aylarKisa, $tarihStr);
	    }
	    if(strpos($format, "D")!==false){
	        global $gunlerKisaIng, $gunlerKisa;
	        $tarihStr=str_replace($gunlerKisaIng, $gunlerKisa, $tarihStr);
	    }
          
        return $tarihStr;
    }    

    static function relative_time($timestamp, $format = 'g:iA') {
        if ($timestamp instanceof DateTime) 
            $timestamp = (int) $timestamp->format('U');

        $timestamp = is_numeric($timestamp) ? $timestamp: strtotime($timestamp);
        
        $time   = mktime(0, 0, 0);
        $delta  = time() - $timestamp;
        $string = '';
        
        if ($timestamp < $time - 86400) {
            return date("F j, Y, g:i a", $timestamp);
        }
        if ($delta > 86400 && $timestamp < $time) {
            return pjango_gettext("Yesterday at ") . date("g:i a", $timestamp);
        }

        if ($delta > 7200)
            $string .= floor($delta / 3600) . pjango_gettext(" hours, ");
        else if ($delta > 3660)
            $string .= pjango_gettext("1 hour, ");
        else if ($delta >= 3600)
            $string .= pjango_gettext("1 hour ");
        $delta  %= 3600;
        
        if ($delta > 60)
            $string .= floor($delta / 60) . pjango_gettext(" minutes ");
        else
            $string .= $delta . pjango_gettext(" seconds ");
        return $string.pjango_gettext(" ago");
    }

    static function relative_date($time) {
        if ($time instanceof DateTime) 
            $time = (int) $time->format('U');

        $time = is_numeric($time) ? $time: strtotime($time);
        $today = strtotime(date('M j, Y'));
        $reldays = ($time - $today)/86400;
        
        if ($reldays >= 0 && $reldays < 1)
            return 'today';
        else if ($reldays >= 1 && $reldays < 2)
            return 'tomorrow';
        else if ($reldays >= -1 && $reldays < 0)
            return 'yesterday';

        if (abs($reldays) < 7) {
            if ($reldays > 0) {
                $reldays = floor($reldays);
                return 'in ' . $reldays . ' day' . ($reldays != 1 ? 's' : '');
            } else {
                $reldays = abs(floor($reldays));
                return $reldays . ' day'  . ($reldays != 1 ? 's' : '') . ' ago';
            }
        }
        if (abs($reldays) < 182)
            return date('l, F j',$time ? $time : time());
        else
            return date('l, F j, Y',$time ? $time : time());
    }
    
    static function relative_datetime($time) {
        $date = self::relative_date($time);
        
        if ($date === 'today')
            return self::relative_time($time);
        
        return $date;
    }
}

/*  Ultizie php funciton as Filters */
h2o::addFilter(array('md5', 'sha1', 'numberformat'=>'number_format', 'wordwrap', 'trim', 'upper' => 'strtoupper', 'lower' => 'strtolower', 'count'));

/* Add filter collections */
h2o::addFilter(array('CoreFilters', 'StringFilters', 'NumberFilters', 'DatetimeFilters', 'HtmlFilters'));

/* Alias default to set_default */
h2o::addFilter('default', array('CoreFilters', 'set_default'));

?>