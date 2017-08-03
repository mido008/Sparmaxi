<?php
/**
* sublayout products
*
* @package    VirtueMart
* @author Max Milbers
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2014 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL2, see LICENSE.php
* @version $Id: cart.php 7682 2014-02-26 17:07:20Z Milbo $
*/

defined('_JEXEC') or die('Restricted access');

$product = $viewData['product'];
$position = $viewData['position'];
$customTitle = isset($viewData['customTitle'])? $viewData['customTitle']: false;
    
if(isset($viewData['class'])){
    $class = $viewData['class'];
} else {
    $class = 'product-fields';
}?>
<?php
    //var_dump($product);die;
if (!empty($product->customfieldsSorted[$position])) {
    if($customTitle and isset($product->customfieldsSorted[$position][0])){
            $field = $product->customfieldsSorted[$position][0]; ?>
        <div class="<?php echo $class?>">
        <div class="product-fields-title-wrapper"><h3 class="product-fields-title"><span><?php echo vmText::_ ($field->custom_title) ?></span></h3>
        </div> <?php
        }
        $custom_title = null;
    ?>
    <div class="related-view <?php echo $class?>sl">
		<a class="prev control"  href="#"><i class="fa fa-angle-left"></i></a>
        <a class="next control" href="#"><i class="fa fa-angle-right"></i></a>
		<div class="related-vertical">
			<ul class="related-list ">

        <?php

        foreach ($product->customfieldsSorted[$position] as $field) {
            if ( $field->is_hidden ) //OSP http://forum.virtuemart.net/index.php?topic=99320.0
            continue;
            ?><li><div class="product-field product-field-type-<?php echo $field->field_type ?>">
                <?php if (!$customTitle and $field->custom_title != $custom_title and $field->show_title) { ?>
                    <span class="product-fields-title-wrapper"><span class="product-fields-title"><strong><?php echo vmText::_ ($field->custom_title) ?></strong></span>
                        <?php if ($field->custom_tip) {
                            echo JHtml::tooltip ($field->custom_tip, vmText::_ ($field->custom_title), 'tooltip.png');
                        } ?></span>
                <?php }
                if (!empty($field->display)){
                    ?><div class="product-field-display"><?php echo $field->display ?></div><?php
                }
                if (!empty($field->custom_desc)){
                    ?><div class="product-field-desc"><?php echo vmText::_($field->custom_desc) ?></div> <?php
                }
                ?>
            </div></li>
        <?php
            $custom_title = $field->custom_title;
        } ?>
</ul>
</div>

      <div class="clear"></div>
    </div></div>
    <?php
$document = JFactory::getDocument();
$app = JFactory::getApplication();
$templateDir = JURI::base() . 'templates/' . $app->getTemplate();
?>
<script type="text/javascript" src="<?php echo $templateDir.'/js/jquery.jcarousellite.min.js' ?>"></script>


<script type="text/javascript">
    jQuery(document).ready(function($) {
		
		var _isMobile = {
		  iOS: function() {
		   return navigator.userAgent.match(/iPhone/i);
		  },
		  any: function() {
		   return (_isMobile.iOS());
		  }
		};

        $(".related-view .related-vertical").jCarouselLite({
		  btnNext: ".related-view .next",
		  btnPrev: ".related-view .prev",
		  circular: false,
          visible:4,
		  vertical: _isMobile.any()? true : true
		});
    });
</script>
<?php
} ?>

