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
//defined('_JEXEC') or die();

class EshopAPI
{
	/***
	 * Method to check if a customer exist in Eshop or not
	 * 
	 * @return bool
	 */
	public static function customerExist($userId)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('COUNT(*)')
			->from('#__eshop_customers')
			->where('id = '.(int)$userId);
		$db->setQuery($query);
		$total = $db->loadResult();
		if ($total)
		{
			return true;
		}
		else 
		{
			return false;	
		}		
	}
	/**
	 * 
	 * Function to add a customer
	 * @param int $userId
	 * @param array $data (customergroup_id, firstname, lastname, email, telephone, tax, company, company_id, address_1, address_2, city, postcode, country_code (iso code 3), zone_code)
	 */
	public static function addCustomer($userId, $data)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id')
			->from('#__eshop_customers')
			->where('customer_id = ' . intval($userId));
		$db->setQuery($query);
		$id = $db->loadResult();
		$row = JTable::getInstance('Eshop', 'Customer');
		if ($id)
		{
			if (isset($data['customergroup_id']) && $data['customergroup_id'] > 0)
			{
				$row->load($id);
				$row->customergroup_id = $data['customergroup_id'];
				$row->store();				
			}
		}
		else 
		{
			// Store address for customer first
			$query->clear();
			$query->delete('#__eshop_addresses')
				->where('customer_id = ' . intval($userId));
			$db->setQuery($query);
			$db->execute();
			$addressRow = JTable::getInstance('Eshop', 'Address');
			$addressRow->bind($data);
			$addressRow->customer_id = $userId;
			if (isset($data['country_code']) && $data['country_code'] != '')
			{
				$query->clear();
				$query->select('id')
					->from('#__eshop_countries')
					->where('iso_code_3 = "' . $data['country_code'] . '"');
				$db->setQuery($query);
				$countryId = $db->loadResult();
				if ($countryId > 0)
					$addressRow->country_id = $countryId; 
			}
			if (isset($data['zone_code']) && $data['zone_code'] != '')
			{
				$query->clear();
				$query->select('id')
					->from('#__eshop_zones')
					->where('zone_code = "' . $data['zone_code'] . '"');
				$db->setQuery($query);
				$zoneId = $db->loadResult();
				if ($zoneId > 0)
					$addressRow->zone_id = $zoneId;
			}
			$addressRow->created_date = JFactory::getDate()->toSql();
			$addressRow->modified_date = JFactory::getDate()->toSql();
			$addressRow->store();
			$addressId = $addressRow->id;
			// Store customer
			$row->bind($data);
			$row->customer_id = $userId;
			if (!$row->customergroup_id)
			{
				$row->customergroup_id = EshopHelper::getConfigValue('customergroup_id');
			}
			$row->address_id = $addressId;
			$row->created_date = JFactory::getDate()->toSql();
			$row->modified_date = JFactory::getDate()->toSql();
			$row->published = 1;
			$row->store();
		}
	}
	
	/**
	 * 
	 * Function to get customer groups in EShop
	 * @return customer groups object list
	 */
	public static function getCustomerGroups()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('a.id, b.customergroup_name')
			->from('#__eshop_customergroups AS a')
			->innerJoin('#__eshop_customergroupdetails AS b ON (a.id = b.customergroup_id)')
			->where('a.published = 1')
			->where('b.language = "' . JComponentHelper::getParams('com_languages')->get('site', 'en-GB') . '"');
		$db->setQuery($query);
		return $db->loadObjectList();
	}
	
	
	/**
	 * 
	 * Function to set customer group for a specific customer
	 * @param int $userId
	 * @param int $customerGroupId
	 */
	public static function setCustomerGroup($userId, $customerGroupId)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->update('#__eshop_customers')
			->set('customergroup_id = ' . intval($customerGroupId))
			->where('customer_id = ' . intval($userId));
		$db->setQuery($query);
		$db->execute();		
	}
}