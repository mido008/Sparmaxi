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
 * EShop controller
 *
 * @package		Joomla
 * @subpackage	EShop
 * @since 1.5
 */
class EShopControllerCart extends JControllerLegacy
{
	/**
	 * Constructor function
	 *
	 * @param array $config
	 */
	function __construct($config = array())
	{
		parent::__construct($config);
	}
	
	/**
	 * 
	 * Function to add a product to the cart
	 */
	function add()
	{
		$cart = new EshopCart();
		$json = array();
		$productId = JRequest::getInt('id');
		$quantity = JRequest::getInt('quantity') > 0 ? JRequest::getInt('quantity') : 1;
		if (JRequest::getVar('options'))
		{
			$options = array_filter(JRequest::getVar('options'));
		}
		else
		{
			$options = array();
		}
		//Validate options first
		$productOptions = EshopHelper::getProductOptions($productId, JFactory::getLanguage()->getTag());
		for ($i = 0; $n = count($productOptions), $i < $n; $i++)
		{
			$productOption = $productOptions[$i];
			if ($productOption->required && empty($options[$productOption->product_option_id]))
			{
				$json['error']['option'][$productOption->product_option_id] = $productOption->option_name . ' ' . JText::_('ESHOP_REQUIRED');
			}
		}
		if (!$json)
		{
			$product = EshopHelper::getProduct($productId, JFactory::getLanguage()->getTag());
			$cart->add($productId, $quantity, $options);
			$viewProductLink = JRoute::_(EshopRoute::getProductRoute($productId, EshopHelper::getProductCategory($productId)));
			$viewCartLink = JRoute::_(EshopRoute::getViewRoute('cart'));
			if (EshopHelper::getConfigValue('active_https'))
			{
				$viewCheckoutLink = JRoute::_(EshopRoute::getViewRoute('checkout'), true, 1);
			}
			else 
			{
				$viewCheckoutLink = JRoute::_(EshopRoute::getViewRoute('checkout'));
			}
			$json['success'] = true;
			//Clear shipping and payment methods
			$session = JFactory::getSession();
			$session->clear('shipping_method');
			$session->clear('shipping_methods');
			$session->clear('payment_method');
		}
		else
		{
			$json['redirect'] = JRoute::_(EshopRoute::getProductRoute($productId, EshopHelper::getProductCategory($productId)));
		}
		echo json_encode($json);
		JFactory::getApplication()->close();
	}
	
	/**
	 *
	 * Function to re-order
	 */
	function reOrder()
	{
		$mainframe = JFactory::getApplication();
		$user = JFactory::getUser();
		if (!$user->id)
		{
			$mainframe->enqueueMessage(JText::_('ESHOP_RE_ORDER_LOGIN_PROMPT'), 'Notice');
			$mainframe->redirect('index.php?option=com_users&view=login');
		}
		else 
		{
			$orderId = JRequest::getInt('order_id');
			// Validate order
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('id')
				->from('#__eshop_orders')
				->where('id = ' . intval($orderId))
				->where('customer_id = ' . intval($user->id));
			$db->setQuery($query);
			if (!$db->loadResult())
			{
				$mainframe->enqueueMessage(JText::_('ESHOP_RE_ORDER_NOT_ALLOW'), 'Error');
				$mainframe->redirect(EshopRoute::getViewRoute('customer') . '&layout=orders');
			}
			else 
			{
				$cart = new EshopCart();
				// Clear cart first
				$cart->clear();
				// Then, clear shipping and payment methods
				$session = JFactory::getSession();
				$session->clear('shipping_method');
				$session->clear('shipping_methods');
				$session->clear('payment_method');
				// Re-add products and corresponding options to the cart
				$orderProducts = EshopHelper::getOrderProducts($orderId);
				if (!count($orderProducts))
				{
					$mainframe->enqueueMessage(JText::_('ESHOP_RE_ORDER_NOT_ALLOW'), 'Error');
					$mainframe->redirect(EshopRoute::getViewRoute('customer') . '&layout=orders');
				}
				else
				{
					for ($i = 0; $n = count($orderProducts), $i < $n; $i++)
					{
						$orderProduct = $orderProducts[$i];
						$options = array();
						for ($j = 0; $m = count($orderProduct->orderOptions), $j < $m; $j++)
						{
							$option = $orderProduct->orderOptions[$j];
							$optionType = $option->option_type;
							if ($optionType == 'Select' || $optionType == 'Radio')
							{
								$options[$option->product_option_id] = $option->product_option_value_id;
							}
							elseif ($optionType == 'Checkbox')
							{
								if (is_array($options[$option->product_option_id]))
								{
									$options[$option->product_option_id][] = $option->product_option_value_id;
								}
								else
								{
									$options[$option->product_option_id] = array();
									$options[$option->product_option_id][] = $option->product_option_value_id;
								}
							}
							else
							{
								$options[$option->product_option_id] = $option->option_value;
							}
						}
						$cart->add($orderProduct->product_id, $orderProduct->quantity, $options);
					}
					$mainframe->redirect(JRoute::_(EshopRoute::getViewRoute('cart')));
				}
			}
		}
	}
	
	/**
	 * 
	 * Function to update quantity of a product in the cart
	 */
	function update()
	{
		$session = JFactory::getSession();
		$session->set('success', JText::_('ESHOP_CART_UPDATE_MESSAGE'));
		$key = JRequest::getVar('key');
		$quantity = JRequest::getInt('quantity');
		$cart = new EshopCart();
		$cart->update($key, $quantity);
		//Clear shipping and payment methods
		$session->clear('shipping_method');
		$session->clear('shipping_methods');
		$session->clear('payment_method');
	}
	
	/**
	 * 
	 * Function to update quantities of all products in the cart
	 */
	function updates()
	{
		$session = JFactory::getSession();
		$session->set('success', JText::_('ESHOP_CART_UPDATE_MESSAGE'));
		$key = JRequest::getVar('key');
		$quantity = JRequest::getVar('quantity');
		$cart = new EshopCart();
		$cart->updates($key, $quantity);
		//Clear shipping and payment methods
		$session->clear('shipping_method');
		$session->clear('shipping_methods');
		$session->clear('payment_method');
	}
	
	/**
	 * 
	 * Function to remove a product from the cart
	 */
	function remove()
	{
		$session = JFactory::getSession();
		$key = JRequest::getVar('key');
		$cart = new EshopCart();
		$cart->remove($key);
		//Clear shipping and payment methods
		$session = JFactory::getSession();
		$session->clear('shipping_method');
		$session->clear('shipping_methods');
		$session->clear('payment_method');
		if (JRequest::getInt('redirect'))
		{
			$session->set('success', JText::_('ESHOP_CART_REMOVED_MESSAGE'));
		}
	}
	
	/**
	 * 
	 * Function to apply coupon to the cart
	 */
	function applyCoupon()
	{
		$session = JFactory::getSession();
		$couponCode = JRequest::getVar('coupon_code');
		$coupon = new EshopCoupon();
		$couponData = $coupon->getCouponData($couponCode);
		if (!count($couponData))
		{
			$couponInfo = $coupon->getCouponInfo($couponCode);
			$user = JFactory::getUser();
			if (is_object($couponInfo) && $couponInfo->coupon_per_customer && !$user->get('id'))
			{
				$session->set('warning', JText::_('ESHOP_COUPON_IS_ONLY_FOR_REGISTERED_USER'));
			}
			else 
			{
				$session->set('warning', JText::_('ESHOP_COUPON_APPLY_ERROR'));
			}
		}
		else
		{
			$session->set('coupon_code', $couponCode);
			$session->set('success', JText::_('ESHOP_COUPON_APPLY_SUCCESS'));
		}
	}
	
	/**
	 *
	 * Function to apply voucher to the cart
	 */
	function applyVoucher()
	{
		$session = JFactory::getSession();
		$voucherCode = JRequest::getVar('voucher_code');
		$voucher = new EshopVoucher();
		$voucherData = $voucher->getVoucherData($voucherCode);
		if (!count($voucherData))
		{
			$session->set('warning', JText::_('ESHOP_VOUCHER_APPLY_ERROR'));
		}
		else
		{
			$session->set('voucher_code', $voucherCode);
			$session->set('success', JText::_('ESHOP_VOUCHER_APPLY_SUCCESS'));
		}
	}
	
	/**
	 * 
	 * Function to apply shipping to the cart
	 */
	function applyShipping()
	{
		$shippingMethod = explode('.', Jrequest::getVar('shipping_method'));
		$session = JFactory::getSession();
		$shippingMethods = $session->get('shipping_methods');
		if (isset($shippingMethods) && isset($shippingMethods[$shippingMethod[0]]))
		{
			$session->set('shipping_method', $shippingMethods[$shippingMethod[0]]['quote'][$shippingMethod[1]]);
			$session->set('success', JText::_('ESHOP_SHIPPING_APPLY_SUCCESS'));
		}
		else
		{
			$session->set('warning', JText::_('ESHOP_SHIPPING_APPLY_ERROR'));
		}
	}
	
	/**
	 * 
	 * Function to get Quote
	 */
	function getQuote()
	{
		$json = array();
		$cart = new EshopCart();
		$countryId = JRequest::getInt('country_id');
		$zoneId = JRequest::getInt('zone_id');
		$postcode = JRequest::getVar('postcode');
		if (!$cart->hasProducts())
		{
			$json['error']['warning'] = JText::_('ESHOP_ERROR_HAS_PRODUCTS');
		}

		if (!$cart->hasShipping())
		{
			$json['error']['warning'] = JText::_('ESHOP_ERROR_HAS_SHIPPING');
		}
		if (!$countryId)
		{
			$json['error']['country'] = JText::_('ESHOP_ERROR_COUNTRY');
		}
		if (!$zoneId)
		{
			$json['error']['zone'] = JText::_('ESHOP_ERROR_ZONE');
		}
		$countryInfo = EshopHelper::getCountry($countryId);
		if (is_object($countryInfo) && $countryInfo->postcode_required && ((strlen($postcode) < 2) || (strlen($postcode) > 8)))
		{
			$json['error']['postcode'] = JText::_('ESHOP_ERROR_POSTCODE');
		}
		if (!$json) {
			$session = JFactory::getSession();
			$tax = new EshopTax(EshopHelper::getConfig());
			$tax->setShippingAddress($countryId, $zoneId);
			$session->set('shipping_country_id', $countryId);
			$session->set('shipping_zone_id', $zoneId);
			$session->set('shipping_postcode', $postcode);
			if (is_object($countryInfo))
			{
				$countryName = $countryInfo->country_name;
				$isoCode2 = $countryInfo->iso_code_2;
				$isoCode3 = $countryInfo->iso_code_3;
			}
			else
			{
				$countryName = '';
				$isoCode2 = '';
				$isoCode3 = '';
			}
			$zoneInfo = EshopHelper::getZone($zoneId);
			if (is_object($zoneInfo))
			{
				$zoneName = $zoneInfo->zone_name;
				$zoneCode = $zoneInfo->zone_code;
			}
			else
			{
				$zoneName = '';
				$zoneCode = '';
			}
			$addressData = array(
				'firstname'			=> '',
				'lastname'			=> '',
				'company'			=> '',
				'address_1'			=> '',
				'address_2'			=> '',
				'postcode'			=> $postcode,
				'city'				=> '',
				'zone_id'			=> $zoneId,
				'zone_name'			=> $zoneName,
				'zone_code'			=> $zoneCode,
				'country_id'		=> $countryId,
				'country_name'		=> $countryName,	
				'iso_code_2'		=> $isoCode2,
				'iso_code_3'		=> $isoCode3
			);
			$quoteData = array();
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('*')
				->from('#__eshop_shippings')
				->where('published = 1')
				->order('ordering');
			$db->setQuery($query);
			$rows = $db->loadObjectList();
			for ($i = 0; $n = count($rows), $i < $n; $i++)
			{
				$shippingName = $rows[$i]->name;
				$params = new JRegistry($rows[$i]->params);
				require_once JPATH_COMPONENT . '/plugins/shipping/' . $shippingName . '.php';
				$shippingClass = new $shippingName();
				$quote = $shippingClass->getQuote($addressData, $params);
				if ($quote)
				{
					$quoteData[$shippingName] = array(
						'title'			=> $quote['title'],
						'quote'			=> $quote['quote'],
						'ordering'		=> $quote['ordering'],
						'error'			=> $quote['error']
					);
				}
			}
			$session->set('shipping_methods', $quoteData);
			if ($session->get('shipping_methods'))
			{
				$json['shipping_methods'] = $session->get('shipping_methods');
			}
			else
			{
				$json['error']['warning'] = JText::_('ESHOP_NO_SHIPPING_METHODS');
			}
		}
		echo json_encode($json);		
		JFactory::getApplication()->close();
	}
	
	/**
	 * 
	 * Function to get Zones for a specific Country
	 */
	function getZones()
	{
		$json = array();
		$countryId = JRequest::getInt('country_id');
		$countryInfo = EshopHelper::getCountry($countryId);
		if (is_object($countryInfo))
		{
			$json = array(
				'country_id'			=> $countryInfo->id,
				'country_name'			=> $countryInfo->country_name,
				'iso_code_2'			=> $countryInfo->iso_code_2,
				'iso_code_3'			=> $countryInfo->iso_code_3,
				'postcode_required'		=> $countryInfo->postcode_required,
				'zones'					=> EshopHelper::getCountryZones($countryId)
			);
		}
		echo json_encode($json);
		JFactory::getApplication()->close();
	}
}