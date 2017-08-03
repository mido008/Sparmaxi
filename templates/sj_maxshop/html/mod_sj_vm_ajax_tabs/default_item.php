<?php


defined('_JEXEC') or die;
$assets_img = JURI::root().'templates/'.$template.'/images/rating';

$max= 0;

foreach($item->categories as $categorie_id)
{
    if($max <= $categorie_id) $max=$categorie_id;
}

$pos = strpos($item->link,"virtuemart_category_id=");
$ch = substr($item->link,0,$pos)."virtuemart_category_id=$max";
$item->link = $ch;
$item->url = $ch;

$ratingModel = VmModel::getModel('ratings');
$currency = CurrencyDisplay::getInstance();
?>
<div class="item-wrap<?php if ($i % $nb_column == 0) {echo $item_last_css;} ?> ajaxtabs-item">
	<?php
	$img = VmAjaxtabsBaseHelper::getVmImage($item, $params);
	if ($img):?>
		<div class="item-image">
			<a href="<?php echo $item->link; ?>"
			   title="<?php echo $item->title; ?>" <?php echo VmAjaxtabsBaseHelper::parseTarget($params->get('item_link_target', '_blank')); ?>>
				<?php echo VmAjaxtabsBaseHelper::imageTag($img);?>
			</a>
		</div>
	<?php endif; // image display ?>

	<div class="product-info">
    <div class="product-review">
			<?php
				$maxrating = VmConfig::get('vm_maximum_rating_scale', 5);
				if (empty($item->rating)) {
				?>
					<div class="ratingbox dummy" title="<?php echo vmText::_('COM_VIRTUEMART_UNRATED'); ?>" ></div>
				<?php
				} else {
					$ratingwidth = $item->rating * 14;
			  ?>

				<div title=" <?php echo (vmText::_("COM_VIRTUEMART_RATING_TITLE") . round($item->rating) . '/' . $maxrating) ?>" class="ratingbox" >
					<div class="stars-orange" style="width:<?php echo $ratingwidth.'px'; ?>"></div>
				</div>
				<?php
				}
			?>							
			
							 
	 </div>
	<?php if ((int)$params->get('item_title_display', 1)): ?>
		<div class="item-title">
			<a href="<?php echo $item->link; ?>"
			   title="<?php echo $item->title; ?>" <?php echo VmAjaxtabsBaseHelper::parseTarget($params->get('item_link_target', '_blank')); ?>>
				<?php echo VmAjaxtabsBaseHelper::truncate($item->title, $params->get('item_title_max_characs', 100)); ?>
			</a>
		</div>
	<?php endif; // title display ?>
	
	 
	<?php if ((int)$params->get('item_price_display', 1)) { ?>
		<div class="item-price">
			<?php
			if (!empty($item->prices['salesPrice'])) {
				echo $currency->createPriceDiv('salesPrice', JText::_("Price: "), $item->prices, false, false, 1.0, true);
                //var_dump($item);die;
			}
			if (!empty($item->prices['discountAmount'])) {
				$currency = CurrencyDisplay::getInstance();
				echo $currency->createPriceDiv('discountAmount', JText::_("Price: "), $item->prices, false, false, 1.0, true);
			} ?>
		</div>
	<?php } ?>
	<?php if ($params->get('item_addtocart_display', 1)) { ?>
        <div class="item-addtocart">
            <?php echo shopFunctionsF::renderVmSubLayout('addtocart',array('product'=>$item)); ?>
        </div>
    <?php } ?>
    </div>
</div>

