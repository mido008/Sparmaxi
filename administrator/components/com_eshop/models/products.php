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
class EShopModelProducts extends EShopModelList
{
	/**
	 *
	 * Constructor
	 * 
	 * @param array $config        	
	 */
	function __construct($config)
	{
		$config['search_fields'] = array('a.product_sku', 'b.product_name', 'b.product_short_desc', 'b.product_desc');
		$config['translatable'] = true;
		$config['state_vars'] = array(
			'category_id' => array('', 'cmd', 1), 
			'stock_status' => array('', 'cmd', 1));
		$config['translatable_fields'] = array(
			'product_name', 
			'product_alias', 
			'product_desc', 
			'product_short_desc', 
			'meta_key', 
			'meta_desc');
		parent::__construct($config);
	}
	
	/**
	 * Get total entities
	 *
	 * @return int
	 */
	public function getTotal()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->_total))
		{
			$db = $this->getDbo();
			$state = $this->getState();
			$where = $this->_buildContentWhereArray();
			$query = $db->getQuery(true);
			$query->select('COUNT(*)');
			if ($this->translatable)
			{
				$query->from($this->mainTable . ' AS a ')
					->innerJoin(EShopInflector::singularize($this->mainTable).'details AS b ON (a.id = b.' . EShopInflector::singularize($this->name) . '_id)');
			}
			else
			{
				$query->from($this->mainTable . ' AS a ');
			}
			if ($state->category_id)
			{
				$query->innerJoin('#__eshop_productcategories AS c ON (a.id = c.product_id)');
			}
			if ($state->stock_status == '1')
			{
				$where[] = 'a.product_quantity > 0';
			}
			elseif ($state->stock_status == '2')
			{
				$where[] = 'a.product_quantity <= 0';
			}
			if (count($where))
				$query->where($where);
				
			$db->setQuery($query);
			$this->_total = $db->loadResult();
		}
		return $this->_total;
	}
	
	/**
	 * Basic build Query function.
	 * The child class must override it if it is necessary
	 *
	 * @return string
	 */
	public function _buildQuery()
	{
		$db = $this->getDbo();
		$state = $this->getState();
		$query = $db->getQuery(true);
		if ($this->translatable)
		{
			$query->select('a.*, ' . implode(', ', $this->translatableFields))
				->from($this->mainTable . ' AS a ')
				->innerJoin(EShopInflector::singularize($this->mainTable).'details AS b ON (a.id = b.' . EShopInflector::singularize($this->name) . '_id)');
		}
		else
		{
			$query->select('a.*')
				->from($this->mainTable . ' AS a ');
		}
		$where = $this->_buildContentWhereArray();
		if ($state->category_id)
		{
			$query->innerJoin('#__eshop_productcategories AS c ON (a.id = c.product_id)');
		}
		if (count($where))
			$query->where($where);
		$orderby = $this->_buildContentOrderBy();
		if ($orderby != '')
			$query->order($orderby);
		return $query;
	}
	
	/**
	 * Build an where clause array
	 *
	 * @return array
	 */
	public function _buildContentWhereArray()
	{
		$db = $this->getDbo();
		$state = $this->getState();
		$where = array();
		if ($state->filter_state == 'P')
			$where[] = ' a.published=1 ';
		elseif ($state->filter_state == 'U')
		$where[] = ' a.published = 0';
	
		if ($state->search)
		{
			$search = $db->quote('%' . $db->escape($state->search, true) . '%', false);
			if (is_array($this->searchFields))
			{
				$whereOr = array();
				foreach ($this->searchFields as $titleField)
				{
					$whereOr[] = " LOWER($titleField) LIKE " . $search;
				}
				$where[] = ' (' . implode(' OR ', $whereOr) . ') ';
			}
			else
			{
				$where[] = 'LOWER(' . $this->searchFields . ') LIKE ' . $db->quote('%' . $db->escape($state->search, true) . '%', false);
			}
		}
	
		if ($this->translatable)
		{
			$where[] = 'b.language = "' . JComponentHelper::getParams('com_languages')->get('site', 'en-GB') . '"';
		}
	
		if ($state->category_id)
		{
			$where[] = 'c.category_id = ' . $state->category_id;
		}
		
		if ($state->stock_status == '1')
		{
			$where[] = 'a.product_quantity > 0';
		}
		elseif ($state->stock_status == '2')
		{
			$where[] = 'a.product_quantity <= 0';
		}

		return $where;
	}
}