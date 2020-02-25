<?php namespace DP\Std\Core;

class Csv{
    public static function load(string $file, string $delimiter=',', int $maxLineLength = 10000)
    {
        $row = 0;
        $result = [];
        $keys = [];
        if (($handle = fopen($file, "r")) !== FALSE) {
           
            while (($data = fgetcsv($handle, $maxLineLength, $delimiter)) !== FALSE) {
                $num = count($data);
                
                if ($row == 0)
                {
                    for ($c=0; $c < $num; $c++) 
                    {
                        $keys[$c] = $data[$c];
                        
                    }
                }
                else {
                    $result[$row]=[]; 
                    for ($c=0; $c < $num; $c++) {
                        $result[$row][$keys[$c]]=$data[$c];
                    }
                }
                $row++;
            }
            fclose($handle);
        }
        return $result;
    }
}
?>