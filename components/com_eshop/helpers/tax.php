<?php
/**
 * @version		1.4.1
 * @package		Joomla
 * @subpackage	EShop
 * @author  	Giang Dinh Truong
 * @copyright	Copyright (C) 2012 Ossolution Team
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die();

/**
 * Tax helper class
 *
 */
class EshopTax
{

	/**
	 * 
	 * Shipping Address data 
	 * @var array(countryID, zoneId)
	 */
	protected $_shippingAddress;

	/**
	 * 
	 * Payment Address data
	 * @var array(countryID, zoneId)
	 */
	
	protected $_paymentAddress;

	/**
	 * 
	 * Store Address data
	 * @var array(countryID, zoneId)
	 */
	protected $_storeAddress;

	public function __construct($config = array())
	{
		$session = JFactory::getSession();
		if ($session->get('shipping_country_id') || $session->get('shipping_zone_id'))
		{
			$this->setShippingAddress($session->get('shipping_country_id'), $session->get('shipping_zone_id'));
		}
		elseif ($config->tax_default == 'shipping')
		{
			$this->setShippingAddress($config->country_id, $config->zone_id);
		}
		
		if ($session->get('payment_country_id') || $session->get('payment_zone_id'))
		{
			$this->setPaymentAddress($session->get('payment_country_id'), $session->get('payment_zone_id'));
		}
		elseif ($config->tax_default == 'payment')
		{
			$this->setPaymentAddress($config->country_id, $config->zone_id);
		}
		
		$this->setStoreAddress($config->country_id, $config->zone_id);
	}

	/**
	 * 
	 * Function to set Shipping Address
	 * @param int $countryId
	 * @param int $zoneId
	 */
	public function setShippingAddress($countryId, $zoneId)
	{
		$this->_shippingAddress = array('countryId' => $countryId, 'zoneId' => $zoneId);
	}

	/**
	 * 
	 * Function to set Payment Address
	 * @param int $countryId
	 * @param int $zoneId
	 */
	public function setPaymentAddress($countryId, $zoneId)
	{
		$this->_paymentAddress = array('countryId' => $countryId, 'zoneId' => $zoneId);
	}

	/**
	 * 
	 * Function to set Store Address 
	 * @param int $countryId
	 * @param int $zoneId
	 */
	public function setStoreAddress($countryId, $zoneId)
	{
		$this->_storeAddress = array('countryId' => $countryId, 'zoneId' => $zoneId);
	}

	/**
	 * 
	 * Function to get Costs, passed by reference to update
	 * @param  array $totalData
	 * @param  float $total
	 * @param  array $taxes
	 */
	public function getCosts(&$totalData, &$total, &$taxes)
	{
		$currency = new EshopCurrency();
		foreach ($taxes as $key => $value)
		{
			if ($value > 0)
			{
				$totalData[] = array(
					'name'		=> 'tax',
					'title'		=> $this->getTaxRateName($key),
					'text'		=> $currency->format($value),
					'value'		=> $value);
				$total += $value;
			}
		}
	}

	/**
	 * 
	 * Function to calculate value after tax for a specific value and tax class
	 * @param float $value
	 * @param int $taxClassId
	 * @param boolean $calculate
	 */
	public function calculate($value, $taxClassId, $calculate = true)
	{
		if ($taxClassId && $calculate)
		{
			$amount = $this->getTax($value, $taxClassId);
			return $value + $amount;
		}
		else
		{
			return $value;
		}
	}

	/**
	 * 
	 * Private method to get tax for a specific value and tax class
	 * @param float $value
	 * @param int $taxClassId
	 */
	public function getTax($value, $taxClassId)
	{
		$amount = 0;
		$taxRates = $this->getTaxRates($value, $taxClassId);
		foreach ($taxRates as $taxRate)
		{
			$amount += $taxRate['amount'];
		}
		return $amount;
	}

	/**
	 * 
	 * Function to get tax rates for a specific value and tax class
	 * @param float $value
	 * @param int $taxClassId
	 */
	public function getTaxRates($value, $taxClassId)
	{
		$user = JFactory::getUser();
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$taxRates = array();
		if ($user->get('id'))
		{
			$customer = new EshopCustomer();
			$customerGroupId = $customer->getCustomerGroupId();
		}
		else
		{
			$customerGroupId = EshopHelper::getConfigValue('customergroup_id');
		}
		if (!$customerGroupId)
			$customerGroupId = 0;
		
		//Based on Shipping Address
		if ($this->_shippingAddress)
		{
			$query->select('tr.priority, t.*')
				->from('#__eshop_taxrules AS tr')
				->innerJoin('#__eshop_taxes AS t ON (tr.tax_id = t.id)')
				->innerJoin('#__eshop_taxcustomergroups AS tcg ON (t.id = tcg.tax_id)')
				->innerJoin('#__eshop_geozones AS gz ON (t.geozone_id = gz.id)')
				->innerJoin('#__eshop_geozonezones AS gzz ON (gz.id = gzz.geozone_id)')
				->where('tr.taxclass_id = ' . intval($taxClassId))
				->where('gzz.country_id = ' . intval($this->_shippingAddress['countryId']))
				->where('(gzz.zone_id = ' . intval($this->_shippingAddress['zoneId']) . ' OR gzz.zone_id = 0)')
				->where('tcg.customergroup_id = ' . intval($customerGroupId))
				->where('tr.based_on = "shipping"')
				->order('tr.priority');
			$db->setQuery($query);
			$rows = $db->loadObjectList();
			for ($i = 0; $n = count($rows), $i < $n; $i++)
			{
				$row = $rows[$i];
				$taxRates[$row->id] = array(
					'tax_rate_id' => $row->id, 
					'tax_name' => $row->tax_name, 
					'tax_rate' => $row->tax_rate, 
					'tax_type' => $row->tax_type, 
					'priority' => $row->priority);
			}
		}
		
		//Based on Payment Address
		if ($this->_paymentAddress)
		{
			$query->clear();
			$query->select('tr.priority, t.*')
				->from('#__eshop_taxrules AS tr')
				->innerJoin('#__eshop_taxes AS t ON (tr.tax_id = t.id)')
				->innerJoin('#__eshop_taxcustomergroups AS tcg ON (t.id = tcg.tax_id)')
				->innerJoin('#__eshop_geozones AS gz ON (t.geozone_id = gz.id)')
				->innerJoin('#__eshop_geozonezones AS gzz ON (gz.id = gzz.geozone_id)')
				->where('tr.taxclass_id = ' . intval($taxClassId))
				->where('gzz.country_id = ' . intval($this->_paymentAddress['countryId']))
				->where('(gzz.zone_id = ' . intval($this->_paymentAddress['zoneId']) . ' OR gzz.zone_id = 0)')
				->where('tcg.customergroup_id = ' . intval($customerGroupId))
				->where('tr.based_on = "payment"')
				->order('tr.priority');
			$db->setQuery($query);
			$rows = $db->loadObjectList();
			for ($i = 0; $n = count($rows), $i < $n; $i++)
			{
				$row = $rows[$i];
				$taxRates[$row->id] = array(
					'tax_rate_id' => $row->id, 
					'tax_name' => $row->tax_name, 
					'tax_rate' => $row->tax_rate, 
					'tax_type' => $row->tax_type, 
					'priority' => $row->priority);
			}
		}
		
		//Based on Store Address
		if ($this->_storeAddress)
		{
			$query->clear();
			$query->select('tr.priority, t.*')
				->from('#__eshop_taxrules AS tr')
				->innerJoin('#__eshop_taxes AS t ON (tr.tax_id = t.id)')
				->innerJoin('#__eshop_taxcustomergroups AS tcg ON (t.id = tcg.tax_id)')
				->innerJoin('#__eshop_geozones AS gz ON (t.geozone_id = gz.id)')
				->innerJoin('#__eshop_geozonezones AS gzz ON (gz.id = gzz.geozone_id)')
				->where('tr.taxclass_id = ' . intval($taxClassId))
				->where('gzz.country_id = ' . intval($this->_storeAddress['countryId']))
				->where('(gzz.zone_id = ' . intval($this->_storeAddress['zoneId']) . ' OR gzz.zone_id = 0)')
				->where('tcg.customergroup_id = ' . intval($customerGroupId))
				->where('tr.based_on = "store"')
				->order('tr.priority');
			$db->setQuery($query);
			$rows = $db->loadObjectList();
			for ($i = 0; $n = count($rows), $i < $n; $i++)
			{
				$row = $rows[$i];
				$taxRates[$row->id] = array(
					'tax_rate_id' => $row->id, 
					'tax_name' => $row->tax_name, 
					'tax_rate' => $row->tax_rate, 
					'tax_type' => $row->tax_type, 
					'priority' => $row->priority);
			}
		}
		$taxRatesData = array();
		foreach ($taxRates as $taxRate)
		{
			if (isset($taxRatesData[$taxRate['tax_rate_id']]))
			{
				$amount = $taxRatesData[$taxRate['tax_rate_id']]['amount'];
			}
			else
			{
				$amount = 0;
			}
			if ($taxRate['tax_type'] == 'F')
			{
				$amount += $taxRate['tax_rate'];
			}
			elseif ($taxRate['tax_type'] == 'P')
			{
				$amount += ($value / 100 * $taxRate['tax_rate']);
			}
			$taxRatesData[$taxRate['tax_rate_id']] = array(
				'tax_rate_id' => $taxRate['tax_rate_id'], 
				'tax_name' => $taxRate['tax_name'], 
				'tax_rate' => $taxRate['tax_rate'], 
				'tax_type' => $taxRate['tax_type'], 
				'amount' => $amount);
		}
		return $taxRatesData;
	}

	/**
	 * 
	 * Function to get name of a specific tax rate
	 * @param int $taxRateId
	 */
	public function getTaxRateName($taxRateId)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('tax_name')
			->from('#__eshop_taxes')
			->where('id = ' . intval($taxRateId));
		$db->setQuery($query);
		return $db->loadResult();
	}
}