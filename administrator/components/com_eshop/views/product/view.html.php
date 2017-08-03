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
 * HTML View class for EShop component
 *
 * @static
 * @package		Joomla
 * @subpackage	EShop
 * @since 1.5
 */
class EShopViewProduct extends EShopViewForm
{

	function _buildListArray(&$lists, $item)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$nullDate = $db->getNullDate();
		//Build manufacturer list
		$query->select('a.id AS value, b.manufacturer_name AS text')
			->from('#__eshop_manufacturers AS a')
			->innerJoin('#__eshop_manufacturerdetails AS b ON (a.id = b.manufacturer_id)')
			->where('a.published = 1')
			->where('b.language = "' . JComponentHelper::getParams('com_languages')->get('site', 'en-GB') . '"')
			->order('a.ordering');
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		$options = array();
		$options[] = JHtml::_('select.option', 0, JText::_('ESHOP_NONE'), 'value', 'text');
		if (count($rows))
		{
			$options = array_merge($options, $rows);
		}
		$lists['manufacturer'] = JHtml::_('select.genericlist', $options, 'manufacturer_id', 
			array(
				'option.text.toHtml' => false, 
				'option.value' => 'value', 
				'option.text' => 'text', 
				'list.attr' => ' class="inputbox chosen" ', 
				'list.select' => $item->manufacturer_id));
		//Build categories list
		$query->clear();
		$query->select('a.id, b.category_name AS title, a.category_parent_id AS parent_id')
			->from('#__eshop_categories AS a')
			->innerJoin('#__eshop_categorydetails AS b ON (a.id = b.category_id)')
			->where('a.published = 1')
			->where('b.language = "' . JComponentHelper::getParams('com_languages')->get('site', 'en-GB') . '"');
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		$children = array();
		if ($rows)
		{
			// first pass - collect children
			foreach ($rows as $v)
			{
				$pt = $v->parent_id;
				$list = @$children[$pt] ? $children[$pt] : array();
				array_push($list, $v);
				$children[$pt] = $list;
			}
		}
		$list = JHtml::_('menu.treerecurse', 0, '', array(), $children, 9999, 0, 0);
		$options = array();
		foreach ($list as $listItem)
		{
			$options[] = JHtml::_('select.option', $listItem->id, $listItem->treename);
		}
		$productCategories = EshopHelper::getProductCategories($item->id);
		$productCategoriesArr = array();
		for ($i = 0; $n = count($productCategories), $i < $n; $i++)
		{
			$productCategoriesArr[] = $productCategories[$i]->id;
		}
		$lists['categories'] = JHtml::_('select.genericlist', $options, 'category_id[]', 
			array(
				'option.text.toHtml' => false, 
				'option.text' => 'text', 
				'option.value' => 'value', 
				'list.attr' => ' class="inputbox chosen" multiple ', 
				'list.select' => $productCategoriesArr));
		//Build customer groups list
		$query->clear();
		$query->select('a.id AS value, b.customergroup_name AS text')
			->from('#__eshop_customergroups AS a')
			->innerJoin('#__eshop_customergroupdetails AS b ON (a.id = b.customergroup_id)')
			->where('a.published = 1')
			->where('b.language = "' . JComponentHelper::getParams('com_languages')->get('site', 'en-GB') . '"')
			->order('b.customergroup_name');
		$db->setQuery($query);
		$options = $db->loadObjectList();
		if ($item->product_customergroups != '')
		{
			$selectedItems = explode(',', $item->product_customergroups);
		}
		else
		{
			$selectedItems = array();
		}
		$lists['product_customergroups'] = JHtml::_('select.genericlist', $options, 'product_customergroups[]',
			array(
				'option.text.toHtml' => false,
				'option.text' => 'text',
				'option.value' => 'value',
				'list.attr' => ' class="inputbox chosen" multiple ',
				'list.select' => $selectedItems));
		//Build Lengths list
		$query->clear();
		$query->select('a.id, b.length_name')
			->from('#__eshop_lengths AS a')
			->innerJoin('#__eshop_lengthdetails AS b ON (a.id = b.length_id)')
			->where('a.published = 1')
			->where('b.language = "' . JComponentHelper::getParams('com_languages')->get('site', 'en-GB') . '"');
		$db->setQuery($query);
		$lists['product_length_id'] = JHtml::_('select.genericlist', $db->loadObjectList(), 'product_length_id', ' class="inputbox" ', 'id', 'length_name', $item->product_length_id);
		//Build Weights list
		$query->clear();
		$query->select('a.id, b.weight_name')
			->from('#__eshop_weights AS a')
			->innerJoin('#__eshop_weightdetails AS b ON (a.id = b.weight_id)')
			->where('a.published = 1')
			->where('b.language = "' . JComponentHelper::getParams('com_languages')->get('site', 'en-GB') . '"');
		$db->setQuery($query);
		$lists['product_weight_id'] = JHtml::_('select.genericlist', $db->loadObjectList(), 'product_weight_id', ' class="inputbox" ', 'id', 'weight_name', $item->product_weight_id);
		//Build downloads
		$query->clear();
		$query->select('a.id AS value, b.download_name AS text')
			->from('#__eshop_downloads AS a')
			->innerJoin('#__eshop_downloaddetails AS b ON (a.id = b.download_id)')
			->where('b.language = "' . JComponentHelper::getParams('com_languages')->get('site', 'en-GB') . '"');
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		$productDownloads = EshopHelper::getProductDownloads($item->id);
		$productDownloadsArr = array();
		for ($i = 0; $n = count($productDownloads), $i < $n; $i++)
		{
			$productDownloadsArr[] = $productDownloads[$i]->id;
		}
		if (count($rows))
		{
			$lists['product_downloads'] = JHtml::_('select.genericlist', $rows, 'product_downloads_id[]',
				array(
					'option.text.toHtml' => false,
					'option.text' => 'text',
					'option.value' => 'value',
					'list.attr' => ' class="inputbox chosen" multiple ',
					'list.select' => $productDownloadsArr));
		}
		else
		{
			$lists['product_downloads'] = '';
		}
		//Build related products list
		$query->clear();
		$query->select('a.id AS value, b.product_name AS text')
			->from('#__eshop_products AS a')
			->innerJoin('#__eshop_productdetails AS b ON (a.id = b.product_id)')
			->where('a.published = 1')
			->where('b.language = "' . JComponentHelper::getParams('com_languages')->get('site', 'en-GB') . '"')
			->order('b.product_name');		
		if ($item->id)
		{
			$query->where('a.id != ' . $item->id);
		}
		$query->order('a.ordering');
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		$productRelations = EshopHelper::getProductRelations($item->id);
		$productRelationsArr = array();
		for ($i = 0; $n = count($productRelations), $i < $n; $i++)
		{
			$productRelationsArr[] = $productRelations[$i]->id;
		}
		if (count($rows))
		{
			$lists['related_products'] = JHtml::_('select.genericlist', $rows, 'related_product_id[]', 
				array(
					'option.text.toHtml' => false, 
					'option.text' => 'text', 
					'option.value' => 'value', 
					'list.attr' => ' class="inputbox chosen" multiple ', 
					'list.select' => $productRelationsArr));
		}
		else
		{
			$lists['related_products'] = '';
		}
		//Build attributes list
		$query->clear();
		$query->select('a.id, b.attributegroup_name')
			->from('#__eshop_attributegroups AS a')
			->innerJoin('#__eshop_attributegroupdetails AS b ON (a.id = b.attributegroup_id)')
			->where('a.published = 1')
			->where('b.language = "' . JComponentHelper::getParams('com_languages')->get('site', 'en-GB') . '"')
			->order('a.ordering');
		$db->setQuery($query);
		$attributeGroups = $db->loadObjectList();
		$attributeGroupsArr = array();
		for ($i = 0; $n = count($attributeGroups), $i < $n; $i++)
		{
			$query->clear();
			$query->select('a.id, b.attribute_name')
				->from('#__eshop_attributes AS a')
				->innerJoin('#__eshop_attributedetails AS b ON (a.id = b.attribute_id)')
				->where('a.attributegroup_id = ' . intval($attributeGroups[$i]->id))
				->where('a.published = 1')
				->where('b.language = "' . JComponentHelper::getParams('com_languages')->get('site', 'en-GB') . '"')
				->order('a.ordering');
			$db->setQuery($query);
			$attributes = $db->loadObjectList();
			if (count($attributes))
			{
				$attributeGroupsArr[addcslashes($attributeGroups[$i]->attributegroup_name, "'")] = array();
			}
			for ($j = 0; $m = count($attributes), $j < $m; $j++)
			{
				$attributeGroupsArr[addcslashes($attributeGroups[$i]->attributegroup_name, "'")][] = JHtml::_('select.option', $attributes[$j]->id, 
					addcslashes($attributes[$j]->attribute_name, "'"));
			}
		}
		$lists['attributes'] = JHtml::_('select.groupedlist', $attributeGroupsArr, 'attribute_id[]', 
			array(
				'list.attr' => ' class="inputbox" ', 
				'id' => null, 
				'list.select' => null, 
				'group.items' => null, 
				'option.key.toHtml' => false, 
				'option.text.toHtml' => false));
		$productAttributes = EshopHelper::getProductAttributes($item->id);
		for ($i = 0; $n = count($productAttributes), $i < $n; $i++)
		{
			$productAttribute = $productAttributes[$i];
			$lists['attributes_' . $productAttribute->id] = JHtml::_('select.groupedlist', $attributeGroupsArr, 'attribute_id[]', 
				array(
					'list.attr' => ' class="inputbox" ', 
					'id' => null, 
					'list.select' => $productAttribute->id, 
					'group.items' => null, 
					'option.key.toHtml' => false, 
					'option.text.toHtml' => false));
		}
		$this->productAttributes = $productAttributes;
		$this->productImages = EshopHelper::getProductImages($item->id);
		//Build options list
		$query->clear();
		$query->select('a.id AS value, b.option_name AS text')
			->from('#__eshop_options AS a')
			->innerJoin('#__eshop_optiondetails AS b ON (a.id = b.option_id)')
			->where('a.published = 1')
			->where('a.id NOT IN (SELECT option_id FROM #__eshop_productoptions WHERE product_id = ' . ($item->id ? $item->id : 0) . ')')
			->where('b.language = "' . JComponentHelper::getParams('com_languages')->get('site', 'en-GB') . '"')
			->order('b.option_name');
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		$options = array();
		$options[] = JHtml::_('select.option', 0, JText::_('ESHOP_NONE'), 'value', 'text');
		if (count($rows))
		{
			$options = array_merge($options, $rows);
		}
		$lists['options'] = JHtml::_('select.genericlist', $options, 'option_id', 
			array(
				'option.text.toHtml' => false, 
				'option.value' => 'value', 
				'option.text' => 'text', 
				'list.attr' => ' class="inputbox" onchange="addProductOption();"'));
		//Build options type list
		$query->clear();
		$query->select('id AS value, option_type AS text')
			->from('#__eshop_options')
			->where('published = 1')
			->order('ordering');
		$db->setQuery($query);
		$lists['options_type'] = JHtml::_('select.genericlist', $db->loadObjectList(), 'option_type_id',
				array(
					'option.text.toHtml' => false,
					'option.value' => 'value',
					'option.text' => 'text',
					'list.attr' => ' class="inputbox" style="display: none;"'));
		//Build product options data
		$productOptions = EshopHelper::getProductOptions($item->id);
		$this->productOptions = $productOptions;
		$productOptionValues = array();
		for ($i = 0; $n = count($productOptions), $i < $n; $i++)
		{
			$productOptionValues[] = EshopHelper::getProductOptionValues($item->id, $productOptions[$i]->id);
		}
		$this->productOptionValues = $productOptionValues;
		//Build option values data
		$query->clear();
		$query->select('id')
			->from('#__eshop_options')
			->where('published = 1')
			->order('ordering');
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		$this->options = $rows;
		$subRows = array();
		for ($i = 0; $n = count($rows), $i < $n; $i++)
		{
			$query->clear();
			$query->select('a.id AS value, b.value AS text')
				->from('#__eshop_optionvalues AS a')
				->innerJoin('#__eshop_optionvaluedetails AS b ON (a.id = b.optionvalue_id)')
				->where('a.option_id = ' . intval($rows[$i]->id))
				->where('b.language = "' . JComponentHelper::getParams('com_languages')->get('site', 'en-GB') . '"')
				->order('a.ordering');
			$db->setQuery($query);
			$subRows[$rows[$i]->id] = $db->loadObjectList();
			$lists['option_values_' . $rows[$i]->id] = JHtml::_('select.genericlist', $subRows[$rows[$i]->id], 'option_values_' . $rows[$i]->id, 
				array(
					'option.text.toHtml' => false, 
					'option.value' => 'value', 
					'option.text' => 'text', 
					'list.attr' => ' class="inputbox" style="display: none;" '));
		}
		$signOptions = array();
		$signOptions[] = JHtml::_('select.option', '+', '+');
		$signOptions[] = JHtml::_('select.option', '-', '-');
		$lists['price_sign'] = JHtml::_('select.genericlist', $signOptions, 'price_sign', 
			array(
				'option.text.toHtml' => false, 
				'option.value' => 'value', 
				'option.text' => 'text', 
				'list.attr' => ' class="inputbox" style="display: none;" '));
		$lists['weight_sign'] = JHtml::_('select.genericlist', $signOptions, 'weight_sign', 
			array(
				'option.text.toHtml' => false, 
				'option.value' => 'value', 
				'option.text' => 'text', 
				'list.attr' => ' class="inputbox" style="display: none;" '));
		for ($i = 0; $n = count($productOptions), $i < $n; $i++)
		{
			$productOptionValues = EshopHelper::getProductOptionValues($item->id, $productOptions[$i]->id);
			if ($productOptions[$i]->option_type == 'Text' || $productOptions[$i]->option_type == 'Textarea'){
			    $j = 0;			    
			    $lists['price_sign_t_' . $productOptions[$i]->id] = JHtml::_('select.genericlist', $signOptions,
			        'optionvalue_' . $productOptions[$i]->id . '_price_sign[]',
			        array(
			            'option.text.toHtml' => false,
			            'option.value' => 'value',
			            'option.text' => 'text',
			            'list.attr' => ' class="inputbox" style="width:auto;"',
			            'list.select' => $productOptionValues[$j]->price_sign));
			    continue;
			}
			for ($j = 0; $m = count($productOptionValues), $j < $m; $j++)
			{
				if (!isset($subRows[$productOptions[$i]->id]))
				{
					$query->clear();
					$query->select('a.id AS value, b.value AS text')
						->from('#__eshop_optionvalues AS a')
						->innerJoin('#__eshop_optionvaluedetails AS b ON (a.id = b.optionvalue_id)')
						->where('a.option_id = ' . intval($productOptions[$i]->id))
						->where('b.language = "' . JComponentHelper::getParams('com_languages')->get('site', 'en-GB') . '"')
						->order('a.ordering');
					$db->setQuery($query);
					$subRows[$productOptions[$i]->id] = $db->loadObjectList();
				}
				$lists['product_option_value_' . $productOptionValues[$j]->id] = JHtml::_('select.genericlist', $subRows[$productOptions[$i]->id], 
					'optionvalue_' . $productOptions[$i]->id . '_id[]', 
					array(
						'option.text.toHtml' => false, 
						'option.value' => 'value', 
						'option.text' => 'text', 
						'list.attr' => ' class="inputbox" ', 
						'list.select' => $productOptionValues[$j]->option_value_id));
				$lists['price_sign_' . $productOptionValues[$j]->id] = JHtml::_('select.genericlist', $signOptions, 
					'optionvalue_' . $productOptions[$i]->id . '_price_sign[]', 
					array(
						'option.text.toHtml' => false, 
						'option.value' => 'value', 
						'option.text' => 'text', 
						'list.attr' => ' class="inputbox" ', 
						'list.select' => $productOptionValues[$j]->price_sign));
				$lists['weight_sign_' . $productOptionValues[$j]->id] = JHtml::_('select.genericlist', $signOptions, 
					'optionvalue_' . $productOptions[$i]->id . '_weight_sign[]', 
					array(
						'option.text.toHtml' => false, 
						'option.value' => 'value', 
						'option.text' => 'text', 
						'list.attr' => ' class="inputbox" ', 
						'list.select' => $productOptionValues[$j]->weight_sign));
			
			}
		}
		// Get product tags
		$productTags = EshopHelper::getProductTags($item->id);
		$productTagsArr = array();
		if (count($productTags))
		{
			for ($i = 0; $n = count($productTags), $i < $n; $i++)
			{
				$productTagsArr[] = $productTags[$i]->tag_name;
			}
			$item->product_tags = implode(',', $productTagsArr);
		}
		else 
		{
			$item->product_tags = '';
		}
		//Discounts
		$this->productDiscounts = EshopHelper::getProductDiscounts($item->id);
		$this->productSpecials = EshopHelper::getProductSpecials($item->id);
		//Build customer groups list
		$query->clear();
		$query->select('a.id AS value, b.customergroup_name AS text')
			->from('#__eshop_customergroups AS a')
			->innerJoin('#__eshop_customergroupdetails AS b ON (a.id = b.customergroup_id)')
			->where('a.published = 1')
			->where('b.language = "' . JComponentHelper::getParams('com_languages')->get('site', 'en-GB') . '"')
			->order('b.customergroup_name');
		$db->setQuery($query);
		$customerGroups = $db->loadObjectList();
		$lists['discount_customer_group'] = JHtml::_('select.genericlist', $customerGroups, 'discount_customergroup_id[]', 
			array('option.text.toHtml' => false, 'option.value' => 'value', 'option.text' => 'text', 'list.attr' => ' class="inputbox" '));
		$lists['special_customer_group'] = JHtml::_('select.genericlist', $customerGroups, 'special_customergroup_id[]', 
			array('option.text.toHtml' => false, 'option.value' => 'value', 'option.text' => 'text', 'list.attr' => ' class="inputbox" '));
		for ($i = 0; $n = count($this->productDiscounts), $i < $n; $i++)
		{
			$productDiscount = $this->productDiscounts[$i];
			$lists['discount_customer_group_' . $productDiscount->id] = JHtml::_('select.genericlist', $customerGroups, 
				'discount_customergroup_id[]', 
				array(
					'option.text.toHtml' => false, 
					'option.value' => 'value', 
					'option.text' => 'text', 
					'list.attr' => ' class="inputbox" ', 
					'list.select' => $productDiscount->customergroup_id));
		}
		for ($i = 0; $n = count($this->productSpecials), $i < $n; $i++)
		{
			$productSpecial = $this->productSpecials[$i];
			$lists['special_customer_group_' . $productSpecial->id] = JHtml::_('select.genericlist', $customerGroups, 'special_customergroup_id[]', 
				array(
					'option.text.toHtml' => false, 
					'option.value' => 'value', 
					'option.text' => 'text', 
					'list.attr' => ' class="inputbox" ', 
					'list.select' => $productSpecial->customergroup_id));
		}
		parent::_buildListArray($lists, $item);
		//Build shipping list
		$lists['product_shipping'] = JHtml::_('select.booleanlist', 'product_shipping', ' class="inputbox" ', $item->id ? $item->product_shipping : 1);	
		//Build featured  list
		$lists['featured'] = JHtml::_('select.booleanlist', 'product_featured', ' class="inputbox" ', $item->product_featured);
		//Build call for price list
		$lists['product_call_for_price'] = JHtml::_('select.booleanlist', 'product_call_for_price', ' class="inputbox" ', $item->product_call_for_price);
		//Build taxclasses list
		$query->clear();
		$query->select('id AS value, taxclass_name AS text')
			->from('#__eshop_taxclasses')
			->where('published = 1');
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		$options = array();
		$options[] = JHtml::_('select.option', 0, JText::_('ESHOP_NONE'), 'value', 'text');
		if (count($rows))
		{
			$options = array_merge($options, $rows);
		}
		$lists['taxclasses'] = JHtml::_('select.genericlist', $options, 'product_taxclass_id', 
			array(
				'option.text.toHtml' => false, 
				'option.value' => 'value', 
				'option.text' => 'text', 
				'list.attr' => ' class="inputbox" ', 
				'list.select' => $item->product_taxclass_id));
		//Stock status list
		$query->clear();
		$query->select('a.id, b.stockstatus_name')
			->from('#__eshop_stockstatuses AS a')
			->innerJoin('#__eshop_stockstatusdetails AS b ON (a.id = b.stockstatus_id)')
			->where('a.published = 1')
			->where('b.language = "' . JComponentHelper::getParams('com_languages')->get('site', 'en-GB') . '"');
		$db->setQuery($query);
		$lists['product_stock_status_id'] = JHtml::_('select.genericlist', $db->loadObjectList(), 'product_stock_status_id', ' class="inputbox" ', 'id', 'stockstatus_name', isset($item->product_stock_status_id) ? $item->product_stock_status_id : '1');
		//Quote mode
		$lists['product_quote_mode'] = JHtml::_('select.booleanlist', 'product_quote_mode', ' class="inputbox" ', $item->product_quote_mode);
		$this->nullDate = $nullDate;
	}
	
	/**
	 * Build the toolbar for product edit form
	 */
	public function _buildToolbar()
	{
		$viewName = $this->getName();
		$canDo = EshopHelper::getActions($viewName);
		$edit = JRequest::getVar('edit');
		$text = $edit ? JText::_($this->lang_prefix . '_EDIT') : JText::_($this->lang_prefix . '_NEW');
		if ($edit)
		{
			if (isset($this->item->product_sku) && $this->item->product_sku != '')
			{
				$productInfo = ' - [ ' . $this->item->product_sku . ' ]';
				if (isset($this->item->product_name) && $this->item->product_name != '')
				{
					$productInfo .= ' - [ ' . $this->item->product_name . ' ]';
				}
			}
			else 
			{
				$productInfo = ' [ ' . $this->item->product_name . ' ]';
			}
		}
		else 
		{
			$productInfo = '';
		}	
		JToolBarHelper::title(JText::_($this->lang_prefix . '_' . $viewName) . ': <small><small>[ ' . $text . ' ]' . $productInfo . '</small></small>');
		if ($canDo->get('core.edit') || $canDo->get('core.create'))
		{
			JToolBarHelper::apply($viewName . '.apply');
			JToolBarHelper::save($viewName . '.save');
			JToolBarHelper::save2new($viewName . '.save2new');
		}
		if ($edit)
			JToolBarHelper::cancel($viewName . '.cancel', 'JTOOLBAR_CLOSE');
		else
			JToolBarHelper::cancel($viewName . '.cancel');
	}
}