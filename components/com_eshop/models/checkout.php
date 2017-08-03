<?php
/**
 * @version		1.4.1
 * @package		Joomla
 * @subpackage	EShop
 * @author		Giang Dinh Truong
 * @copyright	Copyright (C) 2012 Ossolution Team
 * @license		GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die();

class EShopModelCheckout extends EShopModel
{
	
	/**
	 * Entity data
	 *
	 * @var array
	 */
	protected $cartData = null;
	
	/**
	 *
	 * Total Data object array, each element is an price price in the cart
	 * @var object array
	 */
	protected $totalData = null;
	
	/**
	 *
	 * Final total price of the cart
	 * @var float
	 */
	protected $total = null;
	
	/**
	 *
	 * Taxes of all elements in the cart
	 * @var array
	 */
	protected $taxes = null;

	public function __construct($config = array())
	{
		parent::__construct();
		$this->cartData		= null;
		$this->totalData	= null;
		$this->total		= null;
		$this->taxes		= null;
	}

	/**
	 * Function to login user
	 */
	function login()
	{
		JSession::checkToken('post') or jexit(JText::_('JInvalid_Token'));
		$app = JFactory::getApplication();
		// Populate the data array:
		$data = array();
		$data['username'] = JRequest::getVar('username', '', 'method', 'username');
		$data['password'] = JRequest::getString('password', '', 'post', JREQUEST_ALLOWRAW);

		// Get the log in options.
		$options = array();
		$options['remember'] = JRequest::getBool('remember', false);
		
		// Get the log in credentials.
		$credentials = array();
		$credentials['username'] = $data['username'];
		$credentials['password'] = $data['password'];
		
		$json = array();
		// Perform the log in.
		if (true === $app->login($credentials, $options))
		{
			// Success
			if (EshopHelper::getConfigValue('active_https'))
			{
				$json['return'] = JRoute::_(EshopRoute::getViewRoute('checkout'), true, 1);
			}
			else
			{
				$json['return'] = JRoute::_(EshopRoute::getViewRoute('checkout'));
			}
		}
		else
		{
			// Login failed !
			$json['error']['warning'] = JText::_('ESHOP_LOGIN_WARNING');
		}
		return $json;
	}

	/**
	 * 
	 * Function to register user
	 * @param post array $data
	 * @return json array
	 */
	public function register($data)
	{
		$cart = new EshopCart();
		$user = JFactory::getUser();
		$session = JFactory::getSession();
		$json = array();
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		// If user is already logged in, return to checkout page
		if ($user->get('id'))
		{
			if (EshopHelper::getConfigValue('active_https'))
			{
				$json['return'] = JRoute::_(EshopRoute::getViewRoute('checkout'), true, 1);
			}
			else
			{
				$json['return'] = JRoute::_(EshopRoute::getViewRoute('checkout'));
			}
		}
		
		// Validate products in the cart
		if (!$cart->hasProducts())
		{
			$json['return'] = JRoute::_(EshopRoute::getViewRoute('cart'));
		}
		
		if (!$json)
		{
			$fields = EshopHelper::getFormFields('B');
			$form = new RADForm($fields);
			if(!EshopHelper::hasZone($data['country_id']))
			{
				$form->removeRule('zone_id');
			} 			
			$valid = $form->validate($data);
			if (!$valid)
			{
				$json['error'] = $form->getErrors();
			}
			//Email validate
			if ($data['email'] != '')
			{
				$query->select('COUNT(*)')
					->from('#__users')
					->where('email = "' . $data['email'] . '"');
				$db->setQuery($query);
				if ($db->loadResult())
				{
					$json['error']['email'] = JText::_('ESHOP_ERROR_EMAIL_EXISTED');
				}
			}
			// Username validate
			if ($data['username'] == '')
			{
				$json['error']['username'] = JText::_('ESHOP_ERROR_USERNAME');
			}
			else
			{
				$query->clear();
				$query->select('COUNT(*)')
					->from('#__users')
					->where('username = "' . $data['username'] . '"');
				$db->setQuery($query);
				if ($db->loadResult())
				{
					$json['error']['username'] = JText::_('ESHOP_ERROR_USERNAME_EXISTED');
				}
			}
			// Password validate
			if ($data['password1'] == '')
			{
				$json['error']['password1'] = JText::_('ESHOP_ERROR_PASSWORD');
			}
			// Confirm password validate
			if ($data['password1'] != $data['password2'])
			{
				$json['error']['password2'] = JText::_('ESHOP_ERROR_CONFIRM_PASSWORD');
			}
			// Validate account terms agree
			if (EshopHelper::getConfigValue('account_terms') && !isset($data['account_terms_agree']))
			{
				$json['error']['warning'] = JText::_('ESHOP_ERROR_ACCOUNT_TERMS_AGREE');
			}
		}
		if (!$json)
		{
			$session->set('account', 'register');
			// Register user here
			// Load com_users language file
			$lang = JFactory::getLanguage();
			$tag = $lang->getTag();
			if (!$tag)
				$tag = 'en-GB';
			$lang->load('com_users', JPATH_ROOT, $tag);
			$data['name'] = $data['firstname'] . ' ' . $data['lastname'];
			$data['password'] = $data['password2'] = $data['password'] = $data['password1'];
			$data['email1'] = $data['email2'] = $data['email'];
			
			$user = new JUser();
			$params = JComponentHelper::getParams('com_users');
			$data['groups'] = array();
			$data['groups'][] = $params->get('new_usertype', 2);
			$data['block'] = 0;
			if (!$user->bind($data))
			{
				$json['error']['warning'] = JText::sprintf('COM_USERS_REGISTRATION_BIND_FAILED', $user->getError());
			}
			else
			{
				// Store the data.
				if (!$user->save())
				{
					$json['error']['warning'] = JText::sprintf('COM_USERS_REGISTRATION_SAVE_FAILED', $user->getError());
				}
			}
		}
		if (!$json)
		{
			// Login user first
			$app = JFactory::getApplication();
			$credentials = array();
			$credentials['username'] = $data['username'];
			$credentials['password'] = $data['password1'];
			$options = array();
			if (true === $app->login($credentials, $options))
			{
				// Login success - store address
				$user = JFactory::getUser();
				$row = JTable::getInstance('Eshop', 'Address');
				$row->bind($data);
				$row->customer_id = $user->get('id');
				$row->created_date = JFactory::getDate()->toSql();
				$row->modified_date = JFactory::getDate()->toSql();
				$row->store();
				$addressId = $row->id;
				// Store customer
				$row = JTable::getInstance('Eshop', 'Customer');
				$row->bind($data);
				$row->customer_id = $user->get('id');
				$customerGroupId = EshopHelper::getConfigValue('customergroup_id');
				$customerGroupDisplay = EshopHelper::getConfigValue('customer_group_display');
				if ($customerGroupDisplay != '')
				{
					$customerGroupDisplay = explode(',', $customerGroupDisplay);
					if (JRequest::getVar('customergroup_id') && in_array(JRequest::getVar('customergroup_id'), $customerGroupDisplay))
					{
						$customerGroupId = JRequest::getVar('customergroup_id');
					}
				}
				$row->customergroup_id = $customerGroupId;
				$row->address_id = $addressId;
				$row->published = 1;
				$row->created_date = JFactory::getDate()->toSql();
				$row->modified_date = JFactory::getDate()->toSql();
				$row->store();
				
				//Assign billing address
				$addressInfo = EshopHelper::getAddress($addressId);
				$session->set('payment_address_id', $addressId);
				if (count($addressInfo))
				{
					$session->set('payment_country_id', $addressInfo['country_id']);
					$session->set('payment_zone_id', $addressInfo['zone_id']);
				}
				else
				{
					$session->clear('payment_country_id');
					$session->clear('payment_zone_id');
				}
				if (isset($data['shipping_address']))
				{
					$session->set('shipping_address_id', $addressId);
					if (count($addressInfo))
					{
						$session->set('shipping_country_id', $addressInfo['country_id']);
						$session->set('shipping_zone_id', $addressInfo['zone_id']);
					}
					else
					{
						$session->clear('shipping_country_id');
						$session->clear('shipping_zone_id');
					}	
				}
			}
			else
			{
				$json['error']['warning'] = JText::_('ESHOP_WARNING_LOGIN_FAILED');
			}
			$session->clear('guest');
			$session->clear('shipping_method');
			$session->clear('shipping_methods');
			$session->clear('payment_method');
		}
		return $json;
	}
	
	/**
	 *
	 * Function to guest
	 * @param post array $data
	 * @return json array
	 */
	public function guest($data)
	{
		$cart = new EshopCart();
		$user = JFactory::getUser();
		$session = JFactory::getSession();
		$json = array();
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		// If user is already logged in, return to checkout page
		if ($user->get('id'))
		{
			if (EshopHelper::getConfigValue('active_https'))
			{
				$json['return'] = JRoute::_(EshopRoute::getViewRoute('checkout'), true, 1);
			}
			else
			{
				$json['return'] = JRoute::_(EshopRoute::getViewRoute('checkout'));
			}
		}
	
		// Validate products in the cart
		if (!$cart->hasProducts())
		{
			$json['return'] = JRoute::_(EshopRoute::getViewRoute('cart'));
		}
	
		if (!$json)
		{
			$fields = EshopHelper::getFormFields('B');
			$form = new RADForm($fields);
			if(!EshopHelper::hasZone($data['country_id']))
			{
				$form->removeRule('zone_id');
			}
			$valid = $form->validate($data);
			if (!$valid)
			{
				$json['error'] = $form->getErrors();
			}			
		}
		if (!$json)
		{
			$customerGroupId = EshopHelper::getConfigValue('customergroup_id');
			$customerGroupDisplay = EshopHelper::getConfigValue('customer_group_display');
			if ($customerGroupDisplay != '')
			{
				$customerGroupDisplay = explode(',', $customerGroupDisplay);
				if (JRequest::getVar('customergroup_id') && in_array(JRequest::getVar('customergroup_id'), $customerGroupDisplay))
				{
					$customerGroupId = JRequest::getVar('customergroup_id');
				}
			}
			// Set guest information session
			$guest = array();
			$guest['customer_id'] = 0;
			$guest['customergroup_id'] = $customerGroupId;
			$guest['firstname'] = $data['firstname'];
			$guest['lastname'] = $data['lastname'];
			$guest['email'] = $data['email'];
			$guest['telephone'] = $data['telephone'];
			$guest['fax'] = $data['fax'];
			
			// Set payment (billing) address session
			$guest['payment'] = array();			
			$billingFields = EshopHelper::getFormFields('B');
			foreach ($billingFields as $field)
			{
				$fieldName = $field->name;
				if (isset($data[$fieldName]))
				{
					$guest['payment'][$fieldName] = $data[$fieldName];
				}
				else 
				{
					$guest['payment'][$fieldName] = null;
				}				
			}						
			$countryInfo = EshopHelper::getCountry($data['country_id']);
			if (is_object($countryInfo))
			{
				$guest['payment']['country_name'] = $countryInfo->country_name;
				$guest['payment']['iso_code_2'] = $countryInfo->iso_code_2;
				$guest['payment']['iso_code_3'] = $countryInfo->iso_code_3;
			}
			else 
			{
				$guest['payment']['country_name'] = '';
				$guest['payment']['iso_code_2'] = '';
				$guest['payment']['iso_code_3'] = '';
			}
			
			$zoneInfo = EshopHelper::getZone($data['zone_id']);
			if (is_object($zoneInfo))
			{
				$guest['payment']['zone_name'] = $zoneInfo->zone_name;
				$guest['payment']['zone_code'] = $zoneInfo->zone_code;
			}
			else 
			{
				$guest['payment']['zone_name'] = '';
				$guest['payment']['zone_code'] = '';
			}
			// Default Payment Address
			$session->set('payment_country_id', $data['country_id']);
			$session->set('payment_zone_id', $data['zone_id']);
			
			// Set shipping address session
			if (isset($data['shipping_address']))
			{
				$guest['shipping_address'] = true;
			}
			else 
			{
				$guest['shipping_address'] = false;
			}
			if ($guest['shipping_address'])
			{
				$guest['shipping'] = array();
				
				$shippingFields = EshopHelper::getFormFields('S');
				foreach ($shippingFields as $field)
				{
					$fieldName = $field->name;
					if (isset($data[$fieldName]))
					{
						$guest['shipping'][$fieldName] = $data[$fieldName];
					}
					else
					{
						$guest['shipping'][$fieldName] = null;
					}
				}								
				if (is_object($countryInfo))
				{
					$guest['shipping']['country_name'] = $countryInfo->country_name;
					$guest['shipping']['iso_code_2'] = $countryInfo->iso_code_2;
					$guest['shipping']['iso_code_3'] = $countryInfo->iso_code_3;
				}
				else
				{
					$guest['shipping']['country_name'] = '';
					$guest['shipping']['iso_code_2'] = '';
					$guest['shipping']['iso_code_3'] = '';
				}
				
				if (is_object($zoneInfo))
				{
					$guest['shipping']['zone_name'] = $zoneInfo->zone_name;
					$guest['shipping']['zone_code'] = $zoneInfo->zone_code;
				}
				else
				{
					$guest['shipping']['zone_name'] = '';
					$guest['shipping']['zone_code'] = '';
				}
				
				// Default Shipping Address
				$session->set('shipping_country_id', $data['country_id']);
				$session->set('shipping_zone_id', $data['zone_id']);
			}
			else 
			{
				$tempGuest = $session->get('guest');
				if (isset($tempGuest['shipping']))
				{
					$guest['shipping'] = $tempGuest['shipping'];
				}
			}
			
			self::getCosts();
			$json['total'] = $this->total;
			$session->set('guest', $guest);
			$session->set('account', 'guest');
			$session->clear('shipping_method');
			$session->clear('shipping_methods');
			$session->clear('payment_method');
		}
		return $json;
	}
	
	/**
	 * 
	 * Function to process guest shipping
	 * @param array $data
	 * @return json array
	 */
	function processGuestShipping($data)
	{
		$cart = new EshopCart();
		$user = JFactory::getUser();
		$session = JFactory::getSession();
		$json = array();
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		// If user is already logged in, return to checkout page
		if ($user->get('id'))
		{
			if (EshopHelper::getConfigValue('active_https'))
			{
				$json['return'] = JRoute::_(EshopRoute::getViewRoute('checkout'), true, 1);
			}
			else
			{
				$json['return'] = JRoute::_(EshopRoute::getViewRoute('checkout'));
			}
		}
		
		// Validate products in the cart
		if (!$cart->hasProducts())
		{
			$json['return'] = JRoute::_(EshopRoute::getViewRoute('cart'));
		}
		
		if (!$json)
		{
			$fields = EshopHelper::getFormFields('S');
			$form = new RADForm($fields);
			if(!EshopHelper::hasZone($data['country_id']))
			{
				$form->removeRule('zone_id');
			}
			$valid = $form->validate($data);
			if (!$valid)
			{
				$json['error'] = $form->getErrors();
			}	
		}
		if (!$json)
		{
			$guest = $session->get('guest');
			$guest['shipping'] = array();			
			$shippingFields = EshopHelper::getFormFields('S');
			foreach ($shippingFields as $field)
			{
				$fieldName = $field->name;
				if (isset($data[$fieldName]))
				{
					$guest['shipping'][$fieldName] = $data[$fieldName];
				}
				else
				{
					$guest['shipping'][$fieldName] = null;
				}
			}		
			$countryInfo = EshopHelper::getCountry($data['country_id']);
			if (is_object($countryInfo))
			{
				$guest['shipping']['country_name'] = $countryInfo->country_name;
				$guest['shipping']['iso_code_2'] = $countryInfo->iso_code_2;
				$guest['shipping']['iso_code_3'] = $countryInfo->iso_code_3;
			}
			else
			{
				$guest['shipping']['country_name'] = '';
				$guest['shipping']['iso_code_2'] = '';
				$guest['shipping']['iso_code_3'] = '';
			}
		
			$zoneInfo = EshopHelper::getZone($data['zone_id']);
			if (is_object($zoneInfo))
			{
				$guest['shipping']['zone_name'] = $zoneInfo->zone_name;
				$guest['shipping']['zone_code'] = $zoneInfo->zone_code;
			}
			else
			{
				$guest['shipping']['zone_name'] = '';
				$guest['shipping']['zone_code'] = '';
			}
			$session->set('guest', $guest);
		
			// Default Shipping Address
			$session->set('shipping_country_id', $data['country_id']);
			$session->set('shipping_zone_id', $data['zone_id']);
			
			$session->clear('shipping_method');
			$session->clear('shipping_methods');
		}
		return $json;
	}

	/**
	 *
	 * Function to process payment address
	 * @param array $data        	
	 * @return json array
	 */
	function processPaymentAddress($data)
	{
		$cart = new EshopCart();
		$user = JFactory::getUser();
		$session = JFactory::getSession();
		$json = array();
		// If user is already logged in, return to checkout page
		if (!$user->get('id'))
		{
			if (EshopHelper::getConfigValue('active_https'))
			{
				$json['return'] = JRoute::_(EshopRoute::getViewRoute('checkout'), true, 1);
			}
			else
			{
				$json['return'] = JRoute::_(EshopRoute::getViewRoute('checkout'));
			}
		}
		
		// Validate products in the cart
		if (!$cart->hasProducts())
		{
			$json['return'] = JRoute::_(EshopRoute::getViewRoute('cart'));
		}
		$customerFirstname = '';
		$customerLastname = '';
		
		if (!$json)
		{
			// User choose an existing address
			if ($data['payment_address'] == 'existing')
			{
				if (!$data['address_id'])
				{
					$json['error']['warning'] = JText::_('ESHOP_ERROR_ADDRESS');
				}
				else
				{
					$addressInfo = EshopHelper::getAddress($data['address_id']);
					$customerFirstname = $addressInfo['firstname'];
					$customerLastname = $addressInfo['lastname'];
					$customerAddressId = $data['address_id'];
					$session->set('payment_address_id', $data['address_id']);
					if (count($addressInfo))
					{
						$session->set('payment_country_id', $addressInfo['country_id']);
						$session->set('payment_zone_id', $addressInfo['zone_id']);
					}
					else
					{
						$session->clear('payment_country_id');
						$session->clear('payment_zone_id');
					}
					$session->clear('payment_method');
				}
			}
			else
			{
				$fields = EshopHelper::getFormFields('B');
				$form = new RADForm($fields);
				if(!EshopHelper::hasZone($data['country_id']))
				{
					$form->removeRule('zone_id');
				}
				$valid = $form->validate($data);
				if (!$valid)
				{
					$json['error'] = $form->getErrors();
				}								
				if (!$json)
				{
					// Store new address
					$row = JTable::getInstance('Eshop', 'Address');
					$row->bind($data);
					$row->customer_id = $user->get('id');
					$row->created_date = JFactory::getDate()->toSql();
					$row->modified_date = JFactory::getDate()->toSql();
					$row->store();
					$addressId = $row->id;
					$customerFirstname = $data['firstname'];
					$customerLastname = $data['lastname'];
					$customerAddressId = $addressId;
					
					$session->set('payment_address_id', $addressId);
					$session->set('payment_country_id', $data['country_id']);
					$session->set('payment_zone_id', $data['zone_id']);
					$session->clear('payment_method');
				}
			}
		}
		if ($customerFirstname != '')
		{
			$customerId = $user->get('id');
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('id')
				->from('#__eshop_customers')
				->where('customer_id = ' . intval($customerId));
			$db->setQuery($query);
			if (!$db->loadResult())
			{
				$row = JTable::getInstance('Eshop', 'Customer');
				$row->id = '';
				$row->customer_id = $user->get('id');
				$row->customergroup_id = EshopHelper::getConfigValue('customergroup_id');
				$row->address_id = $customerAddressId;
				$row->firstname = $customerFirstname;
				$row->lastname = $customerLastname;
				$row->email = $user->get('email');
				$row->telephone = $data['telephone'];
				$row->fax = $data['fax'];
				$row->published = 1;
				$row->created_date = JFactory::getDate()->toSql();
				$row->modified_date = JFactory::getDate()->toSql();
				$row->store();
			}
		}
		self::getCosts();
		$json['total'] = $this->total;
		return $json;
	}

	/**
	 *
	 * Function to process shipping address
	 * @param array $data        	
	 * @return json array
	 */
	function processShippingAddress($data)
	{
		$cart = new EshopCart();
		$user = JFactory::getUser();
		$session = JFactory::getSession();
		$json = array();
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		// If user is already logged in, return to checkout page
		if (!$user->get('id'))
		{
			if (EshopHelper::getConfigValue('active_https'))
			{
				$json['return'] = JRoute::_(EshopRoute::getViewRoute('checkout'), true, 1);
			}
			else
			{
				$json['return'] = JRoute::_(EshopRoute::getViewRoute('checkout'));
			}
		}
		
		// Validate products in the cart
		if (!$cart->hasProducts())
		{
			$json['return'] = JRoute::_(EshopRoute::getViewRoute('cart'));
		}
		
		if (!$json)
		{
			// User choose an existing address
			if ($data['shipping_address'] == 'existing')
			{
				if (!$data['address_id'])
				{
					$json['error']['warning'] = JText::_('ESHOP_ERROR_ADDRESS');
				}
				else
				{
					$addressInfo = EshopHelper::getAddress($data['address_id']);
					$session->set('shipping_address_id', $data['address_id']);
					if (count($addressInfo))
					{
						$session->set('shipping_country_id', $addressInfo['country_id']);
						$session->set('shipping_zone_id', $addressInfo['zone_id']);
					}
					else
					{
						$session->clear('shipping_country_id');
						$session->clear('shipping_zone_id');
					}
					$session->clear('shipping_method');
					$session->clear('shipping_methods');
				}
			}
			else
			{
				$fields = EshopHelper::getFormFields('S');
				$form = new RADForm($fields);
				if(!EshopHelper::hasZone($data['country_id']))
				{
					$form->removeRule('zone_id');
				}
				$valid = $form->validate($data);
				if (!$valid)
				{
					$json['error'] = $form->getErrors();
				}				
				if (!$json)
				{
					// Store new address
					$row = JTable::getInstance('Eshop', 'Address');
					$row->bind($data);
					$row->customer_id = $user->get('id');
					$row->created_date = JFactory::getDate()->toSql();
					$row->modified_date = JFactory::getDate()->toSql();
					$row->store();
					$addressId = $row->id;
					
					$session->set('shipping_address_id', $addressId);
					$session->set('shipping_country_id', $data['country_id']);
					$session->set('shipping_zone_id', $data['zone_id']);
					$session->clear('shipping_method');
					$session->clear('shipping_methods');
				}
			}
		}
		return $json;
	}

	/**
	 * 
	 * Function to process shipping method
	 * @return json array
	 */
	function processShippingMethod()
	{
		$cart = new EshopCart();
		$user = JFactory::getUser();
		$session = JFactory::getSession();
		$json = array();
		// If shipping is not required, the customer shoud not have reached this page
		if (!$cart->hasShipping())
		{
			if (EshopHelper::getConfigValue('active_https'))
			{
				$json['return'] = JRoute::_(EshopRoute::getViewRoute('checkout'), true, 1);
			}
			else
			{
				$json['return'] = JRoute::_(EshopRoute::getViewRoute('checkout'));
			}
		}
		// Validate if shipping address has been set or not
		if ($user->get('id') && $session->get('shipping_address_id'))
		{
			$shippingAddress = EshopHelper::getAddress($session->get('shipping_address_id'));
		}
		else
		{
			$guest = $session->get('guest');
			$shippingAddress = isset($guest['shipping']) ? $guest['shipping'] : '';
		}
		if (empty($shippingAddress))
		{
			if (EshopHelper::getConfigValue('active_https'))
			{
				$json['return'] = JRoute::_(EshopRoute::getViewRoute('checkout'), true, 1);
			}
			else
			{
				$json['return'] = JRoute::_(EshopRoute::getViewRoute('checkout'));
			}
		}
		if (EshopHelper::getConfigValue('delivery_date'))
		{
			$deliveryDate = JRequest::getVar('delivery_date');
			if ($deliveryDate == '')
			{
				$json['error']['warning'] = JText::_('ESHOP_DELIVERY_DATE_PROMPT');
			}
			elseif ($deliveryDate < JHtml::_('date', '', 'Y-m-d'))
			{
				$json['error']['warning'] = JText::_('ESHOP_DELIVERY_DATE_WARNING');
			}
		}
		if (!$json)
		{
			if (!JRequest::getVar('shipping_method'))
			{
				$json['error']['warning'] = JText::_('ESHOP_ERROR_SHIPPING_METHOD');
			}
			else
			{
				$shippingMethod = explode('.', Jrequest::getVar('shipping_method'));
				$shippingMethods = $session->get('shipping_methods');
				if (isset($shippingMethods) && isset($shippingMethods[$shippingMethod[0]]))
				{
					$session->set('shipping_method', $shippingMethods[$shippingMethod[0]]['quote'][$shippingMethod[1]]);
					$session->set('delivery_date', JRequest::getVar('delivery_date'));
					$session->set('comment', JRequest::getVar('comment'));
					//Get total
					self::getCosts();
					$json['total'] = $this->total;
				}
				else
				{
					$json['error']['warning'] = JText::_('ESHOP_ERROR_SHIPPING_METHOD');
				}
			}
		}
		return $json;
	}
	
	/**
	 * Function to process payment method
	 */
	function processPaymentMethod()
	{
		$cart = new EshopCart();
		$user = JFactory::getUser();
		$session = JFactory::getSession();
		$json = array();
		// Validate if payment address has been set.
		if ($user->get('id') && $session->get('payment_address_id'))
		{
			$paymentAddress = EshopHelper::getAddress($session->get('payment_address_id'));
		}
		else
		{
			$guest = $session->get('guest');
			$paymentAddress = isset($guest['payment']) ? $guest['payment'] : '';
		}
		
		if (empty($paymentAddress))
		{
			if (EshopHelper::getConfigValue('active_https'))
			{
				$json['return'] = JRoute::_(EshopRoute::getViewRoute('checkout'), true, 1);
			}
			else
			{
				$json['return'] = JRoute::_(EshopRoute::getViewRoute('checkout'));
			}
		}
		
		//Validate if cart has products
		if (!$cart->hasProducts())
		{
			if (EshopHelper::getConfigValue('active_https'))
			{
				$json['return'] = JRoute::_(EshopRoute::getViewRoute('checkout'), true, 1);
			}
			else
			{
				$json['return'] = JRoute::_(EshopRoute::getViewRoute('checkout'));
			}
		}
			
		if (!$json) {
			$paymentMethod = JRequest::getVar('payment_method');
			if (!$paymentMethod)
			{
				$json['error']['warning'] = JText::_('ESHOP_ERROR_PAYMENT_METHOD');
			}
			else
			{
				$methods = os_payments::getPaymentMethods();
				$paymentMethods = array();
				for ($i = 0; $n = count($methods), $i < $n; $i++)
				{
					$paymentMethods[] = $methods[$i]->getName();
				}
				if (isset($paymentMethods) && in_array($paymentMethod, $paymentMethods))
				{
					$session->set('payment_method', $paymentMethod);
					//Check coupon
					$couponCode = JRequest::getVar('coupon_code');
					if ($couponCode != '')
					{
						$coupon = new EshopCoupon();
						$couponData = $coupon->getCouponData($couponCode);
						if (!count($couponData))
						{
							$couponInfo = $coupon->getCouponInfo($couponCode);
							if (is_object($couponInfo) && $couponInfo->coupon_per_customer && !$user->get('id'))
							{
								$json['error']['warning'] = JText::_('ESHOP_COUPON_IS_ONLY_FOR_REGISTERED_USER');
							}
							else 
							{
								$json['error']['warning'] = JText::_('ESHOP_COUPON_APPLY_ERROR');
							}
						}
						else
						{
							$session->set('coupon_code', $couponCode);
							$session->set('success', JText::_('ESHOP_COUPON_APPLY_SUCCESS'));
						}
					}
					//Check voucher
					$voucherCode = JRequest::getVar('voucher_code');
					if ($voucherCode != '')
					{
						$voucher = new EshopVoucher();
						$voucherData = $voucher->getVoucherData($voucherCode);
						if (!count($voucherData))
						{
							$json['error']['warning'] = JText::_('ESHOP_VOUCHER_APPLY_ERROR');
						}
						else
						{
							$session->set('voucher_code', $voucherCode);
							$session->set('success', JText::_('ESHOP_VOUCHER_APPLY_SUCCESS'));
						}
					}
					$donateAmount = floatval(JRequest::getVar('donate_amount'));
					$amount = 0;
					if ($donateAmount > 0)
					{
						$amount = $donateAmount;
					}
					elseif (JRequest::getVar('other_amount') > 0)
					{
						$amount = JRequest::getVar('other_amount');
					}
					if ($amount > 0)
					{
						$session->set('donate_amount', $amount);
					}
					else 
					{
						$session->clear('donate_amount');
					}
					$session->set('comment', JRequest::getVar('comment'));
					if (EshopHelper::getConfigValue('checkout_terms') && !JRequest::getVar('checkout_terms_agree'))
					{
						$json['error']['warning'] = JText::_('ESHOP_ERROR_CHECKOUT_TERMS_AGREE');
					}
				}
				else 
				{
					$json['error']['warning'] = JText::_('ESHOP_ERROR_PAYMENT_METHOD');
				}
			}
		}
		return $json;
	}
	
	/**
	 * Function to process order
	 */
	function processOrder($data)
	{
		jimport('joomla.user.helper');
		$session = JFactory::getSession();
		$cart = new EshopCart();
		$tax = new EshopTax(EshopHelper::getConfig());
		$currency = new EshopCurrency();
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		// Store Order
		$row = JTable::getInstance('Eshop', 'Order');
		$row->bind($data);
		$row->created_date = JFactory::getDate()->toSql();
		$row->modified_date = JFactory::getDate()->toSql();
		$row->modified_by = 0;
		$row->checked_out = 0;
		$row->checked_out_time = '0000-00-00 00:00:00';
		$row->store();
		$orderRow = $row;
		$orderId = $row->id;
		$session->set('order_id', $orderId);
		// Store Order Products, Order Options and Order Downloads
		foreach ($cart->getCartData() as $product)
		{
			// Order Products
			$row = JTable::getInstance('Eshop', 'Orderproducts');
			$row->id = '';
			$row->order_id = $orderId;
			$row->product_id = $product['product_id'];
			$row->product_name = $product['product_name'];
			$row->product_sku = $product['product_sku'];
			$row->quantity = $product['quantity'];
			$row->price = $product['price'];
			$row->total_price = $product['total_price'];
			$row->tax = $tax->getTax($product['price'], $product['product_taxclass_id']);
			$row->store();
			$orderProductId = $row->id;
			// Order Options
			foreach ($product['option_data'] as $option)
			{
				$row = JTable::getInstance('Eshop', 'Orderoptions');
				$row->id = '';
				$row->order_id = $orderId;
				$row->order_product_id = $orderProductId;
				$row->product_option_id = $option['product_option_id'];
				$row->product_option_value_id = $option['product_option_value_id'];
				$row->option_name = $option['option_name'];
				$row->option_value = $option['option_value'];
				$row->option_type = $option['option_type'];
				$row->sku = $option['sku'];
				$row->store();
			}
			// Order Downloads
			foreach ($product['download_data'] as $download)
			{
				$row = JTable::getInstance('Eshop', 'Orderdownloads');
				$row->id = '';
				$row->order_id = $orderId;
				$row->order_product_id = $orderProductId;
				$row->download_name = $download['download_name'];
				$row->filename = $download['filename'];
				//Generate download code
				$downloadCode = '';
				while (true)
				{
					$downloadCode = JUserHelper::genRandomPassword(10);
					$query->clear();
					$query->select('COUNT(*)')
						->from('#__eshop_orderdownloads')
						->where('download_code = "' . $downloadCode . '"');
					$db->setQuery($query);
					if (!$db->loadResult())
						break;
				}
				$row->download_code = $downloadCode;
				$row->remaining = $download['total_downloads_allowed'];
				$row->store();
			}
		}
		// Store Order Totals
		foreach ($data['totals'] as $total)
		{
			$row = JTable::getInstance('Eshop', 'Ordertotals');
			$row->id = '';
			$row->order_id = $orderId;
			$row->name = $total['name'];
			$row->title = $total['title'];
			$row->text = $total['text'];
			$row->value = $total['value'];
			$row->store();
		}
		JPluginHelper::importPlugin('eshop');
		$dispatcher = JDispatcher::getInstance();
		$dispatcher->trigger('onAfterStoreOrder', array($orderRow));
		$data['order_id'] = $orderId;
		// Prepare products data
		$productData = array();
		foreach ($cart->getCartData() as $product)
		{
			$optionData = array();
			foreach ($product['option_data'] as $option)
			{
				$optionData[] = array (
					'option_name'		=> $option['option_name'],
					'option_value'		=> $option['option_value']
				);
			}
			$productData[] = array (
				'product_name'		=> $product['product_name'],
				'product_sku'		=> $product['product_sku'],
				'option_data'		=> $optionData,
				'quantity'			=> $product['quantity'],
				'weight'			=> $product['weight'],
				'price'				=> $currency->format($product['price'], $data['currency_code'], '', false, '.', ',')
			);
		}
		//Get total for shipping, taxes
		$otherTotal = $currency->format($data['total'], $data['currency_code'], '', false, '.', ',') - $currency->format($cart->getSubTotal(), $data['currency_code'], '', false, '.', ',');
		$data['discount_amount_cart'] = 0;
		if ($otherTotal > 0)
		{
			$productData[] = array (
				'product_name'		=> 'Shipping, Discounts & Taxes',
				'product_sku'		=> '',
				'option_data'		=> array(),
				'quantity'			=> 1,
				'weight'			=> 0,
				'price'				=> $otherTotal
			);
		}
		else
		{
			$data['discount_amount_cart'] -= $otherTotal;
		}
		$data['products'] = $productData;
		if ($data['total'] > 0)
		{
			// Process Payment here
			$paymentMethod = $data['payment_method'];
			require_once JPATH_COMPONENT . '/plugins/payment/' . $paymentMethod . '.php';
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('params')
				->from('#__eshop_payments')
				->where('name = "' . $paymentMethod . '"');
			$db->setQuery($query);
			$plugin = $db->loadObject();
			$params = new JRegistry($plugin->params);
			$paymentClass = new $paymentMethod($params);
			$paymentClass->processPayment($data);
		}
		else
		{
			// If total = 0, then complete order
			$row = JTable::getInstance('Eshop', 'Order');
			$id = $data['order_id'];
			$row->load($id);
			$row->order_status_id = EshopHelper::getConfigValue('complete_status_id');
			$row->store();
			EshopHelper::completeOrder($row);
			JPluginHelper::importPlugin('eshop');
			$dispatcher = JDispatcher::getInstance();
			$dispatcher->trigger('onAfterCompleteOrder', array($row));
			//Send confirmation email here
			if (EshopHelper::getConfigValue('order_alert_mail'))
			{
				EshopHelper::sendEmails($row);
			}
			JFactory::getApplication()->redirect(JRoute::_(EshopRoute::getViewRoute('checkout').'&layout=complete'));
		}
	}
	
	/**
	 * Function to verify payment
	 */
	function verifyPayment()
	{
		$paymentMethod = JRequest::getVar('payment_method', '');
		$method = os_payments::getPaymentMethod($paymentMethod);
		$method->verifyPayment();
	}
	
	/**
	 *
	 * Function to get Cart Data
	 */
	function getCartData()
	{
		$cart = new EshopCart();
		if (!$this->cartData)
		{
			$this->cartData = $cart->getCartData();
		}
		return $this->cartData;
	}
	
	/**
	 *
	 * Function to get Costs
	 */
	function getCosts()
	{
		$totalData = array();
		$total = 0;
		$taxes = array();
		$this->getSubTotalCosts($totalData, $total, $taxes);
		$this->getVoucherCosts($totalData, $total, $taxes);
		$this->getShippingCosts($totalData, $total, $taxes);
		$this->getDonateCosts($totalData, $total, $taxes);
		$this->getCouponCosts($totalData, $total, $taxes);
		$this->getTaxesCosts($totalData, $total, $taxes);
		$this->getPaymentFeeCosts($totalData, $total, $taxes);
		$this->getTotalCosts($totalData, $total, $taxes);
		$this->totalData	= $totalData;
		$this->total		= $total;
		$this->taxes		= $taxes;
	}
	
	/**
	 *
	 * Function to get Sub Total Costs
	 * @param  array $totalData
	 * @param  float $total
	 * @param  array $taxes
	 */
	function getSubTotalCosts(&$totalData, &$total, &$taxes)
	{
		$cart = new EshopCart();
		$currency = new EshopCurrency();
		$total = $cart->getSubTotal();
		$totalData[] = array(
			'name'		=> 'sub_total',
			'title'		=> JText::_('ESHOP_SUB_TOTAL'),
			'text'		=> $currency->format(max(0, $total)),
			'value'		=> max(0, $total)
		);
		$taxes = $cart->getTaxes();
	}
	
	/**
	 *
	 * Function to get Coupon Costs
	 * @param  array $totalData
	 * @param  float $total
	 * @param  array $taxes
	 */
	function getCouponCosts(&$totalData, &$total, &$taxes)
	{
		$coupon = new EshopCoupon();
		$coupon->getCosts($totalData, $total, $taxes);
	}
	
	/**
	 *
	 * Function to get Voucher Costs
	 * @param  array $totalData
	 * @param  float $total
	 * @param  array $taxes
	 */
	function getVoucherCosts(&$totalData, &$total, &$taxes)
	{
		$voucher = new EshopVoucher();
		$voucher->getCosts($totalData, $total, $taxes);
	}
	
	/**
	 *
	 * Function to get Shipping Costs
	 * @param  array $totalData
	 * @param  float $total
	 * @param  array $taxes
	 */
	function getShippingCosts(&$totalData, &$total, &$taxes)
	{
		$shipping = new EshopShipping();
		$shipping->getCosts($totalData, $total, $taxes);
	}
	
	/**
	 * 
	 * Function to get Payment Fee Costs
	 * @param  array $totalData
	 * @param  float $total
	 * @param  array $taxes
	 */
	function getPaymentFeeCosts(&$totalData, &$total, &$taxes)
	{
		$payment = new EshopPayment();
		$payment->getCosts($totalData, $total, $taxes);
	}
	
	/**
	 *
	 * Function to get Donate Costs
	 * @param  array $totalData
	 * @param  float $total
	 * @param  array $taxes
	 */
	function getDonateCosts(&$totalData, &$total, &$taxes)
	{
		$donate = new EshopDonate();
		$donate->getCosts($totalData, $total, $taxes);
	}
	
	/**
	 *
	 * Function to get Taxes Costs
	 * @param  array $totalData
	 * @param  float $total
	 * @param  array $taxes
	 */
	function getTaxesCosts(&$totalData, &$total, &$taxes)
	{
		$tax = new EshopTax(EshopHelper::getConfig());
		$tax->getCosts($totalData, $total, $taxes);
	}
	
	/**
	 *
	 * Function to get Total Costs
	 * @param  array $totalData
	 * @param  float $total
	 * @param  array $taxes
	 */
	function getTotalCosts(&$totalData, &$total, &$taxes)
	{
		$currency = new EshopCurrency();
		$totalData[] = array(
			'name'		=> 'total',
			'title'		=> JText::_('ESHOP_TOTAL'),
			'text'		=> $currency->format(max(0, $total)),
			'value'		=> max(0, $total)
		);
	}
	
	/**
	 *
	 * Function to get Total Data
	 */
	public function getTotalData()
	{
		return $this->totalData;
	}
	
	/**
	 *
	 * Function to get Total
	 */
	function getTotal()
	{
		return $this->total;
	}
	
	/**
	 *
	 * Function to get Taxes
	 */
	function getTaxes()
	{
		return $this->taxes;
	}
}