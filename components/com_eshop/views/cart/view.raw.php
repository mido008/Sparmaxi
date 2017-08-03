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
class EShopViewCart extends EShopView
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
		//Get cart data
		$cart = new EshopCart();
		$items = $cart->getCartData();
		$countProducts = $cart->countProducts();
		$currency = new EshopCurrency();
		$tax = new EshopTax(EshopHelper::getConfig());
		
		$model = $this->getModel();
		$model->getCosts();
		$totalData = $model->getTotalData();
		$totalPrice = $currency->format($model->getTotal());
		
		$this->items = $items;
		$this->countProducts = $countProducts;
		$this->totalData = $totalData;
		$this->totalPrice = $totalPrice;
		$this->currency = $currency;
		$this->tax = $tax;
		parent::display($tpl);
	}
	
	/**
	 *
	 * @param string $tpl
	 */
	function _displayPopout($tpl = null)
	{
		$document = JFactory::getDocument();
		$document->addStyleSheet(JUri::base(true).'/components/com_eshop/assets/colorbox/colorbox.css');
		$session = JFactory::getSession();
		$tax = new EshopTax(EshopHelper::getConfig());
		$cart = new EshopCart();
		$currency = new EshopCurrency();
		$cartData = $this->get('CartData');
		$model = $this->getModel();
		$model->getCosts();
		$totalData = $model->getTotalData();
		$total = $model->getTotal();
		$taxes = $model->getTaxes();
		$this->cartData = $cartData;
		$this->totalData = $totalData;
		$this->total = $total;
		$this->taxes = $taxes;
		$this->tax = $tax;
		$this->currency = $currency;
		if (EshopHelper::getConfigValue('cart_weight') && $cart->hasShipping())
		{
			$eshopWeight = new EshopWeight();
			$this->weight = $eshopWeight->format($cart->getWeight(), EshopHelper::getConfigValue('weight_id'));
		}
		else
		{
			$this->weight = 0;
		}
		// Success message
		if ($session->get('success'))
		{
			$this->success = $session->get('success');
			$session->clear('success');
		}
		if ($cart->getStockWarning() != '')
		{
			$this->warning = $cart->getStockWarning();
		}
		elseif ($cart->getMinSubTotalWarning() != '')
		{
			$this->warning = $cart->getMinSubTotalWarning();
		}
		elseif ($cart->getMinQuantityWarning() != '')
		{
			$this->warning = $cart->getMinQuantityWarning();
		}
		elseif ($cart->getMinProductQuantityWarning() != '')
		{
			$this->warning = $cart->getMinProductQuantityWarning();
		}
		elseif ($cart->getMaxProductQuantityWarning() != '')
		{
			$this->warning = $cart->getMaxProductQuantityWarning();
		}
		if ($session->get('warning'))
		{
			$this->warning = $session->get('warning');
			$session->clear('warning');
		}
		parent::display($tpl);
	}
}