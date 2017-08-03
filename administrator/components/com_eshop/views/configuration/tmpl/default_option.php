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
			<td class="key" colspan="2">
				<h2><?php echo JText::_('ESHOP_CONFIG_ITEMS'); ?></h2>
			</td>
		</tr>
		<tr>
			<td width="50%">
				<span class="required">*</span><?php echo JText::_('ESHOP_CONFIG_DEFAULT_ITEMS_PER_PAGE'); ?>:<br />
				<span class="help"><?php echo JText::_('ESHOP_CONFIG_DEFAULT_ITEMS_PER_PAGE_HELP'); ?></span>
			</td>
			<td>
				<input class="input-mini" type="text" name="catalog_limit" id="catalog_limit"  value="<?php echo isset($this->config->catalog_limit) ? $this->config->catalog_limit : ''; ?>" />
			</td>
		</tr>
		<tr>
			<td width="50%">
				<span class="required">*</span><?php echo JText::_('ESHOP_CONFIG_DEFAULT_ITEMS_PER_ROW'); ?>:<br />
				<span class="help"><?php echo JText::_('ESHOP_CONFIG_DEFAULT_ITEMS_PER_ROW_HELP'); ?></span>
			</td>
			<td>
				<input class="input-mini" type="text" name="items_per_row" id="items_per_row"  value="<?php echo isset($this->config->items_per_row) ? $this->config->items_per_row : ''; ?>" />
			</td>
		</tr>
		<tr>
			<td width="50%">
				<span class="required">*</span><?php echo JText::_('ESHOP_CONFIG_CATALOG_MODE'); ?>:<br />
				<span class="help"><?php echo JText::_('ESHOP_CONFIG_CATALOG_MODE_HELP'); ?></span>
			</td>
			<td>
				<?php echo $this->lists['catalog_mode']; ?>
			</td>
		</tr>
		<tr>
			<td width="50%">
				<span class="required">*</span><?php echo JText::_('ESHOP_CONFIG_QUOTE_CART_MODE'); ?>:<br />
				<span class="help"><?php echo JText::_('ESHOP_CONFIG_QUOTE_CART_MODE_HELP'); ?></span>
			</td>
			<td>
				<?php echo $this->lists['quote_cart_mode']; ?>
			</td>
		</tr>
		<tr>
			<td width="50%">
				<span class="required">*</span><?php echo JText::_('ESHOP_CONFIG_ADD_CATEGORY_PATH'); ?>:<br />
				<span class="help"><?php echo JText::_('ESHOP_CONFIG_ADD_CATEGORY_PATH_HELP'); ?></span>
			</td>
			<td>
				<?php echo $this->lists['add_category_path']; ?>
			</td>
		</tr>
		<?php
		if (version_compare(JVERSION, '3.0', 'ge') && JLanguageMultilang::isEnabled() && count(EshopHelper::getLanguages()) > 1)
		{
			$languages = $this->languages;
			for ($i = 0; $i < count($languages); $i++)
			{
				
				?>
				<tr>
					<td width="50%">
						<span class="required">*</span><?php echo JText::_('ESHOP_CONFIG_DEFAULT_MENU_ITEM') . ' (' . $languages[$i]->title . ')'; ?>:<br />
						<span class="help"><?php echo JText::_('ESHOP_CONFIG_DEFAULT_MENU_ITEM_HELP') . ' (' . $languages[$i]->title . ')'; ?></span>
					</td>
					<td>
						<?php echo $this->lists['default_menu_item_'.$languages[$i]->lang_code]; ?>
					</td>
				</tr>
				<?php
			}
		}
		else 
		{
			?>
			<tr>
				<td width="50%">
					<span class="required">*</span><?php echo JText::_('ESHOP_CONFIG_DEFAULT_MENU_ITEM'); ?>:<br />
					<span class="help"><?php echo JText::_('ESHOP_CONFIG_DEFAULT_MENU_ITEM_HELP'); ?></span>
				</td>
				<td>
					<?php echo $this->lists['default_menu_item']; ?>
				</td>
			</tr>
			<?php
		}
		?>
		<tr>
			<td class="key" colspan="2">
				<h2><?php echo JText::_('ESHOP_CONFIG_PRODUCTS'); ?></h2>
			</td>
		</tr>
		<tr>
			<td width="50%">
				<?php echo JText::_('ESHOP_CONFIG_CATEGORY_PRODUCT_COUNT'); ?>:<br />
				<span class="help"><?php echo JText::_('ESHOP_CONFIG_CATEGORY_PRODUCT_COUNT_HELP'); ?></span>
			</td>
			<td>
				<?php echo $this->lists['product_count']; ?>
			</td>
		</tr>
		<tr>
			<td width="50%">
				<?php echo JText::_('ESHOP_CONFIG_RICH_SNIPPETS'); ?>:<br />
				<span class="help"><?php echo JText::_('ESHOP_CONFIG_RICH_SNIPPETS_HELP'); ?></span>
			</td>
			<td>
				<?php echo $this->lists['rich_snippets']; ?>
			</td>
		</tr>
		<tr>
			<td width="50%">
				<?php echo JText::_('ESHOP_CONFIG_ALLOW_REVIEWS'); ?>:<br />
				<span class="help"><?php echo JText::_('ESHOP_CONFIG_ALLOW_REVIEWS_HELP'); ?></span>
			</td>
			<td>
				<?php echo $this->lists['allow_reviews']; ?>
			</td>
		</tr>
		<tr>
			<td width="50%">
				<?php echo JText::_('ESHOP_CONFIG_ENABLE_REVIEWS_CAPTCHA'); ?>:<br />
				<span class="help"><?php echo JText::_('ESHOP_CONFIG_ENABLE_REVIEWS_CAPTCHA_HELP'); ?></span>
			</td>
			<td>
				<?php echo $this->lists['enable_reviews_captcha']; ?>
			</td>
		</tr>
		<tr>
			<td width="50%">
				<?php echo JText::_('ESHOP_CONFIG_ENABLE_CHECKOUT_CAPTCHA'); ?>:<br />
				<span class="help"><?php echo JText::_('ESHOP_CONFIG_ENABLE_CHECKOUT_CAPTCHA_HELP'); ?></span>
			</td>
			<td>
				<?php echo $this->lists['enable_checkout_captcha']; ?>
			</td>
		</tr>
		<tr>
			<td width="50%">
				<?php echo JText::_('ESHOP_CONFIG_ENABLE_QUOTE_CAPTCHA'); ?>:<br />
				<span class="help"><?php echo JText::_('ESHOP_CONFIG_ENABLE_QUOTE_CAPTCHA_HELP'); ?></span>
			</td>
			<td>
				<?php echo $this->lists['enable_quote_captcha']; ?>
			</td>
		</tr>
		<tr>
			<td width="50%">
				<?php echo JText::_('ESHOP_CONFIG_ALLOW_WISHLIST'); ?>:<br />
				<span class="help"><?php echo JText::_('ESHOP_CONFIG_ALLOW_WISHLIST_HELP'); ?></span>
			</td>
			<td>
				<?php echo $this->lists['allow_wishlist']; ?>
			</td>
		</tr>
		<tr>
			<td width="50%">
				<?php echo JText::_('ESHOP_CONFIG_ALLOW_COMPARE'); ?>:<br />
				<span class="help"><?php echo JText::_('ESHOP_CONFIG_ALLOW_COMPARE_HELP'); ?></span>
			</td>
			<td>
				<?php echo $this->lists['allow_compare']; ?>
			</td>
		</tr>
		<tr>
			<td width="50%">
				<?php echo JText::_('ESHOP_CONFIG_ALLOW_ASK_QUESTION'); ?>:<br />
				<span class="help"><?php echo JText::_('ESHOP_CONFIG_ALLOW_ASK_QUESTION_HELP'); ?></span>
			</td>
			<td>
				<?php echo $this->lists['allow_ask_question']; ?>
			</td>
		</tr>
		<tr>
			<td width="50%">
				<?php echo JText::_('ESHOP_CONFIG_DYNAMIC_PRICE'); ?>:<br />
				<span class="help"><?php echo JText::_('ESHOP_CONFIG_DYNAMIC_PRICE_HELP'); ?></span>
			</td>
			<td>
				<?php echo $this->lists['dynamic_price']; ?>
			</td>
		</tr>
		<tr>
			<td width="50%">
				<?php echo JText::_('ESHOP_CONFIG_HIDE_OUT_OF_STOCK_PRODUCTS'); ?>:<br />
				<span class="help"><?php echo JText::_('ESHOP_CONFIG_HIDE_OUT_OF_STOCK_PRODUCTS_HELP'); ?></span>
			</td>
			<td>
				<?php echo $this->lists['hide_out_of_stock_products']; ?>
			</td>
		</tr>
		<tr>
			<td width="50%">
				<?php echo JText::_('ESHOP_CONFIG_DISPLAY_PRICE'); ?>:<br />
				<span class="help"><?php echo JText::_('ESHOP_CONFIG_DISPLAY_PRICE_HELP'); ?></span>
			</td>
			<td>
				<?php echo $this->lists['display_price']; ?>
			</td>
		</tr>
		<tr>
			<td class="key" colspan="2">
				<h2><?php echo JText::_('ESHOP_CONFIG_TAXES'); ?></h2>
			</td>
		</tr>
		<tr>
			<td width="50%">
				<?php echo JText::_('ESHOP_CONFIG_ENABLE_TAX'); ?>:
			</td>
			<td>
				<?php echo $this->lists['tax']; ?>
			</td>
		</tr>
		<tr>
			<td width="50%">
				<?php echo JText::_('ESHOP_CONFIG_DISPLAY_EX_TAX'); ?>:
			</td>
			<td>
				<?php echo $this->lists['display_ex_tax']; ?>
			</td>
		</tr>
		<tr>
			<td width="50%">
				<?php echo JText::_('ESHOP_CONFIG_USE_STORE_TAX_ADDRESS'); ?>:<br />
				<span class="help"><?php echo JText::_('ESHOP_CONFIG_USE_STORE_TAX_ADDRESS_HELP'); ?></span>
			</td>
			<td>
				<?php echo $this->lists['tax_default']; ?>
			</td>
		</tr>
		<tr>
			<td width="50%">
				<?php echo JText::_('ESHOP_CONFIG_USE_CUSTOMER_TAX_ADDRESS'); ?>:<br />
				<span class="help"><?php echo JText::_('ESHOP_CONFIG_USE_CUSTOMER_TAX_ADDRESS_HELP'); ?></span>
			</td>
			<td>
				<?php echo $this->lists['tax_customer']; ?>
			</td>
		</tr>
		<tr>
			<td class="key" colspan="2">
				<h2><?php echo JText::_('ESHOP_CONFIG_ACCOUNT'); ?></h2>
			</td>
		</tr>
		<tr>
			<td width="50%">
				<?php echo  JText::_('ESHOP_CONFIG_CUSTOMER_GROUP'); ?>:<br />
				<span class="help"><?php echo JText::_('ESHOP_CONFIG_CUSTOMER_GROUP_HELP'); ?></span>
			</td>
			<td>
				<?php echo $this->lists['customergroup_id']; ?>
			</td>
		</tr>
		<tr>
			<td width="50%">
				<?php echo  JText::_('ESHOP_CONFIG_CUSTOMER_GROUPS'); ?>:<br />
				<span class="help"><?php echo JText::_('ESHOP_CONFIG_CUSTOMER_GROUPS_HELP'); ?></span>
			</td>
			<td>
				<?php echo $this->lists['customer_group_display']; ?>
			</td>
		</tr>
		<tr>
			<td width="50%">
				<?php echo JText::_('ESHOP_CONFIG_ACCOUNT_TERMS'); ?>:<br />
				<span class="help"><?php echo JText::_('ESHOP_CONFIG_ACCOUNT_TERMS_HELP'); ?></span>
			</td>
			<td>
				<?php echo $this->lists['account_terms']; ?>
			</td>
		</tr>	
		<tr>
			<td class="key" colspan="2">
				<h2><?php echo JText::_('ESHOP_CONFIG_CHECKOUT'); ?></h2>
			</td>
		</tr>
		<tr>
			<td width="50%">
				<?php echo JText::_('ESHOP_CONFIG_MIN_SUB_TOTAL'); ?>:<br />
				<span class="help"><?php echo JText::_('ESHOP_CONFIG_MIN_SUB_TOTAL_HELP'); ?></span>
			</td>
			<td>
				<input class="text_area" type="text" name="min_sub_total" id="min_sub_total" size="3" value="<?php echo isset($this->config->min_sub_total) ? $this->config->min_sub_total : ''; ?>" />
			</td>
		</tr>
		<tr>
			<td width="50%">
				<?php echo JText::_('ESHOP_CONFIG_MIN_QUANTITY'); ?>:<br />
				<span class="help"><?php echo JText::_('ESHOP_CONFIG_MIN_QUANTITY_HELP'); ?></span>
			</td>
			<td>
				<input class="text_area" type="text" name="min_quantity" id="min_quantity" size="3" value="<?php echo isset($this->config->min_quantity) ? $this->config->min_quantity : '0'; ?>" />
			</td>
		</tr>
		<tr>
			<td width="50%">
				<?php echo JText::_('ESHOP_CONFIG_ACTIVE_HTTPS'); ?>:<br />
				<span class="help"><?php echo JText::_('ESHOP_CONFIG_ACTIVE_HTTPS_HELP'); ?></span>
			</td>
			<td>
				<?php echo $this->lists['active_https']; ?>
			</td>
		</tr>
		<tr>
			<td width="50%">
				<?php echo JText::_('ESHOP_CONFIG_ALLOW_RE_ORDER'); ?>:<br />
				<span class="help"><?php echo JText::_('ESHOP_CONFIG_ALLOW_RE_ORDER_HELP'); ?></span>
			</td>
			<td>
				<?php echo $this->lists['allow_re_order']; ?>
			</td>
		</tr>
		<tr>
			<td width="50%">
				<?php echo JText::_('ESHOP_CONFIG_ALLOW_COUPON'); ?>:<br />
				<span class="help"><?php echo JText::_('ESHOP_CONFIG_ALLOW_COUPON_HELP'); ?></span>
			</td>
			<td>
				<?php echo $this->lists['allow_coupon']; ?>
			</td>
		</tr>
		<tr>
			<td width="50%">
				<?php echo JText::_('ESHOP_CONFIG_ALLOW_VOUCHER'); ?>:<br />
				<span class="help"><?php echo JText::_('ESHOP_CONFIG_ALLOW_VOUCHER_HELP'); ?></span>
			</td>
			<td>
				<?php echo $this->lists['allow_voucher']; ?>
			</td>
		</tr>
		<tr>
			<td width="50%">
				<?php echo JText::_('ESHOP_CONFIG_DISPLAY_WEIGHT_ON_CART_PAGE'); ?>:<br />
				<span class="help"><?php echo JText::_('ESHOP_CONFIG_DISPLAY_WEIGHT_ON_CART_PAGE_HELP'); ?></span>
			</td>
			<td>
				<?php echo $this->lists['cart_weight']; ?>
			</td>
		</tr>
		<tr>
			<td width="50%">
				<?php echo JText::_('ESHOP_CONFIG_SHIPPING_ESTIMATE'); ?>:<br />
				<span class="help"><?php echo JText::_('ESHOP_CONFIG_SHIPPING_ESTIMATE_HELP'); ?></span>
			</td>
			<td>
				<?php echo $this->lists['shipping_estimate']; ?>
			</td>
		</tr>
		<tr>
			<td width="50%">
				<?php echo JText::_('ESHOP_CONFIG_CHECKOUT_TYPE'); ?>:<br />
				<span class="help"><?php echo JText::_('ESHOP_CONFIG_CHECKOUT_TYPE_HELP'); ?></span>
			</td>
			<td>
				<?php echo $this->lists['checkout_type']; ?>
			</td>
		</tr>
		<tr>
			<td width="50%">
				<?php echo JText::_('ESHOP_CONFIG_CHECKOUT_TERMS'); ?>:<br />
				<span class="help"><?php echo JText::_('ESHOP_CONFIG_CHECKOUT_TERMS_HELP'); ?></span>
			</td>
			<td>
				<?php echo $this->lists['checkout_terms']; ?>
			</td>
		</tr>
		<tr>
			<td width="50%">
				<?php echo JText::_('ESHOP_CONFIG_ORDER_EDITING'); ?>:<br />
				<span class="help"><?php echo JText::_('ESHOP_CONFIG_ORDER_EDITING_HELP'); ?></span>
			</td>
			<td>
				<input class="text_area" type="text" name="order_edit" id="order_edit" size="3" value="<?php echo isset($this->config->order_edit) ? $this->config->order_edit : ''; ?>" />
			</td>
		</tr>
		<tr>
			<td width="50%">
				<?php echo JText::_('ESHOP_CONFIG_ORDER_STATUS'); ?>:<br />
				<span class="help"><?php echo JText::_('ESHOP_CONFIG_ORDER_STATUS_HELP'); ?></span>
			</td>
			<td>
				<?php echo $this->lists['order_status_id']; ?>
			</td>
		</tr>
		<tr>
			<td width="50%">
				<?php echo JText::_('ESHOP_CONFIG_COMPLETE_ORDER_STATUS'); ?>:<br />
				<span class="help"><?php echo JText::_('ESHOP_CONFIG_COMPLETE_ORDER_STATUS_HELP'); ?></span>
			</td>
			<td>
				<?php echo $this->lists['complete_status_id']; ?>
			</td>
		</tr>
		<tr>
			<td width="50%">
				<?php echo JText::_('ESHOP_CONFIG_CANCELED_ORDER_STATUS'); ?>:<br />
				<span class="help"><?php echo JText::_('ESHOP_CONFIG_CANCELED_ORDER_STATUS_HELP'); ?></span>
			</td>
			<td>
				<?php echo $this->lists['canceled_status_id']; ?>
			</td>
		</tr>
		<tr>
			<td width="50%">
				<?php echo JText::_('ESHOP_CONFIG_DELIVERY_DATE'); ?>:<br />
				<span class="help"><?php echo JText::_('ESHOP_CONFIG_DELIVERY_DATE_HELP'); ?></span>
			</td>
			<td>
				<?php echo $this->lists['delivery_date']; ?>
			</td>
		</tr>
		<tr>
			<td width="50%">
				<?php echo JText::_('ESHOP_CONFIG_COMPLETED_URL'); ?>:<br />
				<span class="help"><?php echo JText::_('ESHOP_CONFIG_COMPLETED_URL_HELP'); ?></span>
			</td>
			<td>
				<input class="input-xxlarge" type="text" name="completed_url" id="completed_url"  value="<?php echo isset($this->config->completed_url) ? $this->config->completed_url : ''; ?>" />
			</td>
		</tr>
		<tr>
			<td width="50%">
				<?php echo JText::_('ESHOP_CONFIG_IDEVAFFILIATE_INTEGRATION'); ?>:<br />
				<span class="help"><?php echo JText::_('ESHOP_CONFIG_IDEVAFFILIATE_INTEGRATION_HELP'); ?></span>
			</td>
			<td>
				<?php echo $this->lists['idevaffiliate_integration']; ?>
			</td>
		</tr>
		<tr>
			<td width="50%">
				<?php echo JText::_('ESHOP_CONFIG_IDEVAFFILIATE_PATH'); ?>:<br />
				<span class="help"><?php echo JText::_('ESHOP_CONFIG_IDEVAFFILIATE_PATH_HELP'); ?></span>
			</td>
			<td>
				<input class="input-xxlarge" type="text" name="idevaffiliate_path" id="idevaffiliate_path"  value="<?php echo isset($this->config->idevaffiliate_path) ? $this->config->idevaffiliate_path : ''; ?>" />
			</td>
		</tr>
		<tr>
			<td class="key" colspan="2">
				<h2><?php echo JText::_('ESHOP_CONFIG_STOCK'); ?></h2>
			</td>
		</tr>
		<tr>
			<td width="50%">
				<?php echo JText::_('ESHOP_CONFIG_DISPLAY_STOCK'); ?>:<br />
				<span class="help"><?php echo JText::_('ESHOP_CONFIG_DISPLAY_STOCK_HELP'); ?></span>
			</td>
			<td>
				<?php echo $this->lists['stock_display']; ?>
			</td>
		</tr>	
		<tr>
			<td width="50%">
				<?php echo JText::_('ESHOP_CONFIG_STOCK_WARNING'); ?>:<br />
				<span class="help"><?php echo JText::_('ESHOP_CONFIG_STOCK_WARNING_HELP'); ?></span>
			</td>
			<td>
				<?php echo $this->lists['stock_warning']; ?>
			</td>
		</tr>	
		<tr>
			<td width="50%">
				<?php echo JText::_('ESHOP_CONFIG_STOCK_CHECKOUT'); ?>:<br />
				<span class="help"><?php echo JText::_('ESHOP_CONFIG_STOCK_CHECKOUT_HELP'); ?></span>
			</td>
			<td>
				<?php echo $this->lists['stock_checkout']; ?>
			</td>
		</tr>
		<tr>
			<td width="50%">
				<?php echo JText::_('ESHOP_CONFIG_STOCK_STATUS'); ?>:<br />
				<span class="help"><?php echo JText::_('ESHOP_CONFIG_STOCK_STATUS_HELP'); ?></span>
			</td>
			<td>
				<?php echo $this->lists['stock_status_id']; ?>
			</td>
		</tr>
		<tr>
			<td class="key" colspan="2">
				<h2><?php echo JText::_('ESHOP_CONFIG_FILE'); ?></h2>
			</td>
		</tr>
		<tr>
			<td width="50%">
				<?php echo JText::_('ESHOP_CONFIG_FILE_EXTENSIONS_ALLOWED'); ?>:<br />
				<span class="help"><?php echo JText::_('ESHOP_CONFIG_FILE_EXTENSIONS_ALLOWED_HELP'); ?></span>
			</td>
			<td>
				<textarea name="file_extensions_allowed" id="file_extensions_allowed" rows="5" cols="50"><?php echo isset($this->config->file_extensions_allowed) ? $this->config->file_extensions_allowed : ''; ?></textarea>
			</td>
		</tr>
		<tr>
			<td width="50%">
				<?php echo JText::_('ESHOP_CONFIG_FILE_MIME_TYPES_ALLOWED'); ?>:<br />
				<span class="help"><?php echo JText::_('ESHOP_CONFIG_FILE_MIME_TYPES_ALLOWED_HELP'); ?></span>
			</td>
			<td>
				<textarea name="file_mime_types_allowed" id="file_mime_types_allowed" rows="5" cols="50"><?php echo isset($this->config->file_mime_types_allowed) ? $this->config->file_mime_types_allowed : ''; ?></textarea>
			</td>
		</tr>
		<tr>
			<td class="key" colspan="2">
				<h2><?php echo JText::_('ESHOP_CONFIG_CHECKOUT_DONATE'); ?></h2>
			</td>
		</tr>
		<tr>
			<td width="50%">
				<?php echo JText::_('ESHOP_CONFIG_CHECKOUT_DONATE_ENABLE'); ?>:
			</td>
			<td>
				<?php echo $this->lists['enable_checkout_donate']; ?>
			</td>
		</tr>
		<tr>
			<td width="50%">
				<?php echo JText::_('ESHOP_CONFIG_CHECKOUT_DONATE_AMOUNTS'); ?>:<br />
				<span class="help"><?php echo JText::_('ESHOP_CONFIG_CHECKOUT_DONATE_AMOUNTS_HELP'); ?></span>
			</td>
			<td>
				<textarea name="donate_amounts" id="donate_amounts" rows="5" cols="50"><?php echo isset($this->config->donate_amounts) ? $this->config->donate_amounts : ''; ?></textarea>
			</td>
		</tr>
		<tr>
			<td width="50%">
				<?php echo JText::_('ESHOP_CONFIG_CHECKOUT_DONATE_EXPLANATIONS'); ?>:<br />
				<span class="help"><?php echo JText::_('ESHOP_CONFIG_CHECKOUT_DONATE_EXPLANATIONS_HELP'); ?></span>
			</td>
			<td>
				<textarea name="donate_explanations" id="donate_explanations" rows="5" cols="50"><?php echo isset($this->config->donate_explanations) ? $this->config->donate_explanations : ''; ?></textarea>
			</td>
		</tr>
</table>