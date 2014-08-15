<?php
/**
 * Created by PhpStorm.
 * User: sebastien
 * Date: 08/08/2014
 * Time: 09:56
 */

class SiDi_I18N_DateTime {
    public static function createFromFormat ($format, $time, $timezone=null){
        $time=SiDi_I18N_DateTime::translate_to_english($time);
        if(empty($timezone))
            return DateTime::createFromFormat($format, $time);
        else
            return DateTime::createFromFormat($format, $time, $timezone);
    }
    public static function translate_to_english($datetime){
        global $wp_locale;
        $trans='';
        $no_accent_date = htmlentities($datetime, ENT_NOQUOTES, 'utf-8');
        $no_accent_date = preg_replace('#&([A-za-z])(?:uml|circ|tilde|acute|grave|cedil|ring);#', '\1', $no_accent_date);
        $no_accent_date = preg_replace('#&([A-za-z]{2})(?:lig);#', '\1', $no_accent_date);
        $no_accent_date = str_split(preg_replace('#&[^;]+;#', '', $no_accent_date));
        $datetime=SiDi_I18N_DateTime::mbStringToArray($datetime);
        $len_date=count($datetime);
        $month=array(1=>"January",2=>"February",3=>"March",4=>"April",5=>"May",6=>"June",7=>"July",8=>"August",9=>"September",10=>"October",11=>"November",12=>"December");
        $weekday=array("Sunday","Monday","Tuesday","Wednesday","Thursday","Friday","Saturday");

//        var_dump($no_accent_date, $datetime);
        for($i = 0; $i < $len_date; $i++)
        {
            $char = $datetime[$i];
            if(ctype_alpha ($no_accent_date[$i] )){
                $word=$char;
                for($j=$i+1; $j < $len_date; $j++){
                    $char = $datetime[$j];
                    if(ctype_alpha ($no_accent_date[$j] )){
                        $word.= $char;
                    }else{
                        $i=$j-1;
                        break;
                    }
                }
                $key = array_search($word, $wp_locale->month);
                if(empty($key)){
                    $key = array_search($word, $wp_locale->month_abbrev);
                    if(empty($key)){
                        $key = array_search($word, $wp_locale->weekday);
                        if(empty($key)){
                            $key = array_search($word, $wp_locale->weekday_initial);
                            if(empty($key)){
                                $key = array_search($word, $wp_locale->weekday_abbrev);
                                if(empty($key)){
                                    $trans.= $word;
                                }else{
                                    $trans.= $weekday[array_search($key, $wp_locale['weekday'])];
                                }
                            }else{
                                $trans.= $weekday[array_search($key, $wp_locale['weekday'])];
                            }
                        }else{
                            $trans.= $weekday[$key];
                        }
                    }else{
                        $trans.= $month[intval(array_search($key, $wp_locale['month']),10)];
                    }
                }else{
                    $trans.= $month[intval($key,10)];
                }
            }else{
                $trans.=$char;
            }
        }
        return $trans;
    }

    public static function mbStringToArray ($string) {
        $strlen = mb_strlen($string);
        while ($strlen) {
            $array[] = mb_substr($string,0,1,"UTF-8");
            $string = mb_substr($string,1,$strlen,"UTF-8");
            $strlen = mb_strlen($string);
        }
        return $array;
    }

    /*
     * Matches each symbol of PHP date format standard
     * with jQuery equivalent codeword
     * @author Tristan Jahier
     */
    public static function dateformat_PHP_to_jQueryUI($php_format, $is_time=false)
    {
        $DATE_SYMBOLS_MATCHING = array(
            // Day
            'd' => 'dd',
            'D' => 'D',
            'j' => 'd',
            'l' => 'DD',
            'N' => '',
            'S' => '',
            'w' => '',
            'z' => 'o',
            // Week
            'W' => '',
            // Month
            'F' => 'MM',
            'm' => 'mm',
            'M' => 'M',
            'n' => 'm',
            't' => '',
            // Year
            'L' => '',
            'o' => '',
            'Y' => 'yy',
            'y' => 'y'
        );
        $TIME_SYMBOLS_MATCHING = array(
            // Time
            'a' => '',
            'A' => '',
            'B' => '',
            'g' => '',
            'G' => '',
            'h' => '',
            'H' => '',
            'i' => '',
            's' => '',
            'u' => ''
        );
        $jqueryui_format = "";
        $escaping = false;
        for($i = 0; $i < strlen($php_format); $i++)
        {
            $char = $php_format[$i];
            if($char === '\\') // PHP date format escaping character
            {
                $i++;
                if($escaping) $jqueryui_format .= $php_format[$i];
                else $jqueryui_format .= '\'' . $php_format[$i];
                $escaping = true;
            }
            else
            {
                if($escaping) { $jqueryui_format .= "'"; $escaping = false; }
                if(isset($DATE_SYMBOLS_MATCHING[$char]))
                    $jqueryui_format .= $DATE_SYMBOLS_MATCHING[$char];
                elseif(isset($TIME_SYMBOLS_MATCHING[$char])){
                    if($is_time)
                        $jqueryui_format .=$char;
                    else
                        $jqueryui_format .= $TIME_SYMBOLS_MATCHING[$char];
                }else{
                    $jqueryui_format .= $char;
                }
            }
        }
        return $jqueryui_format;
    }
    public static function date($format, $timestamp=-1){

//        var_dump($format, $timestamp, '</ br>');
        global $wp_locale;
        if($timestamp==-1)
            $timestamp=time();
        $SYMBOLS_MATCHING = array(
            // Day
            'D' => 'weekday_abbrev',
            'l' => 'weekday',
            'S' => '',
            // Month
            'F' => 'month',
            'M' => 'month_abbrev',
            // Time
            'a' => 'meridiem',
            'A' => 'meridiem',
            //date et heure
            'r' => ''
        );
        $S = array(
            'st'=>__('st','sidi'),
            'nd'=>__('nd','sidi'),
            'rd'=>__('rd','sidi'),
            'th'=>__('th','sidi')
        );

        $date = "";
        $post_format="";
        $len_format=strlen($format);
        for($i = 0; $i < $len_format; $i++)
        {
            $char = $format[$i];
            if($char === '\\') // PHP date format escaping character
            {
                $post_format .= $format[$i];
                $i++;
                $post_format .= $format[$i];
            }
            else
            {
                if(isset($SYMBOLS_MATCHING[$char])){
                    if(!empty($post_format)){
                        $date .= date($post_format,$timestamp);
                        $post_format="";
                    }
                    if ($char=='r'){
                        if(empty($SYMBOLS_MATCHING['r']))
                            $SYMBOLS_MATCHING['r']=ucwords(SiDi_I18N_DateTime::date('D, d M Y H:i:s O',$timestamp));
                        $date .=$SYMBOLS_MATCHING['r'];
                    }elseif($char=='S'){
                        if(empty($SYMBOLS_MATCHING['S']))
                            $SYMBOLS_MATCHING['S']=$S[date('S',$timestamp)];
                        $date .=$SYMBOLS_MATCHING['S'];
                    }elseif(empty($wp_locale->{$SYMBOLS_MATCHING[$char]})){
                        $date .=date($char,$timestamp);
                    }else{
                        if($SYMBOLS_MATCHING[$char]=='weekday'){
                            $date .=$wp_locale->weekday[date('w',$timestamp)];
                        }elseif($SYMBOLS_MATCHING[$char]=='weekday_abbrev'){
                            $date .=$wp_locale->weekday_abbrev[$wp_locale->weekday[date('w',$timestamp)]];
                        }elseif($SYMBOLS_MATCHING[$char]=='month'){
                            $date .=$wp_locale->month[date('m',$timestamp)];
                        }elseif($SYMBOLS_MATCHING[$char]=='month_abbrev'){
                            $date .=$wp_locale->month_abbrev[$wp_locale->month[date('m',$timestamp)]];
                        }elseif($SYMBOLS_MATCHING[$char]=='meridiem'){
                            $meridiem=$wp_locale->meridiem[date($char,$timestamp)];
                            $date .=empty($meridiem)?date($char,$timestamp):$meridiem;
                        }
                    }
                }else{
                    $post_format .= $format[$i];
                }
            }
        }
        if(isset($post_format))
            $date .= date($post_format,$timestamp);
        return $date;
    }


//    function mt_date($format, $timestamp=''){
//        global $wp_locale;
//        if(empty($timestamp))
//            $timestamp=time();
//        $SYMBOLS_MATCHING = array(
//            // Day
//            'r' => '',
//            'd' => '',
//            'D' => 'weekday_abbrev',
//            'j' => '',
//            'l' => 'weekday',
//            'N' => '',
//            'S' => '',
//            'w' => '',
//            'z' => '',
//            // Week
//            'W' => '',
//            // Month
//            'F' => 'month',
//            'm' => '',
//            'M' => 'month_abbrev',
//            'n' => '',
//            't' => '',
//            // Year
//            'L' => '',
//            'o' => '',
//            'Y' => '',
//            'y' => '',
//            // Time
//            'a' => 'meridiem',
//            'A' => 'meridiem',
//            'B' => '',
//            'g' => '',
//            'G' => '',
//            'h' => '',
//            'H' => '',
//            'i' => '',
//            's' => '',
//            'u' => '',
//            //Fuseau
//            'e' => '',
//            'I' => '',
//            'O' => '',
//            'P' => '',
//            'T' => '',
//            'Z' => '',
//            //date et heure
//            'c' => '',
//            'U' => ''
//
//        );
//        $date = "";
//        $escaping = false;
//        $len_format=strlen($format);
//        for($i = 0; $i < $len_format; $i++)
//        {
//            $char = $format[$i];
//            if($char === '\\') // PHP date format escaping character
//            {
//                $i++;
//                $date .= $format[$i];
//            }
//            else
//            {
//                if(isset($SYMBOLS_MATCHING[$char])){
//                    if($char=='r'){
//                        if(empty($SYMBOLS_MATCHING['r']))
//                            $SYMBOLS_MATCHING['r']=ucwords(mt_date('D, d M Y H:i:s O',$timestamp));
//                        $date .=$SYMBOLS_MATCHING['r'];
//                    }else{
//                        if(empty($SYMBOLS_MATCHING[$char])){
//                            $date .=date($char,$timestamp);
//                        }else{
//                            if(empty($wp_locale->{$SYMBOLS_MATCHING[$char]})){
//                                $date .=date($char,$timestamp);
//                            }else{
//                                if($SYMBOLS_MATCHING[$char]=='weekday'){
//                                    $date .=$wp_locale->weekday[date('w',$timestamp)];
//                                }elseif($SYMBOLS_MATCHING[$char]=='weekday_abbrev'){
//                                    $date .=$wp_locale->weekday_abbrev[$wp_locale->weekday[date('w',$timestamp)]];
//                                }elseif($SYMBOLS_MATCHING[$char]=='month'){
//                                    $date .=$wp_locale->month[date('m',$timestamp)];
//                                }elseif($SYMBOLS_MATCHING[$char]=='month_abbrev'){
//                                    $date .=$wp_locale->month_abbrev[$wp_locale->month[date('m',$timestamp)]];
//                                }elseif($SYMBOLS_MATCHING[$char]=='meridiem'){
//                                    $meridiem=$wp_locale->meridiem[date($char,$timestamp)];
//                                    $date .=empty($meridiem)?date($char,$timestamp):$meridiem;
//                                }
//                            }
//                        }
//                    }
//                }else{
//                    $date .= $char;
//                }
//            }
//        }
//        return $date;
//    }


//    function date_format_php_to_js( $sFormat ) {
//        switch( $sFormat ) {
//            //Predefined WP date formats
//            case 'F j, Y':
//                return( 'MM d, yy' );
//                break;
//            case 'Y/m/d':
//                return( 'yy/mm/dd' );
//                break;
//            case 'm/d/Y':
//                return( 'mm/dd/yy' );
//                break;
//            case 'd/m/Y':
//                return( 'dd/mm/yy' );
//                break;
//        }
//    }

} 