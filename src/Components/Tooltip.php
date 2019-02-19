<?php namespace GreenG\Std\Components;

use GreenG\Std\Html\Element;
use GreenG\Std\Html\Attr;
use GreenG\Std\Core\Special;


class Tooltip {


    private $config = [];
    public $tooltip = null;
    public $scriptConfig = [];         
    /**
     * @method __construct
     * @param array $cancelers Objects and their events, that hide tooltip 
     * @example $cancelers' =>[
     *   'val' => '.canceler-x .canceler-y #canceler-z',  
     *   'on' => 'scroll'  
     * ]
     */
    public function __construct(string $id, $refObjects, string $boundariesElementId,  array $cancelers = [])
    {
        $this->config = [
            'id' => $id,
            'refObjects' => (is_array($refObjects)) ? implode(',', $refObjects) : $refObjects,
            'boundariesElementId' => $boundariesElementId,
            'cancelers' => $cancelers 
        ];
        $this->init_tooltip_html();
    }

    public static function insert_data_attr_to_ref(array &$attributes, string $title, string $content = '', string $imgUrl = '')
    {  
        $attributes['data-tooltip-title'] = $title; 
        $attributes['data-tooltip-content'] = $content; 
        $attributes['data-tooltip-img-url'] = $imgUrl;
    }

    public function get_script_config()
    {
        return $this->config;
    }
   
    private static function get_identifiers_html(string $identifiersStr)
    {
        $classes = [];
        $ids = [];

        $identifiers = explode(',', str_replace(' ', '', $identifiersStr));
        foreach  ($identifiers as $i)
        {
            if (strlen($i > 0))
            {
                if ($i[0] == '.')
                {
                    $classes[] = $i = ltrim($str, '.');
                }
                else if ($i[0] == '#')
                {
                    $ids[] = $i = ltrim($str, '#');   
                }
            }
        }
        return (object) [
            'classes' => $classes,
            'ids' => $ids
        ];    
    }

    public function render()
    {
        if ($this->tooltipWrapper)
        {
            $this->tooltipWrapper->render(); 
        }    
    }

    private function init_tooltip_html()
    {
        $tooltipId = $this->config['id'];

        $this->tooltipWrapper = new Element('div', 'g-tooltip', new Attr(['class' => 'popper', 'id' => $tooltipId]), [
            new Element('div', 'arrow', new Attr(['class' => ['popper__arrow', 'x-arrow'], 'x-arrow' => "", 'id' => $tooltipId . '__arrow'])),
            new Element('div', 'body', new Attr(['id' => $tooltipId . '__body']), [
                new Element('div', 'img-wrap', null, 
                   new Element('img', 'img', new Attr(['id' => $tooltipId . '__img']), null, [], false) 
                ),
                new Element('div', 'text-wrap', null, [
                    new Element('div', 'text', null, [
                        new Element('h5', 'title', new Attr(['id' => $tooltipId . '__title']), "G-PopperTooltip title"),
                        new Element('p', 'content', new Attr(['id' => $tooltipId . '__content']), "G-PopperTooltip content"),
                    ])
                ])
            ])
        ]); 
    }


}