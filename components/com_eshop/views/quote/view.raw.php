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
 *
 * @package Joomla
 * @subpackage EShop
 * @since 1.5
 */
class EShopViewQuote extends EShopView
{

	function display($tpl = null)
	{
		switch ($this->getLayout())
		{
			case 'mini':
				$this->_displayMini($tpl);
				break;
			case 'popout':
				$this->_displayPopout($tpl);
				break;
			default:
				break;
		}
	}

	/**
	 *
	 * @param string $tpl        	
	 */
	function _displayMini($tpl = null)
	{
		//Get quote data
		$quote = new EshopQuote();
		$items = $quote->getQuoteData();
		$countProducts = $quote->countProducts();
		$this->items = $items;
		$this->countProducts = $countProducts;
		parent::display($tpl);
	}
	
	function _displayPopout($tpl = null)
	{
		$document = JFactory::getDocument();
		$document->addStyleSheet(JUri::base(true).'/components/com_eshop/assets/colorbox/colorbox.css');
		$session = JFactory::getSession();
		$tax = new EshopTax(EshopHelper::getConfig());
		$quote = new EshopQuote();
		$currency = new EshopCurrency();
		$quoteData = $this->get('QuoteData');
		$this->quoteData = $quoteData;
		$this->tax = $tax;
		$this->currency = $currency;
		// Success message
		if ($session->get('success'))
		{
			$this->success = $session->get('success');
			$session->clear('success');
		}
		parent::display($tpl);
	}
}