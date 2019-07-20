<?php namespace GreenG\Std\Components;

use GreenG\Std\Html\Element;
use GreenG\Std\Html\Attr;

class DropdownItemsCfg{
    public function __construct(
        array $items, 
        array $itemsAttrs = null)
    {

    }

}

class Dropdown {

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
        $buttons = new Element('div', 'btns', new Attr(['class'=> 'js--g-dropdown-btns']), null);
        $idx = 0;
        foreach ($items as $itemElement)
        {
            $itemElement->attributes->append_class('js--g-dropdown-btns__btn');
            $itemElement->attributes->append_class($BEMBase.'__btns-wrap__btns__btn');
            if ($activeItemIdx == $idx){
                $itemElement->attributes->append_class('active');
            }
            $buttons->add_content($itemElement);
            $idx++;
        }

        $this->element = new Element('div', $BEMBase, new Attr(['class' => $mainAddClasses, 'id' => $id]),
            new Element('div', 'btns-wrap', null, $buttons)
        );  

    }
   
    public function render()
    {
        if ($this->element)
        {
            $this->element->render(); 
        }    
    }
}