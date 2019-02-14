<?php namespace GreenG\Std\DataFlow;

use GreenG\Std\Core\Arr;
use GreenG\Std\Core\Special;
use GreenG\Std\Html\Html;
use GreenG\Std\Core\Color;

class Renderer
{
    //[0] action:  hover content convert_[convertor-name]_style_[style-property]
    //[1] content: img h2 p
    //[2] link: file-name url
    public static function get_html_country_table_cell_container($rowName, $cellData, $convertArgs, $pseudoCodeDelimiter = '_', $pseudoCodeArgsDelimiter = '|', $pseudoCodeArgsKeyValSeparator = ':')
    {
        if (is_array($cellData))
        {          
            if (!isset($seasonColors))
            {
                $seasonColors = self::default_color_options();     
            }
            $htmlContent = '';
            foreach ($cellData as $cellItem)
            {
                $itemPseudoCommanders = array();
                foreach ($cellItem as $pseudoCodeKey => $value)
                {
                    $action = PseudoCommand::get_pseudo_action($pseudoCodeKey);
                    if (isset($action))
                    {
                        $itemPseudoCommanders[$action][] = new PseudoCommand(
                            $pseudoCodeKey, 
                            $value, 
                            $pseudoCodeDelimiter, 
                            $pseudoCodeArgsDelimiter, 
                            $pseudoCodeArgsKeyValSeparator
                        );
                    }
                }
                $htmlContent .= self::get_html_country_table_cell_container_sub($rowName, $itemPseudoCommanders, $convertArgs);     
            }
        }
        if (!empty($htmlContent))
        {
            return $htmlContent;
        }
        else
        {
            return '';
        }
    }

    private static function default_color_options()
    {
        $defaults = array(
            'High' => '#26c485',
            'Shoulder' => '#e9b76a',
            'Low' => '#c7efbf',
            'Unlisted' => '#cccccc',
            'Selected' => '#965495'
        );
        return $defaults;
    }

    private static function get_html_country_table_cell_container_sub_tooltip($hoverCommanders, &$addAttrsAsoc)
    { 
        $imgComanders = array_filter($hoverCommanders, function($commander){ return $commander->get_content() == 'img'; } );
        $hComanders = array_filter($hoverCommanders, function($commander){ return substr($commander->get_content(), 0, 1) == 'h'; } );
        $pComanders = array_filter($hoverCommanders, function($commander){ return $commander->get_content() == 'p'; } );
        
        $imgComander = reset($imgComanders); //TODO multi content
        $hComander = reset($hComanders); 
        $pComander = reset($pComanders); 

        $tooltipData = array();
        $tooltipData['h'] = $hComander ? $hComander->get_data() : ''; 
        $tooltipData['p'] = $pComander ? $pComander->get_data() : ''; 
        $tooltipData['imgUrl'] = $imgComander ? $imgComander->get_data() : '';
        $addAttrsAsoc['data-tooltip'] = Special::pseudo_json_encode($tooltipData);
    }

    private static function convertAsRangeToColor($valToConvert, string $colorConfigStr, $def)
    {
        //$colorConfigStr example: -40=#2BC6E9|-20=#4A2BE9|0=#419ADD|20=#EFBF3A|40=#EF913A
        $colorConfig = explode('|', $colorConfigStr); 
        $res = $def;      
        $threasholdsIndexed = [];
        foreach ($colorConfig as $treasholdCfgStr)
        {
            $treasholdCfg = explode('=', $treasholdCfgStr); 
            if (count($treasholdCfg) >= 2)
            {
                $threasholdsIndexed[] = [$treasholdCfg[0], new Color($treasholdCfg[1], 'rgb')];      
            }
        }
        $count = count($threasholdsIndexed);
        $ranges = [];
        for ($i=0; $i < $count; $i++) { 
            $inputRange = ($i==0) ? [PHP_INT_MIN , $threasholdsIndexed[$i][0]] : [$threasholdsIndexed[$i - 1][0], $threasholdsIndexed[$i][0]];  
            $outputRange = ($i==0) ? [$threasholdsIndexed[$i][1]] : [$threasholdsIndexed[$i - 1][1], $threasholdsIndexed[$i][1]];   
            $ranges[] = ['input' => $inputRange, 'output' => $outputRange]; 
            if ($i == $count - 1)
            {
                $inputRange = [$threasholdsIndexed[$i][0], PHP_INT_MAX];
                $outputRange = [$threasholdsIndexed[$i][1]];
                $ranges[] = ['input' => $inputRange, 'output' => $outputRange];      
            }
        }
        foreach ($ranges as $range)
        {
            if ($valToConvert >= $range['input'][0] && $valToConvert < $range['input'][1])
            {
                if (count($range['output']) == 1)
                {
                    $res = $range['output'][0];
                }
                else if (count($range['output']) == 2)
                {
                    $leftColor = $range['output'][0]->toArray();
                    $rightColor = $range['output'][1]->toArray();
                    $rangeSize = $range['input'][1] - $range['input'][0];
                    $ratio = ($valToConvert - $range['input'][0]) / $rangeSize;
                    $resColor = [];
                    $colorChannelsCnt = count($leftColor);
                    for ($i=0; $i < $colorChannelsCnt; $i++) { 
                        $resColor[$i] = (int)((1.0 - $ratio) * $leftColor[$i] + $ratio * $rightColor[$i]);
                    }
                    $res = new Color($resColor, 'rgb');
                }
                break;
            }      
        }
        return $res;
    }

    private static function get_html_country_table_cell_container_sub($rowName, $itemPseudoCommanders, $convertArgs)
    {
        $renderEn = false;
        // TODO multi content
        $contentCommander = Arr::get($itemPseudoCommanders, 'content.0'); 
        $convertCommander = Arr::get($itemPseudoCommanders,'convert.0');
        $hoverCommanders = Arr::as_array(Arr::get($itemPseudoCommanders,'hover'));
   
        $content = array();
        $htmlClasses = array('div-table-cell-sub', 'js--tooltip-ref');
        $htmlStyle = array();
        $addAttrsAsoc = array();
        // hover
        if (count($hoverCommanders) > 0)
        {
            self::get_html_country_table_cell_container_sub_tooltip($hoverCommanders, $addAttrsAsoc);  
        }
        // season to color
        if (isset($convertCommander)) // join color strip to each cell top
        { 
            $convertor = $convertCommander->get_content();
            $data = $convertCommander->get_data();
            switch ($convertor)
            {
                case 'season':
                {
                    $colorOptions = $convertArgs;
                    //$htmlClasses[] = "country-table-cell-sub-color-strip";
                    if (array_key_exists($data, $colorOptions))
                    {
                        $htmlStyle['background-color'] = $colorOptions[$data];
                    }
                    else
                    {
                        $htmlStyle['background-color'] = $colorOptions['Unlisted'];   
                    }
                } break;
                case 'Day temperature':
                case 'Night temperature':
                case 'Days of rain':
                case 'Hours sun':
                case 'Water temperature':
                {
                    $resColor = self::convertAsRangeToColor($data, $convertArgs, new Color([128, 128, 128], 'rgb'));
                    $htmlStyle['background-color'] = $resColor->toTextHEX();
                }break;
            }
            
            $renderEn = true;
        }
        // content
        if (isset($contentCommander))
        {
            $data = $contentCommander->get_data();
            $subContentType = $contentCommander->get_content();
            $subContentClasses = array('div-table-cell-sub-content');
            $elementName = '';
            $attributes = [];
            $subContent = null;
            $closingEl = true;
            switch ($subContentType)
            {
                case 'img':
                    $closingEl = false;
                    $elementName = 'img';
                    $contentLinkType = $contentCommander->get_link();   
                    $imgSrc = null;
                    switch ($contentLinkType)
                    {
                        case 'file-name':  
                            if (!empty($data) ) 
                            {
                                $attributes['src'] = get_site_url(null, 'wp-content/uploads/' . $data);   
                                if (!file_exists($attributes['src']))
                                {
                                   // $imgSrc = get_site_url(null, 'wp-content/uploads/' . $rowName. '_Fallback.png');    
                                }      
                            }
                            else
                            {
                                $attributes['src'] = get_site_url(null, 'wp-content/uploads/' . $rowName. '_Fallback.png');
                            }
                        break; // TODO address by param - no wp dependency
                        case 'url': $attributes['src'] = $data ; break;
                        default: $elementName = ''; break;
                    }
                    if (empty($attributes['src'])) 
                    {
                        $elementName = '';  
                    }
                break;
                case 'text':
                    $elementName = 'p';
                    $subContent = $data;
                break;
            }
            if (!empty($elementName))
            {
                $content['content'] = Html::get_str($elementName, $subContentClasses, null, $subContent, $attributes, $closingEl);
                $renderEn = true;
            }
        
        }
        if ($renderEn)
        {
            return  Html::get_str('div', $htmlClasses, $htmlStyle, $content, $addAttrsAsoc);
        }
        else
        {
            return '';
        }
    }



    private static function echo_input($key, $value)
    {   
        echo $key . ':' . json_encode($value); 
    }







      /* public static function process($cellData, $pseudoCodeDelimiter = '_', $pseudoCodeArgsDelimiter = '|', $pseudoCodeArgsKeyValSeparator = ':')
    {
        self::init();
        if (is_array($cellData))
        {          
            foreach ($cellData as $cellItem)
            {
                $itemPseudoCommanders = array();
                foreach ($cellItem as $pseudoCodeKey => $value)
                {
                    $action = PseudoCommand::get_pseudo_action($pseudoCodeKey);
                    if (isset($action))
                    {
                        $itemPseudoCommanders[$action] = new PseudoCommand(
                            $pseudoCodeKey, 
                            $value, 
                            $pseudoCodeDelimiter, 
                            $pseudoCodeArgsDelimiter, 
                            $pseudoCodeArgsKeyValSeparator
                        );
                    }
                }
                self::process_item($itemPseudoCommanders);     
            }
        }
        else
        {
            echo $cellData;
        }
    }

    private static function process_item($itemPseudoCommanders)
    {
        $contentCommander = $itemPseudoCommanders['content'];
        $convertCommander = $itemPseudoCommanders['convert'];
        $hoverCommander = $itemPseudoCommanders['hover'];

        $tooltipContent = null;
        if (isset($hoverCommander))
        {
            //$tooltipContent =   
        }
        if (isset($convertCommander))
        {
            $data = $convertCommander->get_data();
            if (!empty($data))
            {
                echo $data;   
            } 
        }
        // first-step
        if (isset($contentCommander))
        {
            $data = $contentCommander->get_data();
            if (!empty($data))
            {
                $contentType = $contentCommander->get_content();
                $content = null;
                echo '<div class="country-month-data-table-cell">';    
                switch ($contentType)
                {
                    case 'img':
                        $contentLinkType = $contentCommander->get_link();   
                        $imgSrc = null;
                        switch ($contentLinkType)
                        {
                            case 'file-name': $imgSrc = get_site_url(null, 'wp-content/uploads/' . $data); break; // TODO address by param - no wp dependency
                            case 'url': $imgSrc = $data ; break;
                        }
                        if (isset($imgSrc))
                        {
                            echo '<img src="' . $imgSrc . '">'; break;    
                        }
                    break;
                }
                echo '</div>';   
            }
        }
    }
*/
}
