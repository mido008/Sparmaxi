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
defined('_JEXEC') or die();

/**
 * HTML View class for EShop component
 *
 * @static
 * @package		Joomla
 * @subpackage	EShop
 * @since 1.5
 */
class EShopViewConfiguration extends JViewLegacy
{

	function display($tpl = null)
	{
		// Check access first
		$mainframe = JFactory::getApplication();
		if (!JFactory::getUser()->authorise('eshop.configuration', 'com_eshop'))
		{
			$mainframe->enqueueMessage(JText::_('ESHOP_ACCESS_NOT_ALLOW'), 'error');
			$mainframe->redirect('index.php?option=com_eshop&view=dashboard');
		}
		$config = $this->get('Data');
		$db = JFactory::getDbo();
		
		// Introduction display list
		$options = array();
		$options[] = JHtml::_('select.option', 'front_page', JText::_('Front Page'));
		$options[] = JHtml::_('select.option', 'categories_page', JText::_('Categories Page'));
		$lists['introduction_display_on'] = JHtml::_('select.genericlist', $options, 'introduction_display_on', ' class="inputbox" ', 'value', 'text', isset($config->introduction_display_on) ? $config->introduction_display_on : 'frontpage');
		
		//Country list
		$query = $db->getQuery(true);
		$query->select('id, country_name AS name')
			->from('#__eshop_countries')
			->where('published = 1')
			->order('country_name');
		$db->setQuery($query);
		$options = array();
		$options[] = JHtml::_('select.option', 0, JText::_('ESHOP_NONE'), 'id', 'name');
		$options = array_merge($options, $db->loadObjectList());
		$lists['country_id'] = JHtml::_('select.genericlist', $options, 'country_id', ' class="inputbox" onchange="Eshop.updateStateList(this.value, \'zone_id\')" ', 'id', 'name', isset($config->country_id) ? $config->country_id : '0');
		
		//Zone list
		$query->clear();
		$query->select('id, zone_name')
			->from('#__eshop_zones')
			->where('country_id = ' . intval(isset($config->country_id) ? $config->country_id : '0'))
			->where('published = 1');
		$db->setQuery($query);
		$options = array();
		$options[] = JHtml::_('select.option', 0, JText::_('ESHOP_NONE'), 'id', 'zone_name');
		$options = array_merge($options, $db->loadObjectList());
		$lists['zone_id'] = JHtml::_('select.genericlist', $options, 'zone_id', ' class="inputbox" ', 'id', 'zone_name', isset($config->zone_id) ? $config->zone_id : '0');
		
		//Currencies list
		$query->clear();
		$query->select('currency_code, currency_name')
			->from('#__eshop_currencies')
			->where('published = 1');
		$db->setQuery($query);
		$lists['default_currency_code'] = JHtml::_('select.genericlist', $db->loadObjectList(), 'default_currency_code', ' class="inputbox" ', 'currency_code', 'currency_name', isset($config->default_currency_code)? $config->default_currency_code : 'USD');
		
		//Lengths list
		$query->clear();
		$query->select('a.id, b.length_name')
			->from('#__eshop_lengths AS a')
			->innerJoin('#__eshop_lengthdetails AS b ON (a.id = b.length_id)')
			->where('a.published = 1')
			->where('b.language = "' . JComponentHelper::getParams('com_languages')->get('site', 'en-GB') . '"');
		$db->setQuery($query);
		$lists['length_id'] = JHtml::_('select.genericlist', $db->loadObjectList(), 'length_id', ' class="inputbox" ', 'id', 'length_name', isset($config->length_id) ? $config->length_id : '1');
		
		//Weights list
		$query->clear();
		$query->select('a.id, b.weight_name')
			->from('#__eshop_weights AS a')
			->innerJoin('#__eshop_weightdetails AS b ON (a.id = b.weight_id)')
			->where('a.published = 1')
			->where('b.language = "' . JComponentHelper::getParams('com_languages')->get('site', 'en-GB') . '"');
		$db->setQuery($query);
		$lists['weight_id'] = JHtml::_('select.genericlist', $db->loadObjectList(), 'weight_id', ' class="inputbox" ', 'id', 'weight_name', isset($config->weight_id) ? $config->weight_id : '1');
		
		//Customer group list
		$query->clear();
		$query->select('a.id, b.customergroup_name AS name')
			->from('#__eshop_customergroups AS a')
			->innerJoin('#__eshop_customergroupdetails AS b ON (a.id = b.customergroup_id)')
			->where('a.published = 1')
			->where('b.language = "' . JComponentHelper::getParams('com_languages')->get('site', 'en-GB') . '"')
			->order('b.customergroup_name');
		$db->setQuery($query);
		$options = array();
		$options[] = JHtml::_('select.option', 0, JText::_('ESHOP_NONE'), 'id', 'name');
		$options = array_merge($options, $db->loadObjectList());
		$lists['customergroup_id'] = JHtml::_('select.genericlist', $options, 'customergroup_id', ' class="inputbox" ', 'id', 'name', isset($config->customergroup_id) ? $config->customergroup_id : '1');
		
		//Customer group display list
		$customerGroupDisplay = explode(',', isset($config->customer_group_display) ? $config->customer_group_display : '');
		$lists['customer_group_display'] = JHtml::_('select.genericlist', $db->loadObjectList(), 'customer_group_display[]', ' class="inputbox chosen" multiple ', 'id', 'name', $customerGroupDisplay);
		
		//Stock status list
		$query->clear();
		$query->select('a.id, b.stockstatus_name')
			->from('#__eshop_stockstatuses AS a')
			->innerJoin('#__eshop_stockstatusdetails AS b ON (a.id = b.stockstatus_id)')
			->where('a.published = 1')
			->where('b.language = "' . JComponentHelper::getParams('com_languages')->get('site', 'en-GB') . '"');
		$db->setQuery($query);
		$lists['stock_status_id'] = JHtml::_('select.genericlist', $db->loadObjectList(), 'stock_status_id', ' class="inputbox" ', 'id', 'stockstatus_name', isset($config->stock_status_id) ? $config->stock_status_id : '1');
		
		//Order status and complete status list
		$query->clear();
		$query->select('a.id, b.orderstatus_name')
			->from('#__eshop_orderstatuses AS a')
			->innerJoin('#__eshop_orderstatusdetails AS b ON (a.id = b.orderstatus_id)')
			->where('a.published = 1')
			->where('b.language = "' . JComponentHelper::getParams('com_languages')->get('site', 'en-GB') . '"');
		$db->setQuery($query);
		$lists['order_status_id'] = JHtml::_('select.genericlist', $db->loadObjectList(), 'order_status_id', ' class="inputbox" ', 'id', 'orderstatus_name', isset($config->order_status_id) ? $config->order_status_id : '1');
		$lists['complete_status_id'] = JHtml::_('select.genericlist', $db->loadObjectList(), 'complete_status_id', ' class="inputbox" ', 'id', 'orderstatus_name', isset($config->complete_status_id) ? $config->complete_status_id : '1');
		$lists['canceled_status_id'] = JHtml::_('select.genericlist', $db->loadObjectList(), 'canceled_status_id', ' class="inputbox" ', 'id', 'orderstatus_name', isset($config->canceled_status_id) ? $config->canceled_status_id : '1');
		$lists['delivery_date']			= JHtml::_('select.booleanlist', 'delivery_date', ' class="inputbox" ', isset($config->delivery_date) ? $config->delivery_date : '0');
		$lists['idevaffiliate_integration']			= JHtml::_('select.booleanlist', 'idevaffiliate_integration', ' class="inputbox" ', isset($config->idevaffiliate_integration) ? $config->idevaffiliate_integration : '0');

		//Tax default
		$options = array();
		$options[] = JHtml::_('select.option', '', JText::_('ESHOP_NONE'));
		$options[] = JHtml::_('select.option', 'shipping', JText::_('Shipping Address'));
		$options[] = JHtml::_('select.option', 'payment', JText::_('Payment Address'));
		$lists['tax_default'] = JHtml::_('select.genericlist', $options, 'tax_default', ' class="inputbox" ', 'value', 'text', isset($config->tax_default) ? $config->tax_default : '');
		
		//Tax customer
		$options = array();
		$options[] = JHtml::_('select.option', '', JText::_('ESHOP_NONE'));
		$options[] = JHtml::_('select.option', 'shipping', JText::_('Shipping Address'));
		$options[] = JHtml::_('select.option', 'payment', JText::_('Payment Address'));
		$lists['tax_customer'] = JHtml::_('select.genericlist', $options, 'tax_customer', ' class="inputbox" ', 'value', 'text', isset($config->tax_customer) ? $config->tax_customer : '');
		
		//Account terms and Checkout terms
		$query->clear();
		$query->select('id, title')
			->from('#__content')
			->where('state = 1');
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		$options = array();
		$options[] = JHtml::_('select.option', 0, JText::_('ESHOP_NONE'), 'id', 'title');
		$options = array_merge($options, $rows);
		$lists['account_terms'] = JHtml::_('select.genericlist', $options, 'account_terms', ' class="inputbox" ', 'id', 'title', isset($config->account_terms) ? $config->account_terms : '0');
		$lists['checkout_terms'] = JHtml::_('select.genericlist', $options, 'checkout_terms', ' class="inputbox" ', 'id', 'title', isset($config->checkout_terms) ? $config->checkout_terms : '0');
		
		//Themes list
		$query->clear();
			$query->select('name AS value, title AS text')
				->from('#__eshop_themes')
				->where('published = 1');
			$db->setQuery($query);
			$rows = $db->loadObjectList();
		$lists['theme'] = JHtml::_('select.genericlist', $rows, 'theme', ' class="inputbox" ', 'value', 'text', isset($config->theme) ? $config->theme : 'default');
		
		//Other boolean list
		$lists['auto_update_currency']	= JHtml::_('select.booleanlist', 'auto_update_currency', ' class="inputbox" ', isset($config->auto_update_currency) ? $config->auto_update_currency : '0');
		$lists['product_count']			= JHtml::_('select.booleanlist', 'product_count', ' class="inputbox" ', isset($config->product_count) ? $config->product_count : '0');
		$lists['rich_snippets']			= JHtml::_('select.booleanlist', 'rich_snippets', ' class="inputbox" ', isset($config->rich_snippets) ? $config->rich_snippets : '0');
		$lists['allow_reviews']			= JHtml::_('select.booleanlist', 'allow_reviews', ' class="inputbox" ', isset($config->allow_reviews) ? $config->allow_reviews : '1');
		$lists['enable_reviews_captcha']	= JHtml::_('select.booleanlist', 'enable_reviews_captcha', ' class="inputbox" ', isset($config->enable_reviews_captcha) ? $config->enable_reviews_captcha : '1');
		$lists['enable_checkout_captcha']	= JHtml::_('select.booleanlist', 'enable_checkout_captcha', ' class="inputbox" ', isset($config->enable_checkout_captcha) ? $config->enable_checkout_captcha : '1');
		$lists['enable_quote_captcha']	= JHtml::_('select.booleanlist', 'enable_quote_captcha', ' class="inputbox" ', isset($config->enable_quote_captcha) ? $config->enable_quote_captcha : '1');
		$lists['allow_wishlist']		= JHtml::_('select.booleanlist', 'allow_wishlist', ' class="inputbox" ', isset($config->allow_wishlist) ? $config->allow_wishlist : '1');
		$lists['allow_compare']			= JHtml::_('select.booleanlist', 'allow_compare', ' class="inputbox" ', isset($config->allow_compare) ? $config->allow_compare : '1');
		$lists['allow_ask_question']	= JHtml::_('select.booleanlist', 'allow_ask_question', ' class="inputbox" ', isset($config->allow_ask_question) ? $config->allow_ask_question : '1');
		$lists['dynamic_price']			= JHtml::_('select.booleanlist', 'dynamic_price', ' class="inputbox" ', isset($config->dynamic_price) ? $config->dynamic_price : '1');
		$lists['hide_out_of_stock_products'] = JHtml::_('select.booleanlist', 'hide_out_of_stock_products', ' class="inputbox" ', isset($config->hide_out_of_stock_products) ? $config->hide_out_of_stock_products : '0');
		$options = array();
		$options[] = JHtml::_('select.option', 'public', 'Public');
		$options[] = JHtml::_('select.option', 'registered', 'Only Reigstered Users');
		$options[] = JHtml::_('select.option', 'hide', 'Hide');
		$lists['display_price'] 		= JHtml::_('select.genericlist', $options, 'display_price', ' class="inputbox" ', 'value', 'text', isset($config->display_price) ? $config->display_price : 'public');
		$lists['tax']					= JHtml::_('select.booleanlist', 'tax', ' class="inputbox" ', isset($config->tax) ? $config->tax : '1');
		$lists['display_ex_tax']		= JHtml::_('select.booleanlist', 'display_ex_tax', ' class="inputbox" ', isset($config->display_ex_tax) ? $config->display_ex_tax : '1');
		$lists['catalog_mode']			= JHtml::_('select.booleanlist', 'catalog_mode', ' class="inputbox" ', isset($config->catalog_mode) ? $config->catalog_mode : '0');
		$lists['quote_cart_mode']		= JHtml::_('select.booleanlist', 'quote_cart_mode', ' class="inputbox" ', isset($config->quote_cart_mode) ? $config->quote_cart_mode : '0');
		$lists['order_alert_mail']		= JHtml::_('select.booleanlist', 'order_alert_mail', ' class="inputbox" ', isset($config->order_alert_mail) ? $config->order_alert_mail : '1');
		$lists['cart_weight']			= JHtml::_('select.booleanlist', 'cart_weight', ' class="inputbox" ', isset($config->cart_weight) ? $config->cart_weight : '1');
		$lists['shipping_estimate']		= JHtml::_('select.booleanlist', 'shipping_estimate', ' class="inputbox" ', isset($config->shipping_estimate) ? $config->shipping_estimate : '1');
		$lists['active_https']			= JHtml::_('select.booleanlist', 'active_https', ' class="inputbox" ', isset($config->active_https) ? $config->active_https : '0');
		$lists['allow_re_order']		= JHtml::_('select.booleanlist', 'allow_re_order', ' class="inputbox" ', isset($config->allow_re_order) ? $config->allow_re_order : '0');
		$lists['allow_coupon']			= JHtml::_('select.booleanlist', 'allow_coupon', ' class="inputbox" ', isset($config->allow_coupon) ? $config->allow_coupon : '1');
		$lists['allow_voucher']			= JHtml::_('select.booleanlist', 'allow_voucher', ' class="inputbox" ', isset($config->allow_voucher) ? $config->allow_voucher : '1');
		$options = array();
		$options[] = JHtml::_('select.option', 'guest_and_registered', 'Guest and Registered User');
		$options[] = JHtml::_('select.option', 'guest_only', 'Guest Only');
		$options[] = JHtml::_('select.option', 'registered_only', 'Registered User Only');
		$lists['checkout_type'] 				= JHtml::_('select.genericlist', $options, 'checkout_type', ' class="inputbox" ', 'value', 'text', isset($config->checkout_type) ? $config->checkout_type : 'guest_and_registered');
		$lists['stock_display']					= JHtml::_('select.booleanlist', 'stock_display', ' class="inputbox" ', isset($config->stock_display) ? $config->stock_display : '1');
		$lists['stock_warning']					= JHtml::_('select.booleanlist', 'stock_warning', ' class="inputbox" ', isset($config->stock_warning) ? $config->stock_warning : '1');
		$lists['stock_checkout']				= JHtml::_('select.booleanlist', 'stock_checkout', ' class="inputbox" ', isset($config->stock_checkout) ? $config->stock_checkout : '0');
		$lists['enable_checkout_donate'] 		= JHtml::_('select.booleanlist', 'enable_checkout_donate', ' class="inputbox" ', isset($config->enable_checkout_donate) ? $config->enable_checkout_donate : '0');
		$lists['load_bootstrap_css']			= JHtml::_('select.booleanlist', 'load_bootstrap_css', ' class="inputbox" ', isset($config->load_bootstrap_css) ? $config->load_bootstrap_css : '1');
		$lists['load_bootstrap_js']				= JHtml::_('select.booleanlist', 'load_bootstrap_js', ' class="inputbox" ', isset($config->load_bootstrap_js) ? $config->load_bootstrap_js : '1');
		$lists['show_categories_nav']			= JHtml::_('select.booleanlist', 'show_categories_nav', ' class="inputbox" ', isset($config->show_categories_nav) ? $config->show_categories_nav : '1');
		$lists['show_products_nav']				= JHtml::_('select.booleanlist', 'show_products_nav', ' class="inputbox" ', isset($config->show_products_nav) ? $config->show_products_nav : '1');
		$lists['show_manufacturer']				= JHtml::_('select.booleanlist', 'show_manufacturer', ' class="inputbox" ', isset($config->show_manufacturer) ? $config->show_manufacturer : '1');
		$lists['show_availability']				= JHtml::_('select.booleanlist', 'show_availability', ' class="inputbox" ', isset($config->show_availability) ? $config->show_availability : '1');
		$lists['show_product_weight']			= JHtml::_('select.booleanlist', 'show_product_weight', ' class="inputbox" ', isset($config->show_product_weight) ? $config->show_product_weight : '1');
		$lists['show_product_dimensions']		= JHtml::_('select.booleanlist', 'show_product_dimensions', ' class="inputbox" ', isset($config->show_product_dimensions) ? $config->show_product_dimensions : '1');
		$lists['show_product_tags']				= JHtml::_('select.booleanlist', 'show_product_tags', ' class="inputbox" ', isset($config->show_product_tags) ? $config->show_product_tags : '1');
		$lists['show_sku']						= JHtml::_('select.booleanlist', 'show_sku', ' class="inputbox" ', isset($config->show_sku) ? $config->show_sku : '1');
		$lists['show_specification']			= JHtml::_('select.booleanlist', 'show_specification', ' class="inputbox" ', isset($config->show_specification) ? $config->show_specification : '1');
		$lists['show_related_products']			= JHtml::_('select.booleanlist', 'show_related_products', ' class="inputbox" ', isset($config->show_related_products) ? $config->show_related_products : '1');
		$lists['show_category_image']			= JHtml::_('select.booleanlist', 'show_category_image', ' class="inputbox" ', isset($config->show_category_image) ? $config->show_category_image : '1');
		$lists['show_category_desc']			= JHtml::_('select.booleanlist', 'show_category_desc', ' class="inputbox" ', isset($config->show_category_desc) ? $config->show_category_desc : '1');
		$lists['show_products_in_all_levels']	= JHtml::_('select.booleanlist', 'show_products_in_all_levels', ' class="inputbox" ', isset($config->show_products_in_all_levels) ? $config->show_products_in_all_levels : '1');
		$lists['show_sub_categories']	= JHtml::_('select.booleanlist', 'show_sub_categories', ' class="inputbox" ', isset($config->show_sub_categories) ? $config->show_sub_categories : '1');
		$options = array();
		$options[] = JHtml::_('select.option', 'list', 'List');
		$options[] = JHtml::_('select.option', 'grid', 'Grid');
		$lists['default_products_layout'] 		= JHtml::_('select.genericlist', $options, 'default_products_layout', ' class="inputbox" ', 'value', 'text', isset($config->default_products_layout) ? $config->default_products_layout : 'grid');
		$options = array();
		$options[] = JHtml::_('select.option', 'list_with_only_link', 'List with only link');
		$options[] = JHtml::_('select.option', 'list_with_image', 'List with image and link');
		$lists['sub_categories_layout'] 		= JHtml::_('select.genericlist', $options, 'sub_categories_layout', ' class="inputbox" ', 'value', 'text', isset($config->sub_categories_layout) ? $config->sub_categories_layout : 'list_with_image');
		$lists['show_quantity_box']	= JHtml::_('select.booleanlist', 'show_quantity_box', ' class="inputbox" ', isset($config->show_quantity_box) ? $config->show_quantity_box : '1');
		$lists['show_quantity_box_in_product_page']	= JHtml::_('select.booleanlist', 'show_quantity_box_in_product_page', ' class="inputbox" ', isset($config->show_quantity_box_in_product_page) ? $config->show_quantity_box_in_product_page : '1');

		$lists['invoice_enable']				= JHtml::_('select.booleanlist', 'invoice_enable', ' class="inputbox" ', isset($config->invoice_enable) ? $config->invoice_enable : '1');
		$lists['send_invoice_to_customer']		= JHtml::_('select.booleanlist', 'send_invoice_to_customer', ' class="inputbox" ', isset($config->send_invoice_to_customer) ? $config->send_invoice_to_customer : '1');
		$lists['reset_invoice_number']			= JHtml::_('select.booleanlist', 'reset_invoice_number', ' class="inputbox" ', isset($config->reset_invoice_number) ? $config->reset_invoice_number : '1');
		
		//Compare options
		$lists['compare_image']				= JHtml::_('select.booleanlist', 'compare_image', ' class="inputbox" ', isset($config->compare_image) ? $config->compare_image : '1');
		$lists['compare_price']				= JHtml::_('select.booleanlist', 'compare_price', ' class="inputbox" ', isset($config->compare_price) ? $config->compare_price : '1');
		$lists['compare_sku']				= JHtml::_('select.booleanlist', 'compare_sku', ' class="inputbox" ', isset($config->compare_sku) ? $config->compare_sku : '1');
		$lists['compare_manufacturer']		= JHtml::_('select.booleanlist', 'compare_manufacturer', ' class="inputbox" ', isset($config->compare_manufacturer) ? $config->compare_manufacturer : '1');
		$lists['compare_availability']		= JHtml::_('select.booleanlist', 'compare_availability', ' class="inputbox" ', isset($config->compare_availability) ? $config->compare_availability : '1');
		$lists['compare_rating']			= JHtml::_('select.booleanlist', 'compare_rating', ' class="inputbox" ', isset($config->compare_rating) ? $config->compare_rating : '1');
		$lists['compare_short_desc']		= JHtml::_('select.booleanlist', 'compare_short_desc', ' class="inputbox" ', isset($config->compare_short_desc) ? $config->compare_short_desc : '1');
		$lists['compare_desc']				= JHtml::_('select.booleanlist', 'compare_desc', ' class="inputbox" ', isset($config->compare_desc) ? $config->compare_desc : '1');
		$lists['compare_weight']			= JHtml::_('select.booleanlist', 'compare_weight', ' class="inputbox" ', isset($config->compare_weight) ? $config->compare_weight : '1');
		$lists['compare_dimensions']		= JHtml::_('select.booleanlist', 'compare_dimensions', ' class="inputbox" ', isset($config->compare_dimensions) ? $config->compare_dimensions : '1');
		$lists['compare_attributes']		= JHtml::_('select.booleanlist', 'compare_attributes', ' class="inputbox" ', isset($config->compare_attributes) ? $config->compare_attributes : '1');
		
		//Sort options list
		$sortOptions = isset($config->sort_options) ? $config->sort_options : 'b.product_name-ASC';
		$sortOptions = explode(',', $sortOptions);
		$sortValues = array (
			'a.ordering-ASC',
			'a.ordering-DESC',
			'b.product_name-ASC',
			'b.product_name-DESC',
			'a.product_sku-ASC',
			'a.product_sku-DESC',
			'a.product_price-ASC',
			'a.product_price-DESC',
			'a.product_length-ASC',
			'a.product_length-DESC',
			'a.product_width-ASC',
			'a.product_width-DESC',
			'a.product_height-ASC',
			'a.product_height-DESC',
			'a.product_weight-ASC',
			'a.product_weight-DESC',
			'a.product_quantity-ASC',
			'a.product_quantity-DESC',
			'b.product_short_desc-ASC',
			'b.product_short_desc-DESC',
			'b.product_desc-ASC',
			'b.product_desc-DESC',
			'product_rates-ASC',
			'product_rates-DESC',
			'product_reviews-ASC',
			'product_reviews-DESC'
		 );
		$sortTexts = array (
			JText::_('ESHOP_CONFIG_SORTING_PRODUCT_ORDERING_ASC'),
			JText::_('ESHOP_CONFIG_SORTING_PRODUCT_ORDERING_DESC'),
			JText::_('ESHOP_CONFIG_SORTING_PRODUCT_NAME_ASC'),
			JText::_('ESHOP_CONFIG_SORTING_PRODUCT_NAME_DESC'),
			JText::_('ESHOP_CONFIG_SORTING_PRODUCT_SKU_ASC'),
			JText::_('ESHOP_CONFIG_SORTING_PRODUCT_SKU_DESC'),
			JText::_('ESHOP_CONFIG_SORTING_PRODUCT_PRICE_ASC'),
			JText::_('ESHOP_CONFIG_SORTING_PRODUCT_PRICE_DESC'),
			JText::_('ESHOP_CONFIG_SORTING_PRODUCT_LENGTH_ASC'),
			JText::_('ESHOP_CONFIG_SORTING_PRODUCT_LENGTH_DESC'),
			JText::_('ESHOP_CONFIG_SORTING_PRODUCT_WIDTH_ASC'),
			JText::_('ESHOP_CONFIG_SORTING_PRODUCT_WIDTH_DESC'),
			JText::_('ESHOP_CONFIG_SORTING_PRODUCT_HEIGHT_ASC'),
			JText::_('ESHOP_CONFIG_SORTING_PRODUCT_HEIGHT_DESC'),
			JText::_('ESHOP_CONFIG_SORTING_PRODUCT_WEIGHT_ASC'),
			JText::_('ESHOP_CONFIG_SORTING_PRODUCT_WEIGHT_DESC'),
			JText::_('ESHOP_CONFIG_SORTING_PRODUCT_QUANTITY_ASC'),
			JText::_('ESHOP_CONFIG_SORTING_PRODUCT_QUANTITY_DESC'),
			JText::_('ESHOP_CONFIG_SORTING_PRODUCT_SHORT_DESC_ASC'),
			JText::_('ESHOP_CONFIG_SORTING_PRODUCT_SHORT_DESC_DESC'),
			JText::_('ESHOP_CONFIG_SORTING_PRODUCT_DESC_ASC'),
			JText::_('ESHOP_CONFIG_SORTING_PRODUCT_DESC_DESC'),
			JText::_('ESHOP_CONFIG_SORTING_PRODUCT_RATES_ASC'),
			JText::_('ESHOP_CONFIG_SORTING_PRODUCT_RATES_DESC'),
			JText::_('ESHOP_CONFIG_SORTING_PRODUCT_REVIEWS_ASC'),
			JText::_('ESHOP_CONFIG_SORTING_PRODUCT_REVIEWS_DESC')
		);
		$options = array();
		for ($i = 0; $n = count($sortValues), $i < $n; $i++)
		{
			$options[] = JHtml::_('select.option', $sortValues[$i], $sortTexts[$i]);
		}
		$lists['default_sorting'] = JHtml::_('select.genericlist', $options, 'default_sorting', ' class="inputbox" style="width: 300px;" ', 'value', 'text', isset($config->default_sorting) ? $config->default_sorting : 'a.ordering-ASC');
		//Image
		$options = array();
		$options[] = JHtml::_('select.option', 'resizeImage', 'Resize Image');
		$options[] = JHtml::_('select.option', 'cropsizeImage', 'Cropsize Image');
		$options[] = JHtml::_('select.option', 'maxsizeImage', 'Maxsize Image');
		$lists['category_image_size_function'] = JHtml::_('select.genericlist', $options, 'category_image_size_function', ' class="inputbox" ', 'value', 'text', isset($config->category_image_size_function) ? $config->category_image_size_function : 'resizeImage');
		$lists['manufacturer_image_size_function'] = JHtml::_('select.genericlist', $options, 'manufacturer_image_size_function', ' class="inputbox" ', 'value', 'text', isset($config->manufacturer_image_size_function) ? $config->manufacturer_image_size_function : 'resizeImage');
		$lists['thumb_image_size_function'] = JHtml::_('select.genericlist', $options, 'thumb_image_size_function', ' class="inputbox" ', 'value', 'text', isset($config->thumb_image_size_function) ? $config->thumb_image_size_function : 'resizeImage');
		$lists['popup_image_size_function'] = JHtml::_('select.genericlist', $options, 'popup_image_size_function', ' class="inputbox" ', 'value', 'text', isset($config->popup_image_size_function) ? $config->popup_image_size_function : 'resizeImage');
		$lists['list_image_size_function'] = JHtml::_('select.genericlist', $options, 'list_image_size_function', ' class="inputbox" ', 'value', 'text', isset($config->list_image_size_function) ? $config->list_image_size_function : 'resizeImage');
		$lists['additional_image_size_function'] = JHtml::_('select.genericlist', $options, 'additional_image_size_function', ' class="inputbox" ', 'value', 'text', isset($config->additional_image_size_function) ? $config->additional_image_size_function : 'resizeImage');
		$lists['related_image_size_function'] = JHtml::_('select.genericlist', $options, 'related_image_size_function', ' class="inputbox" ', 'value', 'text', isset($config->related_image_size_function) ? $config->related_image_size_function : 'resizeImage');
		$lists['compare_image_size_function'] = JHtml::_('select.genericlist', $options, 'compare_image_size_function', ' class="inputbox" ', 'value', 'text', isset($config->compare_image_size_function) ? $config->compare_image_size_function : 'resizeImage');
		$lists['wishlist_image_size_function'] = JHtml::_('select.genericlist', $options, 'wishlist_image_size_function', ' class="inputbox" ', 'value', 'text', isset($config->wishlist_image_size_function) ? $config->wishlist_image_size_function : 'resizeImage');
		$lists['cart_image_size_function'] = JHtml::_('select.genericlist', $options, 'cart_image_size_function', ' class="inputbox" ', 'value', 'text', isset($config->cart_image_size_function) ? $config->cart_image_size_function : 'resizeImage');
		$lists['label_image_size_function'] = JHtml::_('select.genericlist', $options, 'label_image_size_function', ' class="inputbox" ', 'value', 'text', isset($config->label_image_size_function) ? $config->label_image_size_function : 'resizeImage');
		$lists['option_image_size_function'] = JHtml::_('select.genericlist', $options, 'option_image_size_function', ' class="inputbox" ', 'value', 'text', isset($config->option_image_size_function) ? $config->option_image_size_function : 'resizeImage');
		
		$options = array();
		$options[] = JHtml::_('select.option', 'popout', 'Popout');
		$options[] = JHtml::_('select.option', 'zoom', 'Zoom');
		$lists['view_image'] = JHtml::_('select.genericlist', $options, 'view_image', ' class="inputbox" ', 'value', 'text', isset($config->view_image) ? $config->view_image : 'popout');
		
		//Social
		$options = array();
		$options[] = JHtml::_('select.option', 'arial', 'arial');
		$options[] = JHtml::_('select.option', 'lucida grande', 'lucida grande');
		$options[] = JHtml::_('select.option', 'segoe ui', 'segoe ui');
		$options[] = JHtml::_('select.option', 'tahoma', 'tahoma');
		$options[] = JHtml::_('select.option', 'trebuchet ms', 'trebuchet ms');
		$options[] = JHtml::_('select.option', 'verdana', 'verdana');
		$lists['button_font'] = JHtml::_('select.genericlist', $options, 'button_font', ' class="inputbox" ', 'value', 'text', isset($config->button_font) ? $config->button_font : 'arial');
		
		$options = array();
		$options[] = JHtml::_('select.option', 'light', 'light');
		$options[] = JHtml::_('select.option', 'dark', 'dark');
		$lists['button_theme'] = JHtml::_('select.genericlist', $options, 'button_theme', ' class="inputbox" ', 'value', 'text', isset($config->button_theme) ? $config->button_theme : 'light');
		
		$options = array();
		$options[] = JHtml::_('select.option', 'af_ZA', 'Afrikaans');
		$options[] = JHtml::_('select.option', 'ar_AR', 'Arabic');
		$options[] = JHtml::_('select.option', 'az_AZ', 'Azerbaijani');
		$options[] = JHtml::_('select.option', 'be_BY', 'Belarusian');
		$options[] = JHtml::_('select.option', 'bg_BG', 'Bulgarian');
		$options[] = JHtml::_('select.option', 'bn_IN', 'Bengali');
		$options[] = JHtml::_('select.option', 'bs_BA', 'Bosnian');
		$options[] = JHtml::_('select.option', 'ca_ES', 'Catalan');
		$options[] = JHtml::_('select.option', 'cs_CZ', 'Czech');
		$options[] = JHtml::_('select.option', 'cy_GB', 'Welsh');
		$options[] = JHtml::_('select.option', 'da_DK', 'Danish');
		$options[] = JHtml::_('select.option', 'de_DE', 'German');
		$options[] = JHtml::_('select.option', 'el_GR', 'Greek');
		$options[] = JHtml::_('select.option', 'en_GB', 'English (UK)');
		$options[] = JHtml::_('select.option', 'en_PI', 'English (Pirate)');
		$options[] = JHtml::_('select.option', 'en_UD', 'English (Upside Down)');
		$options[] = JHtml::_('select.option', 'en_US', 'English (US)');
		$options[] = JHtml::_('select.option', 'eo_EO', 'Esperanto');
		$options[] = JHtml::_('select.option', 'es_ES', 'Spanish (Spain)');
		$options[] = JHtml::_('select.option', 'es_LA', 'Spanish');
		$options[] = JHtml::_('select.option', 'et_EE', 'Estonian');
		$options[] = JHtml::_('select.option', 'eu_ES', 'Basque');
		$options[] = JHtml::_('select.option', 'fa_IR', 'Persian');
		$options[] = JHtml::_('select.option', 'fb_LT', 'Leet Speak');
		$options[] = JHtml::_('select.option', 'fi_FI', 'Finnish');
		$options[] = JHtml::_('select.option', 'fo_FO', 'Faroese');
		$options[] = JHtml::_('select.option', 'fr_CA', 'French (Canada)');
		$options[] = JHtml::_('select.option', 'fr_FR', 'French (France)');
		$options[] = JHtml::_('select.option', 'fy_NL', 'Frisian');
		$options[] = JHtml::_('select.option', 'ga_IE', 'Irish');
		$options[] = JHtml::_('select.option', 'gl_ES', 'Galician');
		$options[] = JHtml::_('select.option', 'he_IL', 'Hebrew');
		$options[] = JHtml::_('select.option', 'hi_IN', 'Hindi');
		$options[] = JHtml::_('select.option', 'hr_HR', 'Croatian');
		$options[] = JHtml::_('select.option', 'hu_HU', 'Hungarian');
		$options[] = JHtml::_('select.option', 'hy_AM', 'Armenian');
		$options[] = JHtml::_('select.option', 'id_ID', 'Indonesian');
		$options[] = JHtml::_('select.option', 'is_IS', 'Icelandic');
		$options[] = JHtml::_('select.option', 'it_IT', 'Italian');
		$options[] = JHtml::_('select.option', 'ja_JP', 'Japanese');
		$options[] = JHtml::_('select.option', 'ka_GE', 'Georgian');
		$options[] = JHtml::_('select.option', 'km_KH', 'Khmer');
		$options[] = JHtml::_('select.option', 'ko_KR', 'Korean');
		$options[] = JHtml::_('select.option', 'ku_TR', 'Kurdish');
		$options[] = JHtml::_('select.option', 'la_VA', 'Latin');
		$options[] = JHtml::_('select.option', 'lt_LT', 'Lithuanian');
		$options[] = JHtml::_('select.option', 'lv_LV', 'Latvian');
		$options[] = JHtml::_('select.option', 'mk_MK', 'Macedonian');
		$options[] = JHtml::_('select.option', 'ml_IN', 'Malayalam');
		$options[] = JHtml::_('select.option', 'ms_MY', 'Malay');
		$options[] = JHtml::_('select.option', 'nb_NO', 'Norwegian (bokmal)');
		$options[] = JHtml::_('select.option', 'ne_NP', 'Nepali');
		$options[] = JHtml::_('select.option', 'nl_NL', 'Dutch');
		$options[] = JHtml::_('select.option', 'nn_NO', 'Norwegian (nynorsk)');
		$options[] = JHtml::_('select.option', 'pa_IN', 'Punjabi');
		$options[] = JHtml::_('select.option', 'pl_PL', 'Polish');
		$options[] = JHtml::_('select.option', 'ps_AF', 'Pashto');
		$options[] = JHtml::_('select.option', 'pt_BR', 'Portuguese (Brazil)');
		$options[] = JHtml::_('select.option', 'pt_PT', 'Portuguese (Portugal)');
		$options[] = JHtml::_('select.option', 'ro_RO', 'Romanian');
		$options[] = JHtml::_('select.option', 'ru_RU', 'Russian');
		$options[] = JHtml::_('select.option', 'sk_SK', 'Slovak');
		$options[] = JHtml::_('select.option', 'sl_SI', 'Slovenian');
		$options[] = JHtml::_('select.option', 'sq_AL', 'Albanian');
		$options[] = JHtml::_('select.option', 'sr_RS', 'Serbian');
		$options[] = JHtml::_('select.option', 'sv_SE', 'Swedish');
		$options[] = JHtml::_('select.option', 'sw_KE', 'Swahili');
		$options[] = JHtml::_('select.option', 'ta_IN', 'Tamil');
		$options[] = JHtml::_('select.option', 'te_IN', 'Telugu');
		$options[] = JHtml::_('select.option', 'th_TH', 'Thai');
		$options[] = JHtml::_('select.option', 'tl_PH', 'Filipino');
		$options[] = JHtml::_('select.option', 'tr_TR', 'Turkish');
		$options[] = JHtml::_('select.option', 'uk_UA', 'Ukrainian');
		$options[] = JHtml::_('select.option', 'vi_VN', 'Vietnamese');
		$options[] = JHtml::_('select.option', 'zh_CN', 'Simplified Chinese (China)');
		$options[] = JHtml::_('select.option', 'zh_HK', 'Traditional Chinese (Hong Kong)');
		$options[] = JHtml::_('select.option', 'zh_TW', 'Traditional Chinese (Taiwan)');
		$lists['button_language'] = JHtml::_('select.genericlist', $options, 'button_language', ' class="inputbox" ', 'value', 'text', isset($config->button_language) ? $config->button_language : 'en_GB');
		
		$options = array();
		$options[] = JHtml::_('select.option', 'standard', 'standard');
		$options[] = JHtml::_('select.option', 'button_count', 'button_count');
		$options[] = JHtml::_('select.option', 'box_count', 'box_count');
		$lists['button_layout'] = JHtml::_('select.genericlist', $options, 'button_layout', ' class="inputbox" ', 'value', 'text', isset($config->button_layout) ? $config->button_layout : 'standard');
		
		$options = array();
		$options[] = JHtml::_('select.option', 'top', 'Vertical');
		$options[] = JHtml::_('select.option', 'right', 'Horizontal');
		$options[] = JHtml::_('select.option', 'no-count', 'No Count');
		$lists['linkedin_layout'] = JHtml::_('select.genericlist', $options, 'linkedin_layout', ' class="inputbox" ', 'value', 'text', isset($config->linkedin_layout) ? $config->linkedin_layout : 'top');
		
		$lists['social_enable']	= JHtml::_('select.booleanlist', 'social_enable', ' class="inputbox" ', isset($config->social_enable) ? $config->social_enable : '0');
		$lists['show_facebook_button']	= JHtml::_('select.booleanlist', 'show_facebook_button', ' class="inputbox" ', isset($config->show_facebook_button) ? $config->show_facebook_button : '1');
		$lists['show_faces']	= JHtml::_('select.booleanlist', 'show_faces', ' class="inputbox" ', isset($config->show_faces) ? $config->show_faces : '0');
		$lists['show_facebook_comment']	= JHtml::_('select.booleanlist', 'show_facebook_comment', ' class="inputbox" ', isset($config->show_facebook_comment) ? $config->show_facebook_comment : '1');
		$lists['show_twitter_button']	= JHtml::_('select.booleanlist', 'show_twitter_button', ' class="inputbox" ', isset($config->show_twitter_button) ? $config->show_twitter_button : '1');
		$lists['show_pinit_button']	= JHtml::_('select.booleanlist', 'show_pinit_button', ' class="inputbox" ', isset($config->show_pinit_button) ? $config->show_pinit_button : '1');
		$lists['show_google_button']	= JHtml::_('select.booleanlist', 'show_google_button', ' class="inputbox" ', isset($config->show_google_button) ? $config->show_google_button : '1');
		$lists['show_linkedin_button']	= JHtml::_('select.booleanlist', 'show_linkedin_button', ' class="inputbox" ', isset($config->show_linkedin_button) ? $config->show_linkedin_button : '1');
		$lists['add_category_path'] = JHtml::_('select.booleanlist', 'add_category_path', ' class="inputbox" ', isset($config->add_category_path) ? $config->add_category_path : '1');

		// Initialize variables.
		if (version_compare(JVERSION, '3.0', 'ge') && JLanguageMultilang::isEnabled() && count(EshopHelper::getLanguages()) > 1)
		{
			$languages = EshopHelper::getLanguages();
			for ($j = 0; $j < count($languages); $j++)
			{
				$query->clear();
				$rows = array();
				$query->select('a.id AS value, a.title AS text, a.level');
				$query->from('#__menu AS a');
				$query->join('LEFT', $db->quoteName('#__menu').' AS b ON a.lft > b.lft AND a.rgt < b.rgt');
				$query->where('a.menutype != '.$db->quote(''));
				$query->where('a.component_id IN (SELECT extension_id FROM #__extensions WHERE element="com_eshop")');
				$query->where('a.client_id = 0');
				$query->where('a.published = 1');
				$query->where('a.language = "' . $languages[$j]->lang_code . '" || a.language="*"');
				$query->group('a.id, a.title, a.level, a.lft, a.rgt, a.menutype, a.parent_id, a.published');
				$query->order('a.lft ASC');
				
				// Get the options.
				$db->setQuery($query);
				$rows = $db->loadObjectList();
				
				// Check for a database error.
				if ($db->getErrorNum())
				{
					JError::raiseWarning(500, $db->getErrorMsg());
				}
				
				// Pad the option text with spaces using depth level as a multiplier.
				for ($i = 0, $n = count($rows); $i < $n; $i++)
				{
					$rows[$i]->text = str_repeat('- ', $rows[$i]->level).$rows[$i]->text;
				}
				$options = array();
				$options[] = JHtml::_('select.option', 0, JText::_('ESHOP_NONE'), 'value', 'text');
				$rows = array_merge($options, $rows);
				
				$lists['default_menu_item_'.$languages[$j]->lang_code] = JHtml::_('select.genericlist', $rows, 'default_menu_item_'.$languages[$j]->lang_code,
					array(
						'option.text.toHtml'	=> false,
						'option.text'			=> 'text',
						'option.value'			=> 'value',
						'list.attr'				=> ' class="inputbox" ',
						'list.select'			=> isset($config->{'default_menu_item_'.$languages[$j]->lang_code}) ? $config->{'default_menu_item_'.$languages[$j]->lang_code} : '0'));
			}
			$this->languages = $languages;
		}
		else 
		{
			$query->clear();
			$rows = array();
			$query->select('a.id AS value, a.title AS text, a.level');
			$query->from('#__menu AS a');
			$query->join('LEFT', $db->quoteName('#__menu').' AS b ON a.lft > b.lft AND a.rgt < b.rgt');
			$query->where('a.menutype != '.$db->quote(''));
			$query->where('a.component_id IN (SELECT extension_id FROM #__extensions WHERE element="com_eshop")');
			$query->where('a.client_id = 0');
			$query->where('a.published = 1');
			$query->group('a.id, a.title, a.level, a.lft, a.rgt, a.menutype, a.parent_id, a.published');
			$query->order('a.lft ASC');
			
			// Get the options.
			$db->setQuery($query);
			$rows = $db->loadObjectList();
			
			// Check for a database error.
			if ($db->getErrorNum())
			{
				JError::raiseWarning(500, $db->getErrorMsg());
			}
			
			// Pad the option text with spaces using depth level as a multiplier.
			for ($i = 0, $n = count($rows); $i < $n; $i++)
			{
				$rows[$i]->text = str_repeat('- ', $rows[$i]->level).$rows[$i]->text;
			}
			$options = array();
			$options[] = JHtml::_('select.option', 0, JText::_('ESHOP_NONE'), 'value', 'text');
			$rows = array_merge($options, $rows);
			
			$lists['default_menu_item'] = JHtml::_('select.genericlist', $rows, 'default_menu_item',
				array(
					'option.text.toHtml'	=> false,
					'option.text'			=> 'text',
					'option.value'			=> 'value',
					'list.attr'				=> ' class="inputbox" ',
					'list.select'			=> isset($config->default_menu_item) ? $config->default_menu_item : '0'));
		}
		
		$this->lists = $lists;
		$this->config = $config;
		$this->sortOptions = $sortOptions;
		$this->sortValues = $sortValues;
		$this->sortTexts = $sortTexts;
		JFactory::getDocument()->addScript(JURI::root() . 'administrator/components/com_eshop/assets/js/eshop.js')->addScriptDeclaration(EshopHtmlHelper::getZonesArrayJs());
		EshopHelper::chosen();
		
		parent::display($tpl);
	}
}