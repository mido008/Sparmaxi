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
 * Eshop Component Model
 *
 * @package		Joomla
 * @subpackage	EShop
 * @since 1.5
 */
class EShopModelLength extends EShopModel
{
	public function __construct($config)
	{
		$config['translatable'] = true;
		$config['translatable_fields'] = array('length_name', 'length_unit');
		parent::__construct($config);
	}
	
	/**
	 * Method to remove lengths
	 *
	 * @access	public
	 * @return boolean True on success
	 * @since	1.5
	 */
	public function delete($cid = array())
	{
		if (count($cid))
		{
			$db = $this->getDbo();
			$cids = implode(',', $cid);
			$query = $db->getQuery(true);
			$query->delete('#__eshop_lengths')
				->where('id IN (' . $cids . ')')
				->where('id NOT IN (SELECT  DISTINCT(product_length_id) FROM #__eshop_products)');
			$db->setQuery($query);
			if (!$db->query())
				//Removed error
				return 0;
			$numItemsDeleted = $db->getAffectedRows();
			//Delete details records
			$query->clear();
			$query->delete('#__eshop_lengthdetails')
				->where('length_id IN (' . $cids . ')')
				->where('length_id NOT IN (SELECT  DISTINCT(product_length_id) FROM #__eshop_products)');
			$db->setQuery($query);
			if (!$db->query())
				//Removed error
				return 0;
			if ($numItemsDeleted < count($cid))
			{
				//Removed warning
				return 2;
			}
		}
		//Removed success
		return 1;
	}
}