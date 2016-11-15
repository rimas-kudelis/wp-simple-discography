<?php

/**
 * Helper functions for the Simple Discography plugin.
 */
Class SiDi_Helper {
    /**
     * Formats date passed as a string according to one of the supplied formats.
     */
    public static function format_release_date($dateString, $format, $format_ym, $format_y) {
        switch(strlen($dateString)) {
            case 4:
                $useFormat = $format_y;
                $dateString = $dateString . '-01-01'; // avoid treating the year as HHMM
                break;
            case 7:
                $useFormat = $format_ym;
                break;
            default:
                $useFormat = $format;
                break;
        }

        $date = new DateTime($dateString);

        return date_i18n($useFormat, $date->getTimestamp());
    }

    /**
     * Validates a release date in 'YYYY[-mm[-dd]]' format (year with optional month and day).
     */
    function validate_release_date($dateString) {
        $year = $month = $day = 1;
        $dateParts = explode('-', $dateString);

        switch(count($dateParts)) {
            case 3:
                $day = $dateParts[2];
            case 2:
                $month = $dateParts[1];
            case 1:
                $year = $dateParts[0];
                break;
            default:
                return false;
        }

        return checkdate($month, $day, $year);
    }

    /**
     * Returns Album cover HTML code
     */
    function get_cover_image($attachment_id, $size=array(150,150), $attr=array()){

        $cover = $attachment_id != 0 ? wp_get_attachment_image_src($attachment_id, $size) : false;
        if($cover === false){
            $cover = array(
                plugins_url( 'includes/images/no-cover.png' , __FILE__ ),
                $size[0],
                $size[1]
            );
        }

        foreach($attr as $key => &$val) {
            $val = $key . '="' . esc_attr($val) . '"';
        }

        $attr =  ' ' . implode(' ', $attr);
        return '<img width="' . $cover[1] . '" height="' . $cover[2] . '" src="' . $cover[0] . '" class="attachment-' . $cover[1] . 'x' . $cover[2] . '"' . $attr . '/>';
    }
}