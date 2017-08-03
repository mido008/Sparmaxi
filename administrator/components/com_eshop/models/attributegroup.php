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
class EShopModelAttributegroup extends EShopModel
{
	public function __construct($config)
	{
		$config['translatable'] = true;
		$config['translatable_fields'] = array('attributegroup_name');
		parent::__construct($config);
	}
	
	/**
	 * Method to remove attributegroups
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
			$query->delete('#__eshop_attributegroups')
				->where('id IN (' . $cids . ')')
				->where('id NOT IN (SELECT  DISTINCT(attributegroup_id) FROM #__eshop_attributes)');
			$db->setQuery($query);
			if (!$db->query())
				//Removed error
				return 0;
			$numItemsDeleted = $db->getAffectedRows();
			//Delete details records
			$query->clear();
			$query->delete('#__eshop_attributegroupdetails')
				->where('attributegroup_id IN (' . $cids . ')')
				->where('attributegroup_id NOT IN (SELECT  DISTINCT(attributegroup_id) FROM #__eshop_attributes)');
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