<?php namespace GreenG\Std\Core;

class Str
{
    // SECTION Public

    public static function separed_transform_ucfirst(string $str, string $srcSeparator = '-', string $dstSeparator = '')
    {
        return implode($dstSeparator, array_map('ucfirst', explode($srcSeparator, $str))); 
    }
    
    public static function separed_transform_tolower(string $str, string $srcSeparator = '', string $dstSeparator = '')
    {
        return implode($dstSeparator, array_map('strtolower', explode($srcSeparator, $str))); 
    }

    public static function separed_last_part(string $data, $delimiter = '.', $def = '')
    {
        $dataParts = explode($delimiter, $data);
        $count = count($dataParts);
        if ($count > 0 )
        {
            return $dataParts[$count - 1];
        }     
        return $def;
    }

    public static function separed_first_part(string $data, $delimiter = '.', $def = '')
    {
        $dataParts = explode($delimiter, $data);
        $count = count($dataParts);
        if ($count > 0 )
        {
            return $dataParts[0];
        }     
        return $def;
    }

    public static function starts_with($haystack, $needle)
    {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    public static function ends_with($haystack, $needle)
    {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        return (substr($haystack, -$length) === $needle);
    }

    public static function char_replace_neighbor_check(string $search, string $replace, string $subject)
    {
        $result = '';
        for ($i = 0; $i < strlen($subject); $i++){
            if ($subject[$i] === $search)
            {
                if ($i > 0 && $subject[$i-1] == $replace) continue;
                if ($i + 1 < strlen($subject) && $subject[$i+1] == $replace) continue;
                $result .= $replace;
            }
            else {
                $result .= $subject[$i];
            }

        }
        return $result;
    }
        
    // !SECTION End - Public


    // SECTION Private

    // !SECTION End - Private
}