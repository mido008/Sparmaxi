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

class os_payments
{
    public static $methods;
	/**
	 * Get list of payment methods
	 *
	 * @return array
	 */
	public static function getPaymentMethods()
	{
		if (self::$methods == null)
		{
			$session = JFactory::getSession();
			$user = JFactory::getUser();
			if ($user->get('id') && $session->get('payment_address_id'))
			{
				$paymentAddress = EshopHelper::getAddress($session->get('payment_address_id'));
			}
			else
			{
				$guest = $session->get('guest');
				$paymentAddress = isset($guest['payment']) ? $guest['payment'] : '';
			}
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('*')
				->from('#__eshop_payments')
				->where('published = 1')
                ->order('ordering');
			$db->setQuery($query);
			$rows = $db->loadObjectList();
			foreach ($rows as $row)
			{
				if (file_exists(JPATH_ROOT . '/components/com_eshop/plugins/payment/' . $row->name . '.php'))
				{
                    require_once JPATH_ROOT . '/components/com_eshop/plugins/payment/' . $row->name . '.php';
                    $params = new JRegistry($row->params);
                    $status = true;
                    if ($params->get('geozone_id', '0'))
                    {
                    	$query->clear();
                    	$query->select('COUNT(*)')
                    		->from('#__eshop_geozonezones')
                    		->where('geozone_id = ' . intval($params->get('geozone_id')))
                    		->where('country_id = ' . intval($paymentAddress['country_id']))
                    		->where('(zone_id = 0 OR zone_id = ' . intval($paymentAddress['zone_id']) . ')');
                    	$db->setQuery($query);
                    	if (!$db->loadResult())
                    	{
                    		$status = false;
                    	}
                    }
                    if ($status)
                    {
                    	$method = new $row->name($params);
                    	$method->title = JText::_($row->title);
                    	self::$methods[] = $method;
                    }
				}
			}
		}

		return self::$methods;
	}

	/**
	 * Load information about the payment method
	 *
	 * @param string $name
	 * Name of the payment method
	 */
	public static function loadPaymentMethod($name)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			->from('#__eshop_payments')
			->where('name = "' . $name . '"');
		$db->setQuery($query);
		return $db->loadObject();
	}

	/**
	 * Get default payment gateway
	 *
	 * @return string
	 */
	public static function getDefautPaymentMethod()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('name')
			->from('#__eshop_payments')
			->where('published = 1')
			->order('ordering');
		$db->setQuery($query, 0, 1);
		return $db->loadResult();
	}

	/**
	 * Get the payment method object based on it's name
	 *
	 * @param string $name        	
	 * @return object
	 */
	public static function getPaymentMethod($name)
	{
		$methods = self::getPaymentMethods();
		foreach ($methods as $method)
		{
			if ($method->getName() == $name)
			{
				return $method;
			}
		}
		return null;
	}
}
?>