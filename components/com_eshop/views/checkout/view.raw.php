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
 *
 * @package Joomla
 * @subpackage EShop
 * @since 1.5
 */
class EShopViewCheckout extends EShopView
{

	function display($tpl = null)
	{
		$cart = new EshopCart();
		$user = JFactory::getUser();
		$this->user = $user;
		$this->shipping_required = $cart->hasShipping();
		switch ($this->getLayout())
		{
			case 'login':
				$this->_displayLogin($tpl);
				break;
			case 'guest':
				$this->_displayGuest($tpl);
				break;
			case 'register':
				$this->_displayRegister($tpl);
				break;
			case 'payment_address':
				$this->_displayPaymentAddress($tpl);
				break;
			case 'shipping_address':
				$this->_displayShippingAddress($tpl);
				break;
			case 'guest_shipping':
				$this->_displayGuestShipping($tpl);
				break;	
			case 'shipping_method':
				$this->_displayShippingMethod($tpl);
				break;
			case 'payment_method':
				$this->_displayPaymentMethod($tpl);
				break;
			case 'confirm':
				$this->_displayConfirm($tpl);
				break;
			default:
				break;
		}
	}

	/**
	 *
	 * @param string $tpl        	
	 */
	function _displayLogin($tpl = null)
	{
		parent::display($tpl);
	}

	/**
	 * 
	 * Function to display Guest layout
	 * @param string $tpl        	
	 */
	function _displayGuest($tpl = null)
	{
		$lists = array();
		$session = JFactory::getSession();
		$guest = $session->get('guest');								
		$fields = EshopHelper::getFormFields('B');
		$form = new RADForm($fields);
		if (is_array($guest))
		{
			$form->bind($guest);
			if (isset($guest['payment']))
			{
				$form->bind($guest['payment']);
			}
		}
		// Prepare default data for zone - start
		if (EshopHelper::isFieldPublished('zone_id'))
		{
			if (EshopHelper::isFieldPublished('country_id'))
			{
				$countryField = $form->getField('country_id');
				$countryId = (int)$session->get('payment_country_id') ? $session->get('payment_country_id') : $countryField->getValue();
			}
			else
			{
				$countryId = EshopHelper::getConfigValue('country_id');
			}
			if ($countryId)
			{
				$zoneField = $form->getField('zone_id');
				if ($zoneField instanceof RADFormFieldZone)
				{
					$zoneField->setCountryId($countryId);
				}
			}
		}
		// Prepare default data for zone - end
		$this->_getCustomerGroupList($lists, isset($guest['customergroup_id']) ? $guest['customergroup_id'] : '');
		$this->form = $form;
		$this->lists = $lists;
		$this->payment_zone_id = $session->get('payment_zone_id');
						
		parent::display($tpl);
	}

	/**
	 *
	 * Function to display Register layout
	 * @param string $tpl        	
	 */
	function _displayRegister($tpl = null)
	{
		$lists = array();
		$session = JFactory::getSession();		
		$fields = EshopHelper::getFormFields('B');
		$form = new RADForm($fields);
		// Prepare default data for zone - start
		if (EshopHelper::isFieldPublished('zone_id'))
		{
			if (EshopHelper::isFieldPublished('country_id'))
			{
				$countryField = $form->getField('country_id');
				$countryId = (int)$session->get('payment_country_id') ? $session->get('payment_country_id') : $countryField->getValue();
			}
			else
			{
				$countryId = EshopHelper::getConfigValue('country_id');
			}
			if ($countryId)
			{
				$zoneField = $form->getField('zone_id');
				if ($zoneField instanceof RADFormFieldZone)
				{
					$zoneField->setCountryId($countryId);
				}
			}
		}
		// Prepare default data for zone - end
		$accountTerms = EshopHelper::getConfigValue('account_terms');
		if ($accountTerms)
		{
			require_once JPATH_ROOT.'/components/com_content/helpers/route.php';
			if (version_compare(JVERSION, '3.0', 'ge') && JLanguageMultilang::isEnabled() && count(EshopHelper::getLanguages()) > 1)
			{
				$associations = JLanguageAssociations::getAssociations('com_content', '#__content', 'com_content.item', $accountTerms);
				$langCode = JFactory::getLanguage()->getTag();
				if (isset($associations[$langCode]))
				{
					$accountTermsLink = ContentHelperRoute::getArticleRoute($associations[$langCode]->id).'&tmpl=component&format=html';
				}
				else 
				{
					$accountTermsLink = ContentHelperRoute::getArticleRoute($accountTerms).'&tmpl=component&format=html';
				}
			}
			else 
			{
				$accountTermsLink = ContentHelperRoute::getArticleRoute($accountTerms).'&tmpl=component&format=html';
			}
			$this->accountTermsLink = $accountTermsLink;
		}
		$this->_getCustomerGroupList($lists);
		$this->form = $form;
		$this->lists = $lists;
		$this->payment_zone_id = $session->get('payment_zone_id');
		parent::display($tpl);
	}

	/**
	 *
	 * Function to display Payment Address layout
	 * @param string $tpl
	 */
	function _displayPaymentAddress($tpl = null)
	{
		$lists = array();
		$session = JFactory::getSession();						
		$fields = EshopHelper::getFormFields('B');
		$form = new RADForm($fields);
		// Prepare default data for zone - start
		if (EshopHelper::isFieldPublished('zone_id'))
		{
			if (EshopHelper::isFieldPublished('country_id'))
			{
				$countryField = $form->getField('country_id');
				$countryId = (int)$session->get('payment_country_id') ? $session->get('payment_country_id') : $countryField->getValue();
			}
			else
			{
				$countryId = EshopHelper::getConfigValue('country_id');
			}
			if ($countryId)
			{
				$zoneField = $form->getField('zone_id');
				if ($zoneField instanceof RADFormFieldZone)
				{
					$zoneField->setCountryId($countryId);
				}
			}
		}
		// Prepare default data for zone - end
		$this->_getAddressList($lists, $session->get('payment_address_id'));
		$this->form = $form;				
		$this->lists = $lists;
		$this->payment_zone_id = $session->get('payment_zone_id');
		parent::display($tpl);
	}

	/**
	 *
	 * Function to display Shipping Address layout
	 * @param string $tpl        	
	 */
	function _displayShippingAddress($tpl = null)
	{
		$lists = array();
		$session = JFactory::getSession();
		$fields = EshopHelper::getFormFields('S');
		$form = new RADForm($fields);
		// Prepare default data for zone - start
		if (EshopHelper::isFieldPublished('zone_id'))
		{
			if (EshopHelper::isFieldPublished('country_id'))
			{
				$countryField = $form->getField('country_id');
				$countryId = (int)$session->get('shipping_country_id') ? $session->get('shipping_country_id') : $countryField->getValue();
			}
			else
			{
				$countryId = EshopHelper::getConfigValue('country_id');
			}
			if ($countryId)
			{
				$zoneField = $form->getField('zone_id');
				if ($zoneField instanceof RADFormFieldZone)
				{
					$zoneField->setCountryId($countryId);
				}
			}
		}
		// Prepare default data for zone - end
		$this->_getAddressList($lists, $session->get('shipping_address_id'));
		$this->form = $form;
		$this->lists = $lists;
		$this->shipping_zone_id = $session->get('shipping_zone_id');
		
		parent::display($tpl);
	}
	
	/**
	 *
	 * Function to display Guest Shipping layout
	 * @param string $tpl
	 */
	function _displayGuestShipping($tpl = null)
	{
		$session = JFactory::getSession();
		$guest = $session->get('guest');
		$fields = EshopHelper::getFormFields('S');
		$form = new RADForm($fields);
		if (is_array($guest))
		{
			if (isset($guest['shipping']))
			{
				$shipping = $guest['shipping'];
				$form->bind($shipping);
			}
		}
		// Prepare default data for zone - start
		if (EshopHelper::isFieldPublished('zone_id'))
		{
			if (EshopHelper::isFieldPublished('country_id'))
			{
				$countryField = $form->getField('country_id');
				$countryId = (int)$session->get('shipping_country_id') ? $session->get('shipping_country_id') : $countryField->getValue();
			}
			else
			{
				$countryId = EshopHelper::getConfigValue('country_id');
			}
			if ($countryId)
			{
				$zoneField = $form->getField('zone_id');
				if ($zoneField instanceof RADFormFieldZone)
				{
					$zoneField->setCountryId($countryId);
				}
			}
		}
		// Prepare default data for zone - end
		$this->form = $form;		
		$this->shipping_zone_id = $session->get('shipping_zone_id');		
								
		parent::display($tpl);
	}

	/**
	 *
	 * Function to display Shipping Method layout
	 * @param string $tpl        	
	 */
	function _displayShippingMethod($tpl = null)
	{
		$session = JFactory::getSession();
		$user = JFactory::getUser();
		if ($user->get('id') && $session->get('shipping_address_id'))
		{
			//User Shipping
			$addressInfo = EshopHelper::getAddress($session->get('shipping_address_id'));
		}
		else
		{
			//Guest Shipping
			$guest = $session->get('guest');
			$addressInfo = $guest['shipping'];
		}
		$addressData = array(
			'firstname'			=> $addressInfo['firstname'],
			'lastname'			=> $addressInfo['lastname'],
			'company'			=> $addressInfo['company'],
			'address_1'			=> $addressInfo['address_1'],
			'address_2'			=> $addressInfo['address_2'],
			'postcode'			=> $addressInfo['postcode'],
			'city'				=> $addressInfo['city'],
			'zone_id'			=> $addressInfo['zone_id'],
			'zone_name'			=> $addressInfo['zone_name'],
			'zone_code'			=> $addressInfo['zone_code'],
			'country_id'		=> $addressInfo['country_id'],
			'country_name'		=> $addressInfo['country_name'],
			'iso_code_2'		=> $addressInfo['iso_code_2'],
			'iso_code_3'		=> $addressInfo['iso_code_3']
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
			$this->shipping_methods = $session->get('shipping_methods');
		}
		$shippingMethod = $session->get('shipping_method');
		if (is_array($shippingMethod))
		{
			$this->shipping_method = $shippingMethod['name'];
		}
		else
		{
			$this->shipping_method = '';
		}
		$this->delivery_date = $session->get('delivery_date') ? $session->get('delivery_date') : ''; 
		$this->comment = $session->get('comment') ? $session->get('comment') : '';
		parent::display($tpl);
	}

	/**
	 *
	 * Function to display Payment Method layout
	 * @param string $tpl        	
	 */
	function _displayPaymentMethod($tpl = null)
	{
		$session = JFactory::getSession();
		$paymentMethod = JRequest::getVar('payment_method', os_payments::getDefautPaymentMethod(), 'post');
		if (!$paymentMethod)
			$paymentMethod = os_payments::getDefautPaymentMethod();
		$this->comment = $session->get('comment') ? $session->get('comment') : '';
		$this->methods = os_payments::getPaymentMethods();
		$this->paymentMethod = $paymentMethod;
		$checkoutTerms = EshopHelper::getConfigValue('checkout_terms');
		if ($checkoutTerms)
		{
			require_once JPATH_ROOT.'/components/com_content/helpers/route.php';
			if (version_compare(JVERSION, '3.0', 'ge') && JLanguageMultilang::isEnabled() && count(EshopHelper::getLanguages()) > 1)
			{
				$associations = JLanguageAssociations::getAssociations('com_content', '#__content', 'com_content.item', $checkoutTerms);
				$langCode = JFactory::getLanguage()->getTag();
				if (isset($associations[$langCode]))
				{
					require_once JPATH_ROOT.'/components/com_content/helpers/route.php';
					$checkoutTermsLink = ContentHelperRoute::getArticleRoute($associations[$langCode]->id).'&tmpl=component&format=html';
				}
				else
				{
					$checkoutTermsLink = ContentHelperRoute::getArticleRoute($checkoutTerms).'&tmpl=component&format=html';
				}
			}
			else 
			{
				$checkoutTermsLink = ContentHelperRoute::getArticleRoute($checkoutTerms).'&tmpl=component&format=html';
			}
			$this->checkoutTermsLink = $checkoutTermsLink;
		}
		$this->coupon_code = $session->get('coupon_code');
		$this->voucher_code = $session->get('voucher_code');
		$this->checkout_terms_agree = $session->get('checkout_terms_agree');
		$this->currency = new EshopCurrency();
		parent::display($tpl);
	}

	/**
	 *
	 * Function to display Confirm layout
	 * @param string $tpl        	
	 */
	function _displayConfirm($tpl = null)
	{
		// Get information for the order
		$session = JFactory::getSession();
		$tax = new EshopTax(EshopHelper::getConfig());
		$currency = new EshopCurrency();
		$cartData = $this->get('CartData');
		$model = $this->getModel();
		$model->getCosts();
		$totalData = $model->getTotalData();
		$total = $model->getTotal();
		$taxes = $model->getTaxes();
		$this->cartData = $cartData;
		$this->totalData = $totalData;
		$this->total = $total;
		$this->taxes = $taxes;
		$this->tax = $tax;
		$this->currency = $currency;
		// Success message
		if ($session->get('success'))
		{
			$this->success = $session->get('success');
			$session->clear('success');
		}
		if ($total > 0)
		{
			// Payment method
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$paymentMethod = $session->get('payment_method');
			require_once JPATH_COMPONENT . '/plugins/payment/' . $paymentMethod . '.php';
			$query->select('params')
				->from('#__eshop_payments')
				->where('name = "' . $paymentMethod . '"');
			$db->setQuery($query);
			$plugin = $db->loadObject();
			$params = new JRegistry($plugin->params);
			$paymentClass = new $paymentMethod($params);
			$this->paymentClass = $paymentClass;
		}
		parent::display($tpl);
	}

	/**
	 * 
	 * Private method to get Customer Group List
	 * @param array $lists
	 */
	function _getCustomerGroupList(&$lists, $selected = '')
	{
		if (!$selected) {
			$selected = EshopHelper::getConfigValue('customergroup_id');
		}
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$customerGroupDisplay = EshopHelper::getConfigValue('customer_group_display');
		$countCustomerGroup = count(explode(',', $customerGroupDisplay));
		if ($countCustomerGroup > 1)
		{
			$query->select('a.id, b.customergroup_name AS name')
				->from('#__eshop_customergroups AS a')
				->innerJoin('#__eshop_customergroupdetails AS b ON (a.id = b.customergroup_id)')
				->where('a.published = 1')
				->where('b.language = "' . JFactory::getLanguage()->getTag() . '"');
			if ($customerGroupDisplay != '')
				$query->where('a.id IN (' . $customerGroupDisplay . ')');
			$query->order('b.customergroup_name');
			$db->setQuery($query);
			$lists['customergroup_id'] = JHtml::_('select.genericlist', $db->loadObjectList(), 'customergroup_id', ' class="inputbox" ', 'id', 'name', $selected);
		}
		elseif ($countCustomerGroup == 1)
		{
			$lists['default_customergroup_id'] = $customerGroupDisplay;
		}
	}
	
	/**
	 * 
	 * Function to get Address List
	 * @param array $lists
	 * @param int $selected
	 */
	function _getAddressList(&$lists, $selected = '')
	{
		//Get address list
		$user = JFactory::getUser();
		if ($user->get('id')) {
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			//$query->select('a.id, CONCAT(a.firstname, " ", a.lastname, ", ", a.address_1, ", ", a.city, ", ", IF(z.zone_name <> "", CONCAT(z.zone_name, ", "), ""), c.country_name) AS name')
			$query->select('a.*, z.zone_name, c.country_name')
				->from('#__eshop_addresses AS a')
				->leftJoin('#__eshop_zones AS z ON (a.zone_id = z.id)')
				->leftJoin('#__eshop_countries AS c ON (a.country_id = c.id)')
				->where('a.customer_id = ' . (int) $user->get('id'))
				->where('a.address_1 != ""');
			$db->setQuery($query);
			$addresses = $db->loadObjectList();
			for ($i = 0; $n = count($addresses), $i < $n; $i++)
			{
				$address = $addresses[$i];
				$addressText = $address->firstname;
				if (EshopHelper::isFieldPublished('lastname') && $address->lastname != '')
					$addressText .= ' ' . $address->lastname;
				$addressText .= ', ' . $address->address_1;
				if (EshopHelper::isFieldPublished('city') && $address->city != '')
					$addressText .= ', ' . $address->city;
				if (EshopHelper::isFieldPublished('zone_id') && $address->zone_name != '')
					$addressText .= ', ' . $address->zone_name;
				if (EshopHelper::isFieldPublished('country_id') && $address->country_id != '')
					$addressText .= ', ' . $address->country_name;
				$addresses[$i]->addressText = $addressText;
			}
			if (!$selected)
			{
				//Get default address
				$query->clear();
				$query->select('address_id')
					->from('#__eshop_customers')
					->where('customer_id = ' . (int) $user->get('id'));
				$db->setQuery($query);
				$selected = $db->loadResult();
			}
			if (count($addresses))
			{
				$lists['address_id'] = JHtml::_('select.genericlist', $addresses, 'address_id', ' style="width: 100%; margin-bottom: 15px;" size="5" ', 'id', 'addressText', $selected);
			}
		}
	}		
}