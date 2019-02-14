<?php namespace GreenG\Std\Core;

class Special
{
    // SECTION Public
    public static function url_exists($url) {
        if (!$fp = curl_init($url)) return false;
        return true;
    }

    public static function get_months_names_array()
    {
        return array('January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December');
    }

    public static function pseudo_json_encode($dataToEncode, $bitmapMask = 0)
    {
        $result = json_encode($dataToEncode, $bitmapMask);
        $result = '(@#' . str_replace('"', "``", $result) . '#@)';
        return $result;
    }

    public static function pseudo_json_decode($pseudoJsonString, $assoc = true)
    {
        $tmp = str_replace('"(@#', '', $pseudoJsonString);
        $tmp = str_replace('#@)"', '', $tmp);
        $tmp = str_replace("``", '"', $tmp);
        return json_decode($tmp, $assoc); 
    }

    // !SECTION End - Public


    // SECTION Private

    // !SECTION End - Private
}