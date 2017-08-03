<?php
/**
 *
 * Show the product details page
 *
 * @package	VirtueMart
 * @subpackage
 * @author Max Milbers, Eugen Stranz, Max Galt
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2014 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id$
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

/* Let's see if we found the product */
if (empty($this->product)) {
	echo vmText::_('COM_VIRTUEMART_PRODUCT_NOT_FOUND');
	echo '<br /><br />  ' . $this->continue_link_html;
	return;
}

echo shopFunctionsF::renderVmSubLayout('askrecomjs',array('product'=>$this->product));



if(vRequest::getInt('print',false)){ ?>
<body onload="javascript:print();">
<?php } ?>

<div class="productdetails-view productdetails">
<div class="productdetail-content clearfix">
    <div class="yt-detail clearfix">
    <div class="col-sm-6 yt-detail-left">
        <?php
            echo $this->loadTemplate('images');
        ?>
    </div>
<?php
/* end yt-detail-left */
    ?>
    <div class="col-sm-6 yt-detail-right">
    <?php
    // Product Navigation
    if (VmConfig::get('product_navigation', 1)) {
	?>
        <div class="product-neighbours">
	    <?php
	    if (!empty($this->product->neighbours ['previous'][0])) {
		$prev_link = JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $this->product->neighbours ['previous'][0] ['virtuemart_product_id'] . '&virtuemart_category_id=' . $this->product->virtuemart_category_id, FALSE);
		echo JHtml::_('link', $prev_link, $this->product->neighbours ['previous'][0]
			['product_name'], array('rel'=>'prev', 'class' => 'previous-page','data-dynamic-update' => '1'));
	    }
	    if (!empty($this->product->neighbours ['next'][0])) {
		$next_link = JRoute::_('index.php?option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $this->product->neighbours ['next'][0] ['virtuemart_product_id'] . '&virtuemart_category_id=' . $this->product->virtuemart_category_id, FALSE);
		echo JHtml::_('link', $next_link, $this->product->neighbours ['next'][0] ['product_name'], array('rel'=>'next','class' => 'next-page','data-dynamic-update' => '1'));
	    }
	    ?>
    	<div class="clearfix"></div>
        </div>
    <?php } // Product Navigation END
    ?>

	<?php // Back To Category Button
	if ($this->product->virtuemart_category_id) {
		$catURL =  JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id='.$this->product->virtuemart_category_id, FALSE);
		$categoryName = vmText::_($this->product->category_name) ;
	} else {
		$catURL =  JRoute::_('index.php?option=com_virtuemart');
		$categoryName = vmText::_('COM_VIRTUEMART_SHOP_HOME') ;
	}
	?>
<?php
/*	
    <div class="back-to-category">
    	//<a href="<?php echo $catURL ?>" class="product-details" title="<?php echo $categoryName ?>"><?php echo vmText::sprintf('COM_VIRTUEMART_CATEGORY_BACK_TO',$categoryName) ?></a>
	//</div>
*/
?>
    <?php // Product Title   ?>
    <h2 class="title" itemprop="name"><?php echo $this->product->product_name ?></h2>
    <?php // Product Title END   ?>

    <?php // afterDisplayTitle Event
    echo $this->product->event->afterDisplayTitle ?>

    <?php
        
    // Product Edit Link
        echo '<div class="product-edit">';
    echo $this->edit_link;
        echo '</div>';
    // Product Edit Link END
        echo shopFunctionsF::renderVmSubLayout('rating',array('showRating'=>$this->showRating,'product'=>$this->product));
        echo '<br /><div class="availability">';
        echo shopFunctionsF::renderVmSubLayout('stockhandle',array('product'=>$this->product));
        echo '</div>';
    ?>

    <?php
    // PDF - Print - Email Icon
    if (VmConfig::get('show_emailfriend') || VmConfig::get('show_printicon') || VmConfig::get('pdf_icon')) {
	?>
        <div class="icons">
	    <?php

	    $link = 'index.php?tmpl=component&option=com_virtuemart&view=productdetails&virtuemart_product_id=' . $this->product->virtuemart_product_id;

		echo $this->linkIcon($link . '&format=pdf', 'COM_VIRTUEMART_PDF', 'pdf_button', 'pdf_icon', false);
	    //echo $this->linkIcon($link . '&print=1', 'COM_VIRTUEMART_PRINT', 'printButton', 'show_printicon');
		echo $this->linkIcon($link . '&print=1', 'COM_VIRTUEMART_PRINT', 'printButton', 'show_printicon',false,true,false,'class="printModal"');
		$MailLink = 'index.php?option=com_virtuemart&view=productdetails&task=recommend&virtuemart_product_id=' . $this->product->virtuemart_product_id . '&virtuemart_category_id=' . $this->product->virtuemart_category_id . '&tmpl=component';
	    echo $this->linkIcon($MailLink, 'COM_VIRTUEMART_EMAIL', 'emailButton', 'show_emailfriend', false,true,false,'class="recommened-to-friend"');
	    ?>
    	<div class="clearfix"></div>
        </div>
    <?php } // PDF - Print - Email Icon END
    ?>
<div class="clearfix"></div>
    <?php
    // Product Short Description
    if (!empty($this->product->product_s_desc)) {
        
	?>
        <div class="product-short-description">
	    <?php
            echo '<h3 class="product-short-title">'.vmText::_('COM_VIRTUEMART_PRODUCT_SHORT_DESC_TITLE') .'</h3>';
	    /** @todo Test if content plugins modify the product description */
            echo nl2br($this->product->product_s_desc);
	    ?>
        </div>
	<?php
    } // Product Short Description END

	echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>'ontop'));
    ?>

    <div class="product-info">


	    <div class="spacer-buy-area">

		<?php
		// TODO in Multi-Vendor not needed at the moment and just would lead to confusion
		/* $link = JRoute::_('index2.php?option=com_virtuemart&view=virtuemart&task=vendorinfo&virtuemart_vendor_id='.$this->product->virtuemart_vendor_id);
		  $text = vmText::_('COM_VIRTUEMART_VENDOR_FORM_INFO_LBL');
		  echo '<span class="bold">'. vmText::_('COM_VIRTUEMART_PRODUCT_DETAILS_VENDOR_LBL'). '</span>'; ?>
           // <a class="modal" href="<?php echo $link ?>"><?php echo $text ?></a>
		 */
		?>

		<?php
		

		

		//In case you are not happy using everywhere the same price display fromat, just create your own layout
		//in override /html/fields and use as first parameter the name of your file
		echo shopFunctionsF::renderVmSubLayout('prices',array('product'=>$this->product,'currency'=>$this->currency));
		?> <div class="clearfix"></div><?php
		echo shopFunctionsF::renderVmSubLayout('addtocart',array('product'=>$this->product));

		

		// Ask a question about this product
		if (VmConfig::get('ask_question', 0) == 1) {
			$askquestion_url = JRoute::_('index.php?option=com_virtuemart&view=productdetails&task=askquestion&virtuemart_product_id=' . $this->product->virtuemart_product_id . '&virtuemart_category_id=' . $this->product->virtuemart_category_id . '&tmpl=component', FALSE);
			?>
			<div class="ask-a-question">
				<a class="ask-a-question" href="<?php echo $askquestion_url ?>" rel="nofollow" ><?php echo vmText::_('COM_VIRTUEMART_PRODUCT_ENQUIRY_LBL') ?></a>
			</div>
		<?php
		}
		?>

		<?php
		// Manufacturer of the Product
		if (VmConfig::get('show_manufacturers', 1) && !empty($this->product->virtuemart_manufacturer_id)) {
		    echo $this->loadTemplate('manufacturer');
		}
		?>


	</div>
	<div class="clearfix"></div>
    <div class="cms-sociamedia uk-hidden-small">
				<div class="addthis_toolbox addthis_default_style ">
					<a class="addthis_button_facebook_like" fb:like:layout="button_count"></a>
					<a class="addthis_button_tweet"></a>
					<a class="addthis_button_pinterest_pinit"></a>
					<a class="addthis_counter addthis_pill_style"></a>
				</div>
				<script type="text/javascript">var addthis_config = {"data_track_addressbar":false};</script>
				<script type="text/javascript" src="//s7.addthis.com/js/300/addthis_widget.js#pubid=ra-506c066f69061863"></script>
			</div>

    </div>
<?php
	$count_images = count ($this->product->images);
	if ($count_images > 1) {
		//echo $this->loadTemplate('images_additional');
	}

	echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>'normal'));

    // Product Packaging
    $product_packaging = '';
    if ($this->product->product_box) {
	?>
        <div class="product-box">
	    <?php
	        echo vmText::_('COM_VIRTUEMART_PRODUCT_UNITS_IN_BOX') .$this->product->product_box;
	    ?>
        </div>
    <?php } // Product Packaging END ?>
    </div>
<?php
 //end yt-detail-right
    ?>
</div>
<?php //end yt-detail
    ?>
<div class="clearfix"></div>

<div class="col-md-3 col-sm-12 related-product">

    <?php
       // var_dump($this->product->customfieldsSorted['related_products']);die;
	echo shopFunctionsF::renderVmSubLayout('customfields',array('product'=>$this->product,'position'=>'onbot'));

  echo shopFunctionsF::renderVmSubLayout('related_products',array('product'=>$this->product,'position'=>'related_products','class'=> 'product-related-products','customTitle' => true ));

        //if( $this->product->customfieldsSorted["related_products"] >0) {echo 'check';};

	?>
</div>

<div class="col-md-9 col-sm-12 tab-product-detail">
    <div class="yt-tabs basic  pull-left" style="width:100%; height:100%">
        <ul class="nav-tabs clearfix">
            <li class="active"><a href="#Description" class="active"><?php echo vmText::_('COM_VIRTUEMART_PRODUCT_DESC_TITLE') ?></a></li>
            <li class=""><a href="#Reviews" class=""><?php echo vmText::_('COM_VIRTUEMART_PRODUCT_REVIEWS_TITLE') ?></a></li>
            <li class=""><a href="#Recategory" class=""><?php echo vmText::_('COM_VIRTUEMART_PRODUCT_RELATED_CATEGORY_TITLE') ?></a></li>
        </ul>
        <div class="tab-content">
            <div id="Description" class="clearfix active">
                <?php
                    // event onContentBeforeDisplay
                    echo $this->product->event->beforeDisplayContent; ?>

                <?php
                    // Product Description
                    if (!empty($this->product->product_desc)) {
                        ?>
                <div class="product-description">
                <?php /** @todo Test if content plugins modify the product description */ ?>
                <?php echo $this->product->product_desc; ?>
                </div>
                <?php
                    } // Product Description END
                    // onContentAfterDisplay event
                    echo $this->product->event->afterDisplayContent;
                    ?>
            </div>
            <div id="Reviews" class="clearfix " style="visibility: hidden; display: block;">
                <?php echo $this->loadTemplate('reviews');?>
            </div>
            <div id="Recategory" class="clearfix " style="visibility: hidden; display: block;">
                <?php echo shopFunctionsF::renderVmSubLayout('related_categories',array('product'=>$this->product,'position'=>'related_categories','class'=> 'product-related-categories')); ?>
            </div>
        </div>
    </div>

</div>
<?php


// Show child categories
if (VmConfig::get('showCategory', 1)) {
	echo $this->loadTemplate('showcategory');
}

$j = 'jQuery(document).ready(function($) {
	Virtuemart.product(jQuery("form.product"));

	$("form.js-recalculate").each(function(){
		if ($(this).find(".product-fields").length && !$(this).find(".no-vm-bind").length) {
			var id= $(this).find(\'input[name="virtuemart_product_id[]"]\').val();
			Virtuemart.setproducttype($(this),id);

		}
	});
});';
//vmJsApi::addJScript('recalcReady',$j);

/** GALT
	 * Notice for Template Developers!
	 * Templates must set a Virtuemart.container variable as it takes part in
	 * dynamic content update.
	 * This variable points to a topmost element that holds other content.
	 */
$j = "Virtuemart.container = jQuery('.productdetails-view');
Virtuemart.containerSelector = '.productdetails-view';";

vmJsApi::addJScript('ajaxContent',$j);

echo vmJsApi::writeJS();

if ($this->product->prices['salesPrice'] > 0) {
  echo shopFunctionsF::renderVmSubLayout('snippets',array('product'=>$this->product, 'currency'=>$this->currency, 'showRating'=>$this->showRating));
}

?>
</div>
</div>



