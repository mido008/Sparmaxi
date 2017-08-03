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
class EShopViewCategories extends EShopView
{

	function display($tpl = null)
	{
		$app = JFactory::getApplication();																						
		$params = $app->getParams();
		$model = $this->getModel();
		$title = $params->get('page_title', '');
		$session = JFactory::getSession();
		// Set title of the page
		$siteNamePosition = $app->getCfg('sitename_pagetitles');
		if ($siteNamePosition == 1)
		{
			$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
		}
		elseif ($siteNamePosition == 2)
		{
			$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
		}
		JFactory::getDocument()->setTitle($title);					
		JFactory::getSession()->set('continue_shopping_url', JUri::getInstance()->toString());
		if ($session->get('warning'))
		{
			$this->warning = $session->get('warning');
			$session->clear('warning');
		}
		$this->config = EshopHelper::getConfig();
		$this->items = $model->getData();
		$this->categoriesPerRow = EshopHelper::getConfigValue('items_per_row', 3);
		$this->pagination = $model->getPagination();
		$this->params = $params;
		parent::display($tpl);
	}
}