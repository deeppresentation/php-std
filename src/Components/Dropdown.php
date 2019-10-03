<?php namespace GreenG\Std\Components;

use GreenG\Std\Html\Element;
use GreenG\Std\Html\Attr;

// test git automation
class DropDownItemCfg extends Attr
{
    public $text = '';
    public function __construct(
        string $text,
        ?array $attributes = null
    )
    {
        $this->text = $text;
        parent::__construct($attributes);
    }
}

class Dropdown extends Element{

    public $element = null;      
    /**
     * @method __construct
     */
    public function __construct(
        array $items, 
        string $BEMBase = 'g-dropdown', 
        int $activeItemIdx = 0,
        string $id = null, 
        string $mainAddClasses = null
    )
    {
        $this->breakToNewBEMModule = true;
        $buttons = new Element('div', 'btns'/*, new Attr(['class'=> 'js--g-dropdown-btns'])*/);
        $idx = 0;
        $defaultText = '';
        foreach ($items as $itemElement)
        {
            
            $btn = null;
            $text = '';
            $attr = null;
            if (is_a($itemElement, 'GreenG\Std\Components\DropDownItemCfg'))
            {
                $text = $itemElement->text; 
                $attr = $itemElement; 
                $btn = new Element('div', 'btn', $itemElement, $itemElement->text);
            }
            elseif (is_string($itemElement)){
                $text = $itemElement; 
                $btn = new Element('div', 'btn', null, $itemElement);  
            }
            if ($text)
            {
                $btn = new Element('div', 'btn', $attr, $text);
                $btn->attributes->append_class('js--g-dropdown-btn');
                //$btn->attributes->apend_attr('val', 'test');
                if ($idx == $activeItemIdx)
                {
                    $defaultText = $text;  
                    $btn->attributes->append_class('selected');
                }
                $buttons->add_content($btn);
            }
            $idx++;
        }
        $mainAddClasses = $mainAddClasses ?? '';
        parent::__construct(
            'div', 
            $BEMBase, [
                'class' => $mainAddClasses . ' js--g-dropdown', 
                'id' => $id, 
                
                'data-confirmed-value' => $defaultText
            ], [
                //new Element('input', 'input', null, null, ),
                new Element('input', 'input', new Attr(['class' => 'js--g-dropdown-input', 'value' => $defaultText/*, 'tabindex' => 0*/])),
                new Element('div', 'btns-wrap', new Attr(['class' => 'js--g-dropdown-btns-wrap']), $buttons)
            ]
        );
        
    }
}