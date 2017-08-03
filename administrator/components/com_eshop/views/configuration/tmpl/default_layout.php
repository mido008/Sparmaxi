<?php
/**
 * @version		1.4.1
 * @package		Joomla
 * @subpackage	EShop
 * @author  	Giang Dinh Truong
 * @copyright	Copyright (C) 2012 Ossolution Team
 * @license		GNU/GPL, see LICENSE.php
 */
// no direct access
defined( '_JEXEC' ) or die();
$editor = JFactory::getEditor();
?>
<table class="admintable adminform">
	<tr>
		<td class="key" colspan="2">
			<h2><?php echo JText::_('ESHOP_CONFIG_LAYOUT_GENERAL'); ?></h2>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo  JText::_('ESHOP_CONFIG_THEME'); ?>:
		</td>
		<td>
			<?php echo $this->lists['theme']; ?>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo  JText::_('ESHOP_CONFIG_LOAD_BOOTSTRAP_CSS'); ?>:
		</td>
		<td>
			<?php echo $this->lists['load_bootstrap_css']; ?>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo  JText::_('ESHOP_CONFIG_DATE_FORMAT'); ?>:
			<span class="help"><?php echo JText::_('ESHOP_CONFIG_DATE_FORMAT_HELP'); ?></span>
		</td>
		<td>
			<input class="input-large" type="text" name="date_format" id="date_format"  value="<?php echo isset($this->config->date_format) ? $this->config->date_format : 'm-d-Y'; ?>" />
		</td>
	</tr>
	<tr>
		<td>
			<?php echo  JText::_('ESHOP_CONFIG_LOAD_BOOTSTRAP_JAVASCRIPT'); ?>:
		</td>
		<td>
			<?php echo $this->lists['load_bootstrap_js']; ?>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo  JText::_('ESHOP_CONFIG_SHOW_CATEGORIES_NAVIGATION'); ?>:
		</td>
		<td>
			<?php echo $this->lists['show_categories_nav']; ?>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo  JText::_('ESHOP_CONFIG_SHOW_PRODUCTS_NAVIGATION'); ?>:
		</td>
		<td>
			<?php echo $this->lists['show_products_nav']; ?>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo  JText::_('ESHOP_CONFIG_SHIPPING_ADDRESS_FORMAT'); ?>:
		</td>
		<td>
			<?php echo $editor->display( 'shipping_address_format', isset($this->config->shipping_address_format) ? $this->config->shipping_address_format : '[SHIPPING_FIRSTNAME] [SHIPPING_LASTNAME]<br /> [SHIPPING_ADDRESS_1], [SHIPPING_ADDRESS_2]<br /> [SHIPPING_CITY], [SHIPPING_POSTCODE] [SHIPPING_ZONE_NAME]<br /> [SHIPPING_EMAIL]<br /> [SHIPPING_TELEPHONE]<br /> [SHIPPING_FAX]', '100%', '250', '75', '10' ); ?>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo  JText::_('ESHOP_CONFIG_PAYMENT_ADDRESS_FORMAT'); ?>:
		</td>
		<td>
			<?php echo $editor->display( 'payment_address_format', isset($this->config->payment_address_format) ? $this->config->payment_address_format : '[PAYMENT_FIRSTNAME] [PAYMENT_LASTNAME]<br /> [PAYMENT_ADDRESS_1], [PAYMENT_ADDRESS_2]<br /> [PAYMENT_CITY], [PAYMENT_POSTCODE] [PAYMENT_ZONE_NAME]<br /> [PAYMENT_EMAIL]<br /> [PAYMENT_TELEPHONE]<br /> [PAYMENT_FAX]', '100%', '250', '75', '10' ); ?>
		</td>
	</tr>
	<tr>
		<td class="key" colspan="2">
			<h2><?php echo JText::_('ESHOP_CONFIG_PRODUCT_PAGE'); ?></h2>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo  JText::_('ESHOP_CONFIG_SHOW_MANUFACTURER'); ?>:
			<span class="help"><?php echo JText::_('ESHOP_CONFIG_SHOW_MANUFACTURER_HELP'); ?></span>
		</td>
		<td>
			<?php echo $this->lists['show_manufacturer']; ?>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo  JText::_('ESHOP_CONFIG_SHOW_SKU'); ?>:
			<span class="help"><?php echo JText::_('ESHOP_CONFIG_SHOW_SKU_HELP'); ?></span>
		</td>
		<td>
			<?php echo $this->lists['show_sku']; ?>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo  JText::_('ESHOP_CONFIG_SHOW_AVAILABILITY'); ?>:
			<span class="help"><?php echo JText::_('ESHOP_CONFIG_SHOW_AVAILABILITY_HELP'); ?></span>
		</td>
		<td>
			<?php echo $this->lists['show_availability']; ?>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo  JText::_('ESHOP_CONFIG_SHOW_PRODUCT_WEIGHT'); ?>:
			<span class="help"><?php echo JText::_('ESHOP_CONFIG_SHOW_PRODUCT_WEIGHT_HELP'); ?></span>
		</td>
		<td>
			<?php echo $this->lists['show_product_weight']; ?>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo  JText::_('ESHOP_CONFIG_SHOW_PRODUCT_DIMENSIONS'); ?>:
			<span class="help"><?php echo JText::_('ESHOP_CONFIG_SHOW_PRODUCT_DIMENSIONS_HELP'); ?></span>
		</td>
		<td>
			<?php echo $this->lists['show_product_dimensions']; ?>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo  JText::_('ESHOP_CONFIG_SHOW_PRODUCT_TAGS'); ?>:
			<span class="help"><?php echo JText::_('ESHOP_CONFIG_SHOW_PRODUCT_TAGS_HELP'); ?></span>
		</td>
		<td>
			<?php echo $this->lists['show_product_tags']; ?>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo  JText::_('ESHOP_CONFIG_SHOW_SPECIFICATION'); ?>:
			<span class="help"><?php echo JText::_('ESHOP_CONFIG_SHOW_SPECIFICATION_HELP'); ?></span>
		</td>
		<td>
			<?php echo $this->lists['show_specification']; ?>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo  JText::_('ESHOP_CONFIG_SHOW_RELATED_PRODUCTS'); ?>:
			<span class="help"><?php echo JText::_('ESHOP_CONFIG_SHOW_RELATED_PRODUCTS_HELP'); ?></span>
		</td>
		<td>
			<?php echo $this->lists['show_related_products']; ?>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo  JText::_('ESHOP_CONFIG_SHOW_QUANTITY_BOX_IN_PRODUCT_PAGE'); ?>:
			<span class="help"><?php echo JText::_('ESHOP_CONFIG_SHOW_QUANTITY_BOX_IN_PRODUCT_PAGE_HELP'); ?></span>
		</td>
		<td>
			<?php echo $this->lists['show_quantity_box_in_product_page']; ?>
		</td>
	</tr>
	<tr>
		<td class="key" colspan="2">
			<h2><?php echo JText::_('ESHOP_CONFIG_CATEGORY_PAGE'); ?></h2>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo  JText::_('ESHOP_CONFIG_SHOW_CATEGORY_IMAGE'); ?>:
			<span class="help"><?php echo JText::_('ESHOP_CONFIG_SHOW_CATEGORY_IMAGE_HELP'); ?></span>
		</td>
		<td>
			<?php echo $this->lists['show_category_image']; ?>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo  JText::_('ESHOP_CONFIG_SHOW_CATEGORY_DESC'); ?>:
			<span class="help"><?php echo JText::_('ESHOP_CONFIG_SHOW_CATEGORY_DESC_HELP'); ?></span>
		</td>
		<td>
			<?php echo $this->lists['show_category_desc']; ?>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo  JText::_('ESHOP_CONFIG_SHOW_PRODUCTS_IN_ALL_LEVELS'); ?>:
			<span class="help"><?php echo JText::_('ESHOP_CONFIG_SHOW_PRODUCTS_IN_ALL_LEVELS_HELP'); ?></span>
		</td>
		<td>
			<?php echo $this->lists['show_products_in_all_levels']; ?>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo  JText::_('ESHOP_CONFIG_SHOW_SUB_CATEGORIES'); ?>:
			<span class="help"><?php echo JText::_('ESHOP_CONFIG_SHOW_SUB_CATEGORIES_HELP'); ?></span>
		</td>
		<td>
			<?php echo $this->lists['show_sub_categories']; ?>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo  JText::_('ESHOP_CONFIG_SUB_CATEGORIES_LAYOUT'); ?>:
			<span class="help"><?php echo JText::_('ESHOP_CONFIG_SUB_CATEGORIES_LAYOUT_HELP'); ?></span>
		</td>
		<td>
			<?php echo $this->lists['sub_categories_layout']; ?>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo  JText::_('ESHOP_CONFIG_DEFAULT_PRODUCTS_LAYOUT'); ?>:
			<span class="help"><?php echo JText::_('ESHOP_CONFIG_DEFAULT_PRODUCTS_LAYOUT_HELP'); ?></span>
		</td>
		<td>
			<?php echo $this->lists['default_products_layout']; ?>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo  JText::_('ESHOP_CONFIG_SHOW_QUANTITY_BOX'); ?>:
			<span class="help"><?php echo JText::_('ESHOP_CONFIG_SHOW_QUANTITY_BOX_HELP'); ?></span>
		</td>
		<td>
			<?php echo $this->lists['show_quantity_box']; ?>
		</td>
	</tr>
	<tr>
		<td class="key" colspan="2">
			<h2><?php echo JText::_('ESHOP_CONFIG_COMPARE_PAGE'); ?></h2>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo  JText::_('ESHOP_CONFIG_COMPARE_IMAGE'); ?>:
		</td>
		<td>
			<?php echo $this->lists['compare_image']; ?>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo  JText::_('ESHOP_CONFIG_COMPARE_PRICE'); ?>:
		</td>
		<td>
			<?php echo $this->lists['compare_price']; ?>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo  JText::_('ESHOP_CONFIG_COMPARE_SKU'); ?>:
		</td>
		<td>
			<?php echo $this->lists['compare_sku']; ?>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo  JText::_('ESHOP_CONFIG_COMPARE_MANUFACTURER'); ?>:
		</td>
		<td>
			<?php echo $this->lists['compare_manufacturer']; ?>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo  JText::_('ESHOP_CONFIG_COMPARE_AVAILABILITY'); ?>:
		</td>
		<td>
			<?php echo $this->lists['compare_availability']; ?>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo  JText::_('ESHOP_CONFIG_COMPARE_RATING'); ?>:
		</td>
		<td>
			<?php echo $this->lists['compare_rating']; ?>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo  JText::_('ESHOP_CONFIG_COMPARE_SHORT_DESC'); ?>:
		</td>
		<td>
			<?php echo $this->lists['compare_short_desc']; ?>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo  JText::_('ESHOP_CONFIG_COMPARE_DESC'); ?>:
		</td>
		<td>
			<?php echo $this->lists['compare_desc']; ?>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo  JText::_('ESHOP_CONFIG_COMPARE_WEIGHT'); ?>:
		</td>
		<td>
			<?php echo $this->lists['compare_weight']; ?>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo  JText::_('ESHOP_CONFIG_COMPARE_DIMENSIONS'); ?>:
		</td>
		<td>
			<?php echo $this->lists['compare_dimensions']; ?>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo  JText::_('ESHOP_CONFIG_COMPARE_ATTRIBUTES'); ?>:
		</td>
		<td>
			<?php echo $this->lists['compare_attributes']; ?>
		</td>
	</tr>
</table>