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
class EshopViewCountry extends EShopViewForm
{
	function _buildListArray(&$lists, $item)
	{
		$lists['published'] = JHtml::_('select.booleanlist', 'published', ' class="inputbox" ', $item->published);
		$lists['postcode_required'] = JHtml::_('select.booleanlist', 'postcode_required', ' class="inputbox" ', $item->postcode_required);
		return true;
	}
}