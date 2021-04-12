<?php

//Antonio

class TSmallCard {

    private $main = null;
    const CardAcua = "aqua";
    const CardRed = "red";
    const CardGreen = "green";
    const CardYellow= "yellow";

    public function __construct($title,$value,$iconCLass,$color,$linkHref=null) {
              
        $divParent = new TElement("div");
        $divParent->class = "col-md-3 col-sm-6 col-xs-12";
        
        $div = new TElement("div");
        $div->class = "info-box";
		
		$span = new TElement("span");
        $span->class = "info-box-icon";
		$span->style = "background-color: $color";

		$icon = new TElement("i");
		$icon->class = $iconCLass;
        $icon->style = "color: white";
		$span->add($icon);
		
		$divInfoBox = new TElement("div");
		$divInfoBox->class  = "info-box-content";
        $divInfoBox->style = 'text-align: center';
		

		$spanTit = new TElement("span");
        $spanTit->class = "info-box-text";
        $spanTit->add($title);

		$spanNum = new TElement("span");
		$spanNum->class = "info-box-number";
        $spanNum->style = 'padding-top:20px;font-size:70px';
        $spanNum->add($value);

        $divInfo = new TElement("div");
        $divInfo->style = "margin-left:20px !important";
        
        $divInfo->add($spanTit);
		$divInfo->add($spanNum);

        $divInfoBox->add($divInfo);
		
        $div->add($span);
        $div->add($divInfoBox);
		
        

        $divlink = new TElement("a");
        $divlink->href = $linkHref;
        $divlink->add($div);

        $divParent->add($divlink);
        
        $this->main = $divParent;
        
    }
    
    public function show(){
        echo $this->main;
    }
}
?>