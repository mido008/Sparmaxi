<?php
/**
 * @version		1.4.1
 * @package		Joomla
 * @subpackage	EShop - Quantity Shipping
 * @author  	Giang Dinh Truong
 * @copyright	Copyright (C) 2012 Ossolution Team
 * @license		GNU/GPL, see LICENSE.php
 */
// no direct access
defined( '_JEXEC' ) or die();

class eshop_quantity extends eshop_shipping
{

	/**
	 *
	 * Constructor function
	 */
	function eshop_quantity()
	{
		parent::setName('eshop_quantity');
		parent::eshop_shipping();
	}
	
	/**
	 * 
	 * Function tet get quote for quantity shipping
	 * @param array $addressData
	 * @param object $params
	 */
	function getQuote($addressData, $params)
	{
		//Check geozone condition
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		if (!$params->get('geozone_id'))
		{
			$status = true;
		}
		else
		{
			$query->select('COUNT(*)')
				->from('#__eshop_geozonezones')
				->where('geozone_id = ' . intval($params->get('geozone_id')))
				->where('country_id = ' . intval($addressData['country_id']))
				->where('(zone_id = 0 OR zone_id = ' . intval($addressData['zone_id']) . ')');
			$db->setQuery($query);
			if ($db->loadResult())
			{
				$status = true;
			}
			else
			{
				$status = false;
			}
		}
		$methodData = array();
		if($status)
		{
			$cart = new EshopCart();
			$currency = new EshopCurrency();
			$tax = new EshopTax();
			$rates = explode("|", $params->get('rates'));
			$quantity = $cart->countProducts();
			for ($i = 0; $n = count($rates), $i < $n; $i++)
			{
				$data = explode(";", $rates[$i]);
				if (isset($data[0]) && $data[0] >= $quantity)
				{
					if (isset($data[1]))
					{
						$cost = $data[1];
						break;
					}
				}
			}
			$packageFee = $params->get('package_fee', 0);
			$cost = $cost + $packageFee;
			
			$query->clear();
			$query->select('*')
				->from('#__eshop_shippings')
				->where('name = "eshop_quantity"');
			$db->setQuery($query);
			$row = $db->loadObject();
			$quoteData['quantity'] = array(
				'name'			=> 'eshop_quantity.quantity', 
				'title'			=> JText::_('PLG_ESHOP_QUANTITY_DESC'), 
				'cost'			=> $cost, 
				'taxclass_id' 	=> $params->get('taxclass_id'), 
				'text'			=> $currency->format($tax->calculate($cost, $params->get('taxclass_id'), EshopHelper::getConfigValue('tax'))));
			
			$methodData = array(
				'name'		=> 'eshop_quantity',
				'title'		=> JText::_('PLG_ESHOP_QUANTITY_TITLE'),
				'quote'		=> $quoteData,
				'ordering'	=> $row->ordering,
				'error'		=> false);
		}
		return $methodData;
	}
}