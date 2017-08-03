<?php

defined ('_JEXEC') or die('Restricted access');
JHtml::_ ('behavior.modal');

$js = "
jQuery(document).ready(function () {
	jQuery('.orderlistcontainer').hover(
		function() { jQuery(this).find('.orderlist').stop().show()},
		function() { jQuery(this).find('.orderlist').stop().hide()}
	)
	
	jQuery('.vm-view-list .vm-view').each(function() {
		var ua = navigator.userAgent,
		event = (ua.match(/iPad/i)) ? 'touchstart' : 'click';
		
		jQuery(this).bind(event, function() {
			
			jQuery(this).addClass(function() {
				if(jQuery(this).hasClass('active')) return ''; 
				return 'active';
			});
			jQuery(this).siblings('.vm-view').removeClass('active');
			
			if(jQuery('.view-list').hasClass('active')) {
				jQuery('.browse-view .product').addClass('vm-col-1');
			}else{
				jQuery('.browse-view .product').removeClass('vm-col-1');
			}
		});
		
	});
});
";

vmJsApi::addJScript('vm.hover',$js);

if (empty($this->keyword) and !empty($this->category)) {
	?>
<div class="category_description">
	<?php echo $this->category->category_description; ?>
</div>
<?php
}

// Show child categories
if (VmConfig::get ('showCategory', 1) and empty($this->keyword)) {
	if (!empty($this->category->haschildren)) {

		echo ShopFunctionsF::renderVmSubLayout('categories',array('categories'=>$this->category->children));

	}
}

if($this->showproducts){
?>
<div class="browse-view">
<?php

if (!empty($this->keyword)) {
	//id taken in the view.html.php could be modified
	$category_id  = vRequest::getInt ('virtuemart_category_id', 0); ?>
	

	<form action="<?php echo JRoute::_('index.php?option=com_virtuemart&view=category&search=false&limitstart=0' ); ?>" method="get">

		<!--BEGIN Search Box -->
		<div class="virtuemart_search">
			<?php echo $this->searchcustom ?>
			<br/>
			<?php echo $this->searchCustomValues ?>
			<input name="keyword" class="inputbox" type="text" size="40" value="<?php echo $this->keyword ?>"/>
			<input type="submit" value="<?php echo vmText::_ ('COM_VIRTUEMART_SEARCH') ?>" class="button" onclick="this.form.keyword.focus();"/>
		</div>
		<input type="hidden" name="search" value="true"/>
		<input type="hidden" name="view" value="category"/>
		<input type="hidden" name="option" value="com_virtuemart"/>
		<input type="hidden" name="virtuemart_category_id" value="<?php echo $category_id; ?>"/>

	</form>
	<!-- End Search Box -->
<?php  } ?>

<?php // Show child categories

	?>
<div class="orderby-displaynumber">
	<div class="vm-view-list col-md-2 col-sm-2 col-xs-12">
		<div class="vm-view view-grid active"><i class="listing-icon grid"></i></div>
		<div class="vm-view view-list"><i class="listing-icon list"></i></div>
	</div>
	<div class="toolbar-center col-md-6 col-sm-6 col-xs-12">
		<div class="vm-order-list">
			<?php echo $this->orderByList['orderby']; ?>
			<?php echo $this->orderByList['manufacturer']; ?>
		</div>
		<div class="set-desc arrow-up">
			<i class="long-arrow-up"></i>
		</div>	
		<div class="display-number"><?php echo 'Show';?><?php echo $this->vmPagination->getLimitBox ($this->category->limit_list_step); ?></div>
	</div>
	
	<div class="vm-pagination vm-pagination-top col-md-4 col-sm-4 col-xs-12">
		<?php echo $this->vmPagination->getPagesLinks (); ?>
	</div>
	
	<div class="clear"></div>
</div> <!-- end of orderby-displaynumber -->

   <!--<h1><?php //echo $this->category->category_name; ?></h1>-->

	<?php
	if (!empty($this->products)) {
	$products = array();
	$products[0] = $this->products;
	echo shopFunctionsF::renderVmSubLayout($this->productsLayout,array('products'=>$products,'currency'=>$this->currency,'products_per_row'=>$this->perRow,'showRating'=>$this->showRating));

	?>

<div class="orderby-displaynumber">
	<div class="vm-view-list col-md-2 col-sm-2 col-xs-12">
		<div class="vm-view view-grid active"><i class="listing-icon grid"></i></div>
		<div class="vm-view view-list"><i class="listing-icon list"></i></div>
	</div>
	<div class="toolbar-center col-md-6 col-sm-6 col-xs-12">
		<div class="vm-order-list">
			<?php echo $this->orderByList['orderby']; ?>
			<?php echo $this->orderByList['manufacturer']; ?>
		</div>
		<div class="set-desc arrow-down">
			<i class="long-arrow-down"></i>
		</div>	
		<div class="display-number"><?php echo 'Show';?><?php echo $this->vmPagination->getLimitBox ($this->category->limit_list_step); ?></div>
	</div>
	
	<div class="vm-pagination vm-pagination-top col-md-4 col-sm-4 col-xs-12">
		<?php echo $this->vmPagination->getPagesLinks (); ?>
	</div>
	
	<div class="clear"></div>
</div> <!-- end of orderby-displaynumber -->

	<?php
} elseif (!empty($this->keyword)) {
	echo vmText::_ ('COM_VIRTUEMART_NO_RESULT') . ($this->keyword ? ' : (' . $this->keyword . ')' : '');
}
?>
</div>

<?php } ?>

<!-- end browse-view -->