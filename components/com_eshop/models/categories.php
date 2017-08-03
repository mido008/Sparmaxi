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
class EshopModelCategories extends RADModelList
{

	public function __construct($config = array())
	{
		$config['translatable'] = true;
		$config['translatable_fields'] = array(
			'category_name', 
			'category_alias', 
			'category_page_title', 
			'category_page_heading', 
			'category_desc', 
			'meta_key', 
			'meta_desc');		
		parent::__construct($config);
		$app = JFactory::getApplication();
		$listLength = EshopHelper::getConfigValue('catalog_limit');
		if (!$listLength)
		{
			$listLength = $app->getCfg('list_limit');
		}		
		$this->state->insert('id', 'int', 0)
			->insert('limit', 'int', $listLength);
		$request = EshopHelper::getRequestData();
		$this->state->setData($request);		
		if ($app->input->getCmd('view') == 'categories')
		{
			$app->setUserState('limit', $this->state->limit);
		}
	}

	/**
	 * Method to get categories data
	 *
	 * @access public
	 * @return array
	 */
	public function getData()
	{
		// Lets load the content if it doesn't already exist
		if (empty($this->data))
		{
			$rows = parent::getData();
			$imageSizeFunction = EshopHelper::getConfigValue('category_image_size_function', 'resizeImage');
			$imageCategoryWidth = EshopHelper::getConfigValue('image_category_width');			
			$imageCategoryHeight = EshopHelper::getConfigValue('image_category_height');
			for ($i = 0; $n = count($rows), $i < $n; $i++)
			{
				$row = $rows[$i];
				if ($row->category_image && JFile::exists(JPATH_ROOT . '/media/com_eshop/categories/' . $row->category_image))
				{
					$image = call_user_func_array(array('EshopHelper', $imageSizeFunction), 
						array($row->category_image, JPATH_ROOT . '/media/com_eshop/categories/', $imageCategoryWidth, $imageCategoryHeight));
				}
				else
				{
					$image = call_user_func_array(array('EshopHelper', $imageSizeFunction), 
						array('no-image.png', JPATH_ROOT . '/media/com_eshop/categories/', $imageCategoryWidth, $imageCategoryHeight));
				}
				$row->image = JUri::base(true) . '/media/com_eshop/categories/resized/' . $image;
			}
			$this->data = $rows;
		}
		return $this->data;
	}
	/**
	 * Override BuildQueryWhere method
	 * @see RADModelList::_buildQueryWhere()
	 */
	protected function _buildQueryWhere(JDatabaseQuery $query)
	{
		parent::_buildQueryWhere($query);
		$query->where('a.category_parent_id=' . $this->state->id);
		//Check viewable of customer groups
		$user = JFactory::getUser();
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
		$query->where('((a.category_customergroups = "") OR (a.category_customergroups IS NULL) OR (a.category_customergroups = "' . $customerGroupId . '") OR (a.category_customergroups LIKE "' . $customerGroupId . ',%") OR (a.category_customergroups LIKE "%,' . $customerGroupId . ',%") OR (a.category_customergroups LIKE "%,' . $customerGroupId . '"))');
		return $this;
	}
}