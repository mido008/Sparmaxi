<?php


defined('_JEXEC') or die('Restricted access');
$products_per_row = $viewData['products_per_row'];
$currency = $viewData['currency'];
$showRating = $viewData['showRating'];
$verticalseparator = " vertical-separator";
echo shopFunctionsF::renderVmSubLayout('askrecomjs');
$ItemidStr = '';
$Itemid = shopFunctionsF::getLastVisitedItemId();
if(!empty($Itemid)){
	$ItemidStr = '&Itemid='.$Itemid;
}

foreach ($viewData['products'] as $type => $products ) {

	$rowsHeight = shopFunctionsF::calculateProductRowsHeights($products,$currency,$products_per_row);

	if(!empty($type) and count($products)>0){
		$productTitle = vmText::_('COM_VIRTUEMART_'.strtoupper($type).'_PRODUCT'); ?>
<div class="<?php echo $type ?>-view">
  <h4><?php echo $productTitle ?></h4>
		<?php // Start the Output
    }

	// Calculating Products Per Row
	$cellwidth = ' width'.floor ( 100 / $products_per_row );

	$BrowseTotalProducts = count($products);

	$col = 1;
	$nb = 1;
	$row = 1;

	foreach ( $products as $product ) {

		// Show the horizontal seperator
		if ($col == 1 && $nb > $products_per_row) { ?>
	
		<?php }

		// this is an indicator wether a row needs to be opened or not
		if ($col == 1) { ?>
	<div class="row">
		<?php }

		// Show the vertical seperator
		if ($nb == $products_per_row or $nb % $products_per_row == 0) {
			$show_vertical_separator = ' ';
		} else {
			$show_vertical_separator = $verticalseparator;
		}
            //var_dump($products_per_row);die;

		$max= 0;
		
		foreach($product->categories as $categorie_id)
		{
		    if($max <= $categorie_id) $max=$categorie_id;
		}
		
		$pos = strpos($product->link,"virtuemart_category_id=");
		$ch = substr($product->link,0,$pos)."virtuemart_category_id=$max";
		$product->link = $ch;
		$product->url = $ch;
		
// 		print_r("<pre>");
// 	    print_r($ItemidStr);
// 	    print_r("__________<br> $max <br> ----- $pos ------ <br>");
// 	    print_r("***************<br>");
// 	    print_r($ch);
// 	    print_r("***************<br>");
// 	    print_r($product->link);
// 	    print_r("__________<br>");
// 	    print_r(VMDealsPHelper::parseTarget($params->get('link_target')));
// 	    print_r("__________<br>");
// 	    print_r($item->title);
// 	    print_r("</pre>");
		
    // Show Products ?>
	<div class="product vm-col vm-col-<?php echo number_format(12/$products_per_row) . $show_vertical_separator ?> col-md-<?php echo number_format(12/$products_per_row)?> col-sm-<?php echo number_format(12/$products_per_row)?> ">
		<div class="spacer">
			<div class="vm-product-media-container">

					<a title="<?php echo $product->product_name ?>" href="<?php echo $product->link.$ItemidStr; ?>">
						<?php
						echo $product->images[0]->displayMediaThumb('class="browseProductImage"', false);
                            
						?>
					</a>
                    <div class="vm3pr-<?php echo $rowsHeight[$row]['customfields'] ?>">
                        <?php echo shopFunctionsF::renderVmSubLayout('addtocart',array('product'=>$product,'rowHeights'=>$rowsHeight[$row])); ?>
                    </div>

			</div>

			<div class="product-info">
                <div class="vm-product-rating-container">
					<?php echo shopFunctionsF::renderVmSubLayout('rating',array('showRating'=>$showRating, 'product'=>$product));
					if ( VmConfig::get ('display_stock', 1)) { ?>
						<span class="vmicon vm2-<?php echo $product->stock->stock_level ?>" title="<?php echo $product->stock->stock_tip ?>"></span>
					<?php }
					echo '<div class="yt-stock">';
                        echo shopFunctionsF::renderVmSubLayout('stockhandle',array('product'=>$product));
                    echo '</div>';
					?>
				</div>
				<div class="vm-product-descr-container-<?php echo $rowsHeight[$row]['product_s_desc'] ?>">
					<h2><?php echo JHtml::link ($product->link.$ItemidStr, $product->product_name); ?></h2>
					<?php ?>
				</div>



				<?php //echo $rowsHeight[$row]['price'] ?>
				<div class="vm3pr-<?php echo $rowsHeight[$row]['price'] ?>"> 
					<?php
					   echo "<span>".shopFunctionsF::renderVmSubLayout('prices',array('product'=>$product,'currency'=>$currency))."</span>"; 
				    ?>
					<div class="clear"></div>
					<?php
// 					   $discount_price = $product->prices['salesPrice'] + ($product->prices['salesPrice']*150/100);
// 					   echo '<div style="float:left;" class="item-prices-final">';
// 					   echo $currency->createPriceDiv('salesPrice', JText::_("Price: "), $discount_price, false, false, 1.0, true);
// 					   echo '</div>';
							
					?>
					
					
				</div>
				
				<?php 
// 				if(!empty($rowsHeight[$row]['product_s_desc'])){
				    
// 					<p class="product_s_desc">
// 						<?php // Product Short Description
// 						if (!empty($product->product_s_desc)) {
// 							echo shopFunctionsF::limitStringByWord ($product->product_s_desc, 500, ' ...') 
// ? >
// 						<?php } ? >
// 					</p>
				//<?php
// 				  } ?>
				
                <div class="vm3pr-<?php echo $rowsHeight[$row]['customfields'] ?>">
                        <?php echo shopFunctionsF::renderVmSubLayout('addtocart',array('product'=>$product,'rowHeights'=>$rowsHeight[$row])); ?>
                    </div>
				
				<?php //echo $rowsHeight[$row]['customs'] ?>

	
				<div class="vm-details-button">
					<?php // Product Details Button
					$link = empty($product->link)? $product->canonical:$product->link;
					echo JHtml::link($link.$ItemidStr,vmText::_ ( 'COM_VIRTUEMART_PRODUCT_DETAILS' ), array ('title' => $product->product_name, 'class' => 'product-details' ) );
					//echo JHtml::link ( JRoute::_ ( 'index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $product->virtuemart_product_id . '&virtuemart_category_id=' . $product->virtuemart_category_id , FALSE), vmText::_ ( 'COM_VIRTUEMART_PRODUCT_DETAILS' ), array ('title' => $product->product_name, 'class' => 'product-details' ) );
					?>
				</div>
			</div>
		</div>
	</div>

	<?php
    $nb ++;

      // Do we need to close the current row now?
      if ($col == $products_per_row || $nb>$BrowseTotalProducts) { ?>
    <div class="clear"></div>
  </div>
      <?php
      	$col = 1;
		$row++;
    } else {
      $col ++;
    }
  }

      if(!empty($type)and count($products)>0){
        // Do we need a final closing row tag?
        //if ($col != 1) {
      ?>
    <div class="clear"></div>
  </div>
    <?php
    // }
    }
  }
