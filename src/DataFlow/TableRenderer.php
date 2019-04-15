<?php namespace GreenG\Std\DataFlow;

use GreenG\Std\Core\Arr;
use GreenG\Std\Html\Html;
use GreenG\Std\Html\Parse\Helper;
use GreenG\Std\DataFlow\Renderer;


class TableRenderer
{
    private static function get_pre_processed_cell($cellVal, bool $isHeader, array $labelConfig, $rid= -1, $ri = -1, int $ci = -1, $customAttributes = null) 
    {
        if (!$isHeader)
        {
            $rConfigs = Arr::sget($labelConfig, 'body.rows', null);
            $cConfigs = Arr::sget($labelConfig, 'body.columns', null);

            $rConfig = Arr::sget($rConfigs, '*', Arr::sget($rConfigs, $ri, null));
            $cConfig = Arr::sget($cConfigs, '*', Arr::sget($cConfigs, $ci, null));

            $convert = Arr::sget($rConfig, 'convert', null);

            $rBlackList =  Arr::as_array(Arr::sget($rConfig,'blackList', []));
            $cBlackList =  Arr::as_array(Arr::sget($cConfig,'blackList', []));
            $blacklist = array_merge($rBlackList, $cBlackList);
            $config = $cConfig ?? null;
        }
        else
        {
            $config = Arr::sget($labelConfig, 'header', null);   
            $blacklist = Arr::as_array(Arr::sget($config,'blackList', []));
        }
        if ($config && !Arr::sget($config, 'renderAsCell', false))
        {
            if( !in_array($cellVal, $blacklist)) 
            {
                if ($isHeader && Arr::sget($labelConfig, 'isWatherTable', false))
                {
                    $cellVal = substr($cellVal, 0,3);    
                }

                $element = Arr::sget($config, 'labelEl', 'p');
                $class = Arr::sget($config, 'labelClass', '');
                $class = (!empty($class)) ? 'class="' . $class .'"': '';
                return Html::get_str('div', null, null, '<'. $element
                .' ' . $class . '>' . $cellVal . '</' . $element . '>', $customAttributes);
            }
        }    
        else
        {
            if (!is_array($cellVal) && Arr::sget($labelConfig, 'isWatherTable', false))//TODO 
            {
                $cellVal = [['convert_'.$rid => floatval($cellVal),'content_text' => $cellVal]]; 
            }
            return Renderer::get_html_country_table_cell_container($rid , $cellVal, $convert);
        }
        return '';
    }


    private static function get_column_of_row_idx(array $gConfig)
    {
        $cCfg = Arr::sget($gConfig, "body.columns");
        foreach ($cCfg as $idx => $val)
        {
            if (Arr::sget($val,'isRowId', false))
            {
                return $idx;
            }
        }
        return -1;
    }

    private static function get_r_configs_by_row_id(array $config, $rId)
    {
        $allRowsConfig = Arr::sget($config, 'body.rows');
        if (!$allRowsConfig || !is_array($allRowsConfig))
        {
            return null;
        }
        $res = [];
        foreach ($allRowsConfig as $key => $val)
        {
            $ids = Arr::sget($val,'ids', []);
            if ($ids && in_array($rId, $ids))
            {
                $res[$key] = $val;
            }
        }
        return $res;
    }

    public static function render(array $table, array $config = [], bool $hideEmptyRows = true, string $tableClasses = '', string $tableId = '')
    {
        echo self::get_html($table, $config, $hideEmptyRows, $tableClasses, $tableId);
    }

    //row ma prednost
    // TODO use convertor from backend to colors etc
    public static function get_html(array $table, array $config = [], bool $hideEmptyRows = true, string $tableClasses = '', string $tableId = '')
    {
        $res = '';
        $tableAttr = 'class="div-table'. ((!empty($tableClasses)) ? ' '. $tableClasses : '').'"' . ((!empty($tableId))?' id="'.$tableId.'"':'');
        if ( isset($table) ) {
            $res .= '<div '. $tableAttr .'">'; 
                // body process
                $rowIdColumnIdx = self::get_column_of_row_idx($config);
                $cellResults = [];
                foreach ( $table['b'] as $ri => $tr ) { 
                    $rowId = ($rowIdColumnIdx < 0) ? $ri : Arr::as_string(Arr::sget($tr, $rowIdColumnIdx, '')) ?? $ri;
                    $rConfigs = self::get_r_configs_by_row_id($config, $rowId); // Arr::sget($config, 'body.rows.*', Arr::sget($config, 'body.rows.' . $ri));                    
                    if ($rConfigs)
                    {
                        foreach ($rConfigs as $outRi => $rConfig)
                        {
                            $isFirstOutRowRun = false;
                            $alias = Arr::sget($rConfig, 'alias', null);
                            if (!isset($cellResults[$outRi])) 
                            {
                                $isFirstOutRowRun = true;
                                $cellResults[$outRi] = [];
                            }
                            foreach ( $tr as $ci => $td ) 
                            {
                                $data = $td['c'];
                                $disableRendering = false;
                                if ($ci == $rowIdColumnIdx)
                                {
                                    if (!is_null($alias)) $data = $alias;
                                    $disableRendering = !is_null($alias) && !$isFirstOutRowRun;
                                }
                                if (!$disableRendering)
                                {
                                    if (!isset($cellResults[$outRi][$ci])) $cellResults[$outRi][$ci] = [];
                                    $cellResults[$outRi][$ci][$rowId] = self::get_pre_processed_cell($data, false, $config, $rowId, $outRi, $ci);
                                }
                            }
                        }
                    }
                    else
                    {
                        if (!isset($cellResults[$ri])) 
                        {
                            $isFirstOutRowRun = true;
                            $cellResults[$ri] = [];
                        }
                        foreach ( $tr as $ci => $td ) 
                        {
                            $data = $td['c'];
                            if (!isset($cellResults[$ri][$ci])) $cellResults[$ri][$ci] = [];
                            $cellResults[$ri][$ci][$rowId] = self::get_pre_processed_cell($data, false, $config, $rowId, $ri, $ci);
                        }
                    }
                }
                ksort($cellResults);
                
                
                if ($hideEmptyRows)
                {
                    $cellResultsPostproc = [];
                    foreach ($cellResults as $ci_top => $cr_top)
                    {
                        foreach ($cr_top as $ci_middle => $cr_middle)
                        {
                            if (!empty($cr_middle)  && $ci_middle != $rowIdColumnIdx)
                            {
                                foreach ($cr_middle as $cr_low)
                                {
                                    if (!empty($cr_low))
                                    {
                                        $cellResultsPostproc[$ci_top] = $cr_top;
                                        break;
                                    }
                                }
                            }
                        }   
                    }

                   /*$cellResultsPostproc = array_filter($cellResults, function($cellResults){
                        foreach ($cellResults as $ci => $cr)
                        {
                            if (!empty($cr) && $ci != $rowIdColumnIdx)
                            {
                                foreach ($cr as $scr)
                                {
                                    if (!empty($scr))
                                    {
                                        return true;
                                    }
                                }
                            }
                        }   
                        return false;                 
                    });  */ 
                }
                else
                {
                    $cellResultsPostproc = $cellResults;    
                }

                if (!Arr::sget($config, 'isWatherTable', false) && 
                    !Arr::sget($config, 'isGroupTable', false)) // TODO
                {
                    // header
                    if (array_key_exists('header', $config) )
                    {
                        if ( $table['h'] ) {
                            $res .= '<div class="div-table-heading">';
                                $res .= '<div class="div-table-row">';
                                    foreach ( $table['h'] as $idx => $th ) {
                                        if ($idx > 0 || count($cellResultsPostproc) > 1)
                                        {
                                            $res .= '<div class="div-table-head">';
                                                $colorDiv = Helper::str_get_html($cellResultsPostproc[0][$idx]['Season']);
                                                $style = null;
                                                if (count($colorDiv->nodes) > 1)
                                                {
                                                    $style = $colorDiv->nodes[1]->attr['style'];
                                                }
                                                $res .= self::get_pre_processed_cell($th['c'], true, $config, -1, -1, -1, ['style' => $style]);
                                            $res .= '</div>';
                                        }
                                    }
                                $res .= '</div>';
                            $res .= '</div>';
                        }
                    }    
                } 
                else
                {
                    // header
                    if (array_key_exists('header', $config) )
                    {
                        if ( $table['h'] ) {
                            $res .= '<div class="div-table-heading">';
                                $res .= '<div class="div-table-row">';
                                    foreach ( $table['h'] as $th ) {
                                        $res .= '<div class="div-table-head">';
                                            $res .= self::get_pre_processed_cell($th['c'], true, $config);
                                        $res .= '</div>';
                                    }
                                $res .= '</div>';
                            $res .= '</div>';
                        }
                    }
                }


         

                // Body render
                $res .= '<div class="div-table-body">';
                    foreach ($cellResultsPostproc as $ri => $gResults)
                    {
                        $res .= '<div class="div-table-row">'; 
                            foreach ( $gResults as $ci => $columnRes ) {
                                if ($ci == $rowIdColumnIdx) $res .= '<div class="div-table-cell div-table-row-id-column">';  
                                else $res .= '<div class="div-table-cell">';
                                if (!empty($columnRes))
                                {
                                    foreach ( $columnRes as $rowId => $subResults)
                                    {
                                        $res .= $subResults;
                                    }
                                } 
                                $res .= '</div>';
                            }
                        $res .= '</div>';
                    }
                $res .= '</div>';
        $res .= '</div>';
        }
        return $res;
    }
}

?>