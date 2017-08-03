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
class EShopViewProducts extends EShopViewList
{
	public function display($tpl = null)
	{
		$this->currency = new EshopCurrency();
		parent::display($tpl);
	}
	
	/**
	 * Build all the lists items used in the form and store it into the array
	 * @param  $lists
	 * @return boolean
	 */
	public function _buildListArray(&$lists, $state)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
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
		$options[] = JHtml::_('select.option', '0', JText::_('ESHOP_SELECT_A_CATEGORY'));
		foreach ($list as $listItem)
		{
			$options[] = JHtml::_('select.option', $listItem->id, $listItem->treename);
		}
		$lists['category_id'] = JHtml::_('select.genericlist', $options, 'category_id', 
			array(
				'option.text.toHtml' => false, 
				'option.text' => 'text', 
				'option.value' => 'value', 
				'list.attr' => ' class="inputbox" onchange="this.form.submit();"',
				'list.select' => $state->category_id));
		// Stock status list
		$options = array();
		$options[] = JHtml::_('select.option', '0', JText::_('ESHOP_SELECT_STOCK_STATUS'));
		$options[] = JHtml::_('select.option', '1', JText::_('ESHOP_IN_STOCK'));
		$options[] = JHtml::_('select.option', '2', JText::_('ESHOP_OUT_OF_STOCK'));
		$lists['stock_status'] = JHtml::_('select.genericlist', $options, 'stock_status',
			array(
				'option.text.toHtml' => false,
				'option.text' => 'text',
				'option.value' => 'value',
				'list.attr' => ' class="inputbox" onchange="this.form.submit();"',
				'list.select' => $state->stock_status));
	}

	/**
	 * Additional grid function for featured toggles
	 *
	 * @return string HTML code to write the toggle button
	 */
	function toggle($field, $i, $imgY = 'tick.png', $imgX = 'publish_x.png', $prefix = '')
	{
		$img = $field ? $imgY : $imgX;
		$task = $field ? 'product.unfeatured' : 'product.featured';
		$alt = $field ? JText::_('ESHOP_PRODUCT_FEATURED') : JText::_('ESHOP_PRODUCT_UNFEATURED');
		$action = $field ? JText::_('ESHOP_PRODUCT_UNFEATURED') : JText::_('ESHOP_PRODUCT_FEATURED');
		return ('<a href="#" onclick="return listItemTask(\'cb' . $i . '\',\'' . $task . '\')" title="' . $action . '">' .
			 JHtml::_('image', 'admin/' . $img, $alt, null, true) . '</a>');
	}
	
	public function featured($value = 0, $i, $canChange = true)
	{
		JHtml::_('bootstrap.tooltip');
		// Array of image, task, title, action
		$states	= array(
			0	=> array('unfeatured',	'product.featured',	'ESHOP_PRODUCT_UNFEATURED',	'ESHOP_PRODUCT_FEATURED'),
			1	=> array('featured',	'product.unfeatured',	'ESHOP_PRODUCT_FEATURED',		'ESHOP_PRODUCT_UNFEATURED'),
		);
		$state	= JArrayHelper::getValue($states, (int) $value, $states[1]);
		$icon	= $state[0];
		if ($canChange)
		{
			$html	= '<a href="#" onclick="return listItemTask(\'cb' . $i . '\',\'' . $state[1] . '\')" class="btn btn-micro hasTooltip' . ($value == 1 ? ' active' : '') . '" title="' . JHtml::tooltipText($state[3]) . '"><i class="icon-' . $icon . '"></i></a>';
		}
		else
		{
			$html	= '<a class="btn btn-micro hasTooltip disabled' . ($value == 1 ? ' active' : '') . '" title="' . JHtml::tooltipText($state[2]) . '"><i class="icon-' . $icon . '"></i></a>';
		}
		return $html;
	}
}