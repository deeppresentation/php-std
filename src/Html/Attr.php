<?php namespace GreenG\Std\Html;


class Attr
{
    private $data = [];
    public function __construct(array $attributes = [])
    {
        $this->data = $attributes;  
    }
    public function add_attr(string $key, string $val)
    {
        $this->data[$key] = $val;    
    }

    //@param string|array $attrVal
    public function apend_attr(array $attributes)
    {
        foreach ($attributes as $key => $val)
        {
            $this->data = array_merge_recursive($this->data, $attributes);
        }
    }

    public function to_str()
    {
        return Html::get_attr_str($this->data);
    }
}
