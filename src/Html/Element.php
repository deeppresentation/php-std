<?php namespace GreenG\Std\Html;

class Element
{
       private $element = null;
       private $hasClosing = true;
       /** @var Attr */
       private $BEMBase = null;
       private $BEMMod = [];
       private $content = [];
       /** @var Element */
       private $parent = null;
       public $attributes = null;
    
       // SECTION Public 
        //@param string|bool $content
       public function __construct(string $element, string $BEMBase = null, $content = null, Attr $attributes = null, array $BEMMod = [], $hasClosing = true)
       {
              $this->bemClassPart = $bemClassPart;
              $this->BEMBase = $BEMBase;
              $this->BEMMod = $BEMMod;
              $this->element = $element;
              $this->append_content($content);
              $this->attributes = $attributes;
              $this->hasClosing = $hasClosing;
       }
       //@param string|Element $content
       public function add_content($content)
       {
              if ($content && is_string($content) || is_a($content, 'Element'))
              {
                     if (is_a($content, 'Element'))
                     {
                            $content->parent = $this;      
                     }
                     $this->content[] = $content;    
              }   
       }

       public function get_html()
       {
              
       }
   
   
       // !SECTION End - Public
   
}