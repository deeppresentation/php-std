<?php namespace GreenG\Std\Components;

class PopperTooltip {


    private $config = [];
    public function __construct(array $args = [])
    {
        $this->config = $config;
        /*[
            'ref_containers' => '.container-x .container-y #container-z',
            'ref_objects' => '.ref-x .ref-y #ref-z',  
            ['cancelers' =>
                [
                    'val' => '.canceler-x .canceler-y #canceler-z',  
                    'on' => 'scroll'  
                ]            
            ]
        ]
        */
    }

    private static function get_identifiers_html(string $identifiersStr)
    {
        $classes = [];
        $ids = [];
        $identifiers = explode(' ', $identifiersStr);
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

    public function get_tooltip_html()
    {
        

        ?>
        <div class="popper th-tooltip" id="js--country-tooltip">
            <div class="th-tooltip__arrow popper__arrow x-arrow" id="js--country-tooltip-arrow" x-arrow=""></div>
            <div class="th-tooltip__body g-clearfix" id="js--country-tooltip-body"> 
                <div class="th-tooltip__body__img-wrap">
                    <img class="th-tooltip__body__img-wrap__img" src="" alt="" id="js--country-tooltip-img">
                </div>
                <div class="th-tooltip__body__text-wrap">
                    <div class="th-tooltip__body__text-wrap__text">
                        <h5 class="th-tooltip__body__text-wrap__text__title" id="js--country-tooltip-h">Fiesta Nacional del Lúpulo</h5>
                        <p class="th-tooltip__body__text-wrap__text__content" id="js--country-tooltip-p">it's harvest time and celebration.</p>
                    </div>
                </div>
            </div>
        </div>
        <?php    
    }

}