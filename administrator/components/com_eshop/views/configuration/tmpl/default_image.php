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
?>
<table class="admintable adminform">
	<tr>
		<td>
			<span class="required">*</span><?php echo  JText::_('ESHOP_CONFIG_VIEW_IMAGE'); ?>:<br />
			<span class="help"><?php echo JText::_('ESHOP_CONFIG_VIEW_IMAGE_HELP'); ?></span>
		</td>
		<td>
			<?php echo $this->lists['view_image']; ?>
		</td>
	</tr>
	<tr>
		<td colspan="2">
			<?php echo  JText::_('ESHOP_CONFIG_IMAGE_SIZE_FUNCTION'); ?>:
			<span class="help"><?php echo JText::_('ESHOP_CONFIG_IMAGE_SIZE_FUNCTION_HELP'); ?></span>
		</td>
	</tr>
	<tr>
		<td>
			<span class="required">*</span><?php echo  JText::_('ESHOP_CONFIG_CATEGORY_IMAGE_SIZE'); ?>:
		</td>
		<td>
			<input type="text" value="<?php echo $this->config->image_category_width; ?>" name="image_category_width" class="input-mini" />
                x
             <input type="text" value="<?php echo $this->config->image_category_height; ?>" name="image_category_height" class="input-mini" />
		</td>
		<td>
			<?php echo $this->lists['category_image_size_function']; ?>
		</td>
	</tr>
	<tr>
		<td>
			<span class="required">*</span><?php echo  JText::_('ESHOP_CONFIG_MANUFACTURER_IMAGE_SIZE'); ?>:
		</td>
		<td>
			<input type="text" value="<?php echo $this->config->image_manufacturer_width; ?>" name="image_manufacturer_width" class="input-mini" />
                x
             <input type="text" value="<?php echo $this->config->image_manufacturer_height; ?>" name="image_manufacturer_height" class="input-mini" />
		</td>
		<td>
			<?php echo $this->lists['manufacturer_image_size_function']; ?>
		</td>
	</tr>
	<tr>
		<td>
			<span class="required">*</span><?php echo  JText::_('ESHOP_CONFIG_PRODUCT_IMAGE_THUMB_SIZE'); ?>:
		</td>
		<td>
			<input type="text" value="<?php echo $this->config->image_thumb_width; ?>" name="image_thumb_width" class="input-mini" />
                x
             <input type="text" value="<?php echo $this->config->image_thumb_height; ?>" name="image_thumb_height" class="input-mini" />
		</td>
		<td>
			<?php echo $this->lists['thumb_image_size_function']; ?>
		</td>
	</tr>
	<tr>
		<td>
			<span class="required">*</span><?php echo  JText::_('ESHOP_CONFIG_PRODUCT_IMAGE_POPUP_SIZE'); ?>:
		</td>
		<td>
			<input type="text" value="<?php echo $this->config->image_popup_width; ?>" name="image_popup_width" class="input-mini" />
                x
             <input type="text" value="<?php echo $this->config->image_popup_height; ?>" name="image_popup_height" class="input-mini" />
		</td>
		<td>
			<?php echo $this->lists['popup_image_size_function']; ?>
		</td>
	</tr>	
	<tr>
		<td>
			<span class="required">*</span><?php echo  JText::_('ESHOP_CONFIG_PRODUCT_IMAGE_LIST_SIZE'); ?>:
		</td>
		<td>
			<input type="text" value="<?php echo $this->config->image_list_width; ?>" name="image_list_width" class="input-mini" />
                x
             <input type="text" value="<?php echo $this->config->image_list_height; ?>" name="image_list_height" class="input-mini" />
		</td>
		<td>
			<?php echo $this->lists['list_image_size_function']; ?>
		</td>
	</tr>
	<tr>
		<td>
			<span class="required">*</span><?php echo  JText::_('ESHOP_CONFIG_ADDITIONAL_PRODUCT_IMAGE_SIZE'); ?>:
		</td>
		<td>
			<input type="text" value="<?php echo $this->config->image_additional_width; ?>" name="image_additional_width" class="input-mini" />
                x
             <input type="text" value="<?php echo $this->config->image_additional_height; ?>" name="image_additional_height" class="input-mini" />
		</td>
		<td>
			<?php echo $this->lists['additional_image_size_function']; ?>
		</td>
	</tr>	
	<tr>
		<td>
			<span class="required">*</span><?php echo  JText::_('ESHOP_CONFIG_RELATED_PRODUCT_IMAGE_SIZE'); ?>:
		</td>
		<td>
			<input type="text" value="<?php echo $this->config->image_related_width; ?>" name="image_related_width" class="input-mini" />
                x
             <input type="text" value="<?php echo $this->config->image_related_height; ?>" name="image_related_height" class="input-mini" />
		</td>
		<td>
			<?php echo $this->lists['related_image_size_function']; ?>
		</td>
	</tr>	
	<tr>
		<td>
			<span class="required">*</span><?php echo  JText::_('ESHOP_CONFIG_COMPARE_IMAGE_SIZE'); ?>:
		</td>
		<td>
			<input type="text" value="<?php echo $this->config->image_compare_width; ?>" name="image_compare_width" class="input-mini" />
                x
             <input type="text" value="<?php echo $this->config->image_compare_height; ?>" name="image_compare_height" class="input-mini" />
		</td>
		<td>
			<?php echo $this->lists['compare_image_size_function']; ?>
		</td>
	</tr>	
	<tr>
		<td>
			<span class="required">*</span><?php echo  JText::_('ESHOP_CONFIG_WISH_LIST_IMAGE_SIZE'); ?>:
		</td>
		<td>
			<input type="text" value="<?php echo $this->config->image_wishlist_width; ?>" name="image_wishlist_width" class="input-mini" />
                x
             <input type="text" value="<?php echo $this->config->image_wishlist_height; ?>" name="image_wishlist_height" class="input-mini" />
		</td>
		<td>
			<?php echo $this->lists['wishlist_image_size_function']; ?>
		</td>
	</tr>	
	<tr>
		<td>
			<span class="required">*</span><?php echo  JText::_('ESHOP_CONFIG_CART_IMAGE_SIZE'); ?>:
		</td>
		<td>
			<input type="text" value="<?php echo $this->config->image_cart_width; ?>" name="image_cart_width" class="input-mini" />
                x
             <input type="text" value="<?php echo $this->config->image_cart_height; ?>" name="image_cart_height" class="input-mini" />
		</td>
		<td>
			<?php echo $this->lists['cart_image_size_function']; ?>
		</td>
	</tr>
	<tr>
		<td>
			<span class="required">*</span><?php echo  JText::_('ESHOP_CONFIG_LABEL_IMAGE_SIZE'); ?>:
		</td>
		<td>
			<input type="text" value="<?php echo $this->config->image_label_width; ?>" name="image_label_width" class="input-mini" />
                x
             <input type="text" value="<?php echo $this->config->image_label_height; ?>" name="image_label_height" class="input-mini" />
		</td>
		<td>
			<?php echo $this->lists['label_image_size_function']; ?>
		</td>
	</tr>
	<tr>
		<td>
			<span class="required">*</span><?php echo  JText::_('ESHOP_CONFIG_OPTION_IMAGE_SIZE'); ?>:
		</td>
		<td>
			<input type="text" value="<?php echo $this->config->image_option_width; ?>" name="image_option_width" class="input-mini" />
                x
             <input type="text" value="<?php echo $this->config->image_option_height; ?>" name="image_option_height" class="input-mini" />
		</td>
		<td>
			<?php echo $this->lists['option_image_size_function']; ?>
		</td>
	</tr>
</table>