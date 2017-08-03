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
class EShopViewFrontpage extends EShopView
{

	function display($tpl = null)
	{
		$document = JFactory::getDocument();
		$document->addStyleSheet(JUri::base(true) . '/components/com_eshop/assets/colorbox/colorbox.css');
		$document->addStyleSheet(JUri::base(true) . '/components/com_eshop/assets/css/labels.css');
		$app = JFactory::getApplication('site');
		$params = $app->getParams();
		$title = $params->get('page_title', '');
		if ($title == '')
		{
			$title = JText::_('ESHOP_FRONT_PAGE');
		}
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
		$document->setTitle($title);
		// Set metakey, metadesc and robots
		if ($params->get('menu-meta_keywords'))
		{
			$document->setMetaData('keywords', $params->get('menu-meta_keywords'));
		}
		if ($params->get('menu-meta_description'))
		{
			$document->setMetaData('description', $params->get('menu-meta_description'));
		}
		if ($params->get('robots'))
		{
			$document->setMetadata('robots', $params->get('robots'));
		}
		$numberCategories = (int) $params->get('num_categories', 9);
		$numberProducts = (int) $params->get('num_products', 9);
		JLoader::register('EshopModelCategories', JPATH_ROOT . '/components/com_eshop/models/categories.php');
		JLoader::register('EshopModelProducts', JPATH_ROOT . '/components/com_eshop/models/products.php');
		if ($numberCategories > 0)
		{
			$categories = RADModel::getInstance('Categories', 'EshopModel', array('remember_states' => false))
				->limitstart(0)
				->limit($numberCategories)
				->filter_order('a.ordering')
				->getData();
		}
		else 
		{
			$categories = array();
		}
		if ($numberProducts > 0)
		{
			$products = RADModel::getInstance('Products', 'EshopModel', array('remember_states' => false))
				->limitstart(0)
				->limit($numberProducts)
				->product_featured(1)
				->sort_options('a.ordering-ASC')
				->getData();
		}
		else 
		{
			$products = array();
		}
		// Store session for Continue Shopping Url		
		JFactory::getSession()->set('continue_shopping_url', JUri::getInstance()->toString());
		$tax = new EshopTax(EshopHelper::getConfig());
		$currency = new EshopCurrency();
		$this->categories = $categories;
		$this->products = $products;
		$this->tax = $tax;
		$this->currency = $currency;
		$this->productsPerRow = EshopHelper::getConfigValue('items_per_row', 3);
		$this->categoriesPerRow = EshopHelper::getConfigValue('items_per_row', 3);
		parent::display($tpl);
	}
}