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
class EShopControllerCheckout extends JControllerLegacy
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
	 * Function to login user
	 */	
	function login()
	{
		$model = $this->getModel('Checkout');
		$json = $model->login();
		echo json_encode($json);
		JFactory::getApplication()->close();
	}
	
	/**
	 * Function to register user
	 */
	function register()
	{
		$post = JRequest::get('post', JREQUEST_ALLOWHTML);
		foreach ($post as $field => $value)
		{
			if (is_array($post[$field]))
			{
				$post[$field] = json_encode($value);
			}
		}
		$model = $this->getModel('Checkout');
		$json = $model->register($post);
		echo json_encode($json);
		JFactory::getApplication()->close();
	}
	
	/**
	 * Function to guest
	 */
	function guest()
	{
		$post = JRequest::get('post', JREQUEST_ALLOWHTML);
		foreach ($post as $field => $value)
		{
			if (is_array($post[$field]))
			{
				$post[$field] = json_encode($value);
			}
		}
		$model = $this->getModel('Checkout');
		$json = $model->guest($post);
		echo json_encode($json);
		JFactory::getApplication()->close();
	}
	
	/**
	 * Function to process guest shipping
	 */
	function processGuestShipping()
	{
		$post = JRequest::get('post', JREQUEST_ALLOWHTML);
		foreach ($post as $field => $value)
		{
			if (is_array($post[$field]))
			{
				$post[$field] = json_encode($value);
			}
		}
		$model = $this->getModel('Checkout');
		$json = $model->processGuestShipping($post);
		echo json_encode($json);
		JFactory::getApplication()->close();
	}
	
	/**
	 * Function to process payment address
	 */
	function processPaymentAddress()
	{
		$post = JRequest::get('post', JREQUEST_ALLOWHTML);
		foreach ($post as $field => $value)
		{
			if (is_array($post[$field]))
			{
				$post[$field] = json_encode($value);
			}
		}
		$model = $this->getModel('Checkout');
		$json = $model->processPaymentAddress($post);
		echo json_encode($json);
		JFactory::getApplication()->close();
	}
	
	/**
	 * Function to process shipping address
	 */
	function processShippingAddress()
	{
		$post = JRequest::get('post', JREQUEST_ALLOWHTML);
		foreach ($post as $field => $value)
		{
			if (is_array($post[$field]))
			{
				$post[$field] = json_encode($value);
			}
		}
		$model = $this->getModel('Checkout');
		$json = $model->processShippingAddress($post);
		echo json_encode($json);
		JFactory::getApplication()->close();
	}
	
	/**
	 * Function to process shipping method
	 */
	function processShippingMethod()
	{
		$model = $this->getModel('Checkout');
		$json = $model->processShippingMethod();
		echo json_encode($json);
		JFactory::getApplication()->close();
	}
	
	/**
	 * Function to process payment method
	 */
	function processPaymentMethod()
	{
		$model = $this->getModel('Checkout');
		$json = $model->processPaymentMethod();
		echo json_encode($json);
		JFactory::getApplication()->close();
	}
	
	/**
	 * 
	 * Function to validate captcha from checkout form
	 */
	function validateCaptcha()
	{
		$application = JFactory::getApplication();
		$json = array();
		if (EshopHelper::getConfigValue('enable_checkout_captcha'))
		{
			$captchaPlugin = $application->getParams()->get('captcha', JFactory::getConfig()->get('captcha'));
			if ($captchaPlugin == 'recaptcha')
			{
				$input = $application->input;
				$res = JCaptcha::getInstance($captchaPlugin)->checkAnswer($input->post->get('recaptcha_response_field', '', 'string'));
				if (!$res)
				{
					$json['error'] = JText::_('ESHOP_INVALID_CAPTCHA');
				}
			}
		}
		if (!$json)
			$json['success'] = TRUE;
		echo json_encode($json);
		$application->close();
	}
	
	/**
	 * Function to process order
	 */
	function processOrder()
	{
		$model = $this->getModel('Checkout');
		$cart = new EshopCart();
		$user = JFactory::getUser();
		$session = JFactory::getSession();
		// Get information for the order
		$cartData = $model->getCartData();
		$model->getCosts();
		$totalData = $model->getTotalData();
		$total = $model->getTotal();
		$taxes = $model->getTaxes();
		$return = '';
		if ($cart->hasShipping())
		{
			// Validate if shipping address is set
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
					$return = JRoute::_(EshopRoute::getViewRoute('checkout'), true, 1);
				}
				else 
				{
					$return = JRoute::_(EshopRoute::getViewRoute('checkout'));
				}
			}
			// Validate if shipping method is set
			if (!$session->get('shipping_method'))
			{
				if (EshopHelper::getConfigValue('active_https'))
				{
					$return = JRoute::_(EshopRoute::getViewRoute('checkout'), true, 1);
				}
				else 
				{
					$return = JRoute::_(EshopRoute::getViewRoute('checkout'));
				}
			}
		}
		else
		{
			$session->clear('shipping_method');
			$session->clear('shipping_methods');
		}
		
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
				$return = JRoute::_(EshopRoute::getViewRoute('checkout'), true, 1);
			}
			else 
			{
				$return = JRoute::_(EshopRoute::getViewRoute('checkout'));
			}
		}
		
		if ($total > 0)
		{
			// Validate if payment method has been set
			if (!$session->get('payment_method'))
			{
				if (EshopHelper::getConfigValue('active_https'))
				{
					$return = JRoute::_(EshopRoute::getViewRoute('checkout'), true, 1);
				}
				else
				{
					$return = JRoute::_(EshopRoute::getViewRoute('checkout'));
				}
			}
		}
		
		// Validate if cart has products
		if (!$cart->hasProducts())
		{
			$return = JRoute::_(EshopRoute::getViewRoute('cart'));
		}
		
		if (!$return)
		{
			$data = JRequest::get('post');
			// Prepare customer data
			if ($user->get('id'))
			{
				$data['customer_id'] = $user->get('id');
				$data['email'] = $user->get('email');
				$customer = EshopHelper::getCustomer($user->get('id'));
				if (is_object($customer))
				{
					$data['customergroup_id'] = $customer->customergroup_id;
					$data['firstname'] = $customer->firstname;
					$data['lastname'] = $customer->lastname;
					$data['telephone'] = $customer->telephone;
					$data['fax'] = $customer->fax;
				}
				else
				{
					$data['customergroup_id'] = '';
					$data['firstname'] = '';
					$data['lastname'] = '';
					$data['telephone'] = '';
					$data['fax'] = '';
				}
				$paymentAddress = EshopHelper::getAddress($session->get('payment_address_id'));
			}
			else
			{
				$data['customer_id'] = 0;
				$data['customergroup_id'] = $guest['customergroup_id'];
				$data['firstname'] = $guest['firstname'];
				$data['lastname'] = $guest['lastname'];
				$data['email'] = $guest['email'];
				$data['telephone'] = $guest['telephone'];
				$data['fax'] = $guest['fax'];
		
				$guest = $session->get('guest');
				$paymentAddress = $guest['payment'];
			}
		
			// Prepare payment data
			$billingFields = EshopHelper::getFormFields('B');
			foreach ($billingFields as $field)
			{
				$fieldName = $field->name;
				if (isset($paymentAddress[$fieldName]))
				{
					if (is_array($paymentAddress[$fieldName]))
					{
						$data['payment_'.$fieldName] = json_encode($paymentAddress[$fieldName]);
					}
					else 
					{
						$data['payment_'.$fieldName] = $paymentAddress[$fieldName];
					}
				}
				else 
				{
					$data['payment_'.$fieldName] = '';
				}
			}
			$data['payment_zone_name'] = $paymentAddress['zone_name'];
			$data['payment_country_name'] = $paymentAddress['country_name'];
			$data['payment_method'] = $session->get('payment_method');
			$data['payment_method_title'] = EshopHelper::getPaymentTitle($data['payment_method']);				
			// Prepare shipping data
			$shippingFields = EshopHelper::getFormFields('S');
			if ($cart->hasShipping())
			{
				if ($user->get('id')) {
					$shippingAddress = EshopHelper::getAddress($session->get('shipping_address_id'));
				}
				else
				{
					$guest = $session->get('guest');
					$shippingAddress = $guest['shipping'];
				}
				foreach ($shippingFields as $field)
				{
					$fieldName = $field->name;
					if (isset($shippingAddress[$fieldName]))
					{
						if (is_array($shippingAddress[$fieldName]))
						{
							$data['shipping_'.$fieldName] = json_encode($shippingAddress[$fieldName]);
						}
						else 
						{
							$data['shipping_'.$fieldName] = $shippingAddress[$fieldName];
						}
					}
					else 
					{
						$data['shipping_'.$fieldName] = '';
					}
				}
				$data['shipping_zone_name'] = $shippingAddress['zone_name'];				
				$data['shipping_country_name'] = $shippingAddress['country_name'];				
				$shippingMethod = $session->get('shipping_method');
				if (is_array($shippingMethod))
				{
					$data['shipping_method'] = $shippingMethod['name'];
					$data['shipping_method_title'] = $shippingMethod['title'];
				}
				else
				{
					$data['shipping_method'] = '';
					$data['shipping_method_title'] = '';
				}
			}
			else
			{
				foreach ($shippingFields as $field)
				{
					$fieldName = $field->name;
					$data['shipping_'.$fieldName] = '';
				}
				$data['shipping_zone_name'] = '';
				$data['shipping_country_name'] = '';
				$data['shipping_method'] = '';
				$data['shipping_method_title'] = '';
			}
			$data['totals'] = $totalData;
			$data['delivery_date'] = $session->get('delivery_date');
			$data['comment'] = $session->get('comment');
			$data['order_status_id'] = EshopHelper::getConfigValue('order_status_id');
			$data['language'] = JFactory::getLanguage()->getTag();
			$currency = new EshopCurrency();
			$data['currency_id'] = $currency->getCurrencyId();
			$data['currency_code'] = $currency->getCurrencyCode();
			if ($session->get('coupon_code'))
			{
				$coupon = EshopHelper::getCoupon($session->get('coupon_code'));
				$data['coupon_id'] = $coupon->id;
				$data['coupon_code'] = $coupon->coupon_code;
			}
			else 
			{
				$data['coupon_id'] = 0;
				$data['coupon_code'] = '';
			}
			if ($session->get('voucher_code'))
			{
				$voucher = EshopHelper::getVoucher($session->get('voucher_code'));
				$data['voucher_id'] = $voucher->id;
				$data['voucher_code'] = $voucher->voucher_code;
			}
			else
			{
				$data['voucher_id'] = 0;
				$data['voucher_code'] = '';
			}
			$data['currency_exchanged_value'] = $currency->getExchangedValue();
			$data['total'] = $total;
			$data['order_number'] = strtoupper(JUserHelper::genRandomPassword(10));
			$data['invoice_number'] = EshopHelper::getInvoiceNumber();
			$model->processOrder($data);
		}
		else
		{
			JFactory::getApplication()->redirect($return);
		}
	}
	
	/**
	 * Function to verify payment
	 */
	function verifyPayment()
	{
		$model = $this->getModel('Checkout');
		$model->verifyPayment();
	}
}