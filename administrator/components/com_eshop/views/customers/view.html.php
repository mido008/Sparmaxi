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
class EshopViewCustomers extends EShopViewList
{
	/**
	 * Build the toolbar for view list
	 */
	public function _buildToolbar()
	{
		$viewName = $this->getName();
		$controller = EShopInflector::singularize($this->getName());
		JToolBarHelper::title(JText::_($this->lang_prefix.'_'.strtoupper($viewName)));
	
		$canDo	= EshopHelper::getActions($viewName);
	
		if ($canDo->get('core.delete'))
			JToolBarHelper::deleteList(JText::_($this->lang_prefix.'_DELETE_'.strtoupper($this->getName()).'_CONFIRM') , $controller.'.remove');
		if ($canDo->get('core.edit'))
			JToolBarHelper::editList($controller.'.edit');
		if ($canDo->get('core.create')) {
			JToolBarHelper::addNew($controller.'.add');
		}
		if ($canDo->get('core.edit.state')) {
			JToolBarHelper::publishList($controller.'.publish');
			JToolBarHelper::unpublishList($controller.'.unpublish');
		}
	}
}