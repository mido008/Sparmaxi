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
defined( '_JEXEC' ) or die();

/**
 * HTML View class for EShop component
 *
 * @static
 * @package		Joomla
 * @subpackage	EShop
 * @since 1.5
 */
class EShopViewCart extends EShopView
{		
	function display($tpl = null)
	{
		$mainframe = JFactory::getApplication();
		if (EshopHelper::getConfigValue('catalog_mode'))
		{
			$session = JFactory::getSession();
			$session->set('warning', JText::_('ESHOP_CATALOG_MODE_ON'));
			$mainframe->redirect(JRoute::_(EshopRoute::getViewRoute('categories')));
		}
		else
		{
			$app		= JFactory::getApplication();
			$menu		= $app->getMenu();
			$menuItem = $menu->getActive();
			if ($menuItem)
			{
				if (isset($menuItem->query['view']) && ($menuItem->query['view']== 'frontpage'))
				{
					$pathway = $app->getPathway();
					$pathUrl = EshopRoute::getViewRoute('frontpage');
					$pathway->addItem(JText::_('ESHOP_SHOPPING_CART'), $pathUrl);
				}
			}
			$document = JFactory::getDocument();
			$document->addStyleSheet(JUri::base(true).'/components/com_eshop/assets/colorbox/colorbox.css');
			$title = JText::_('ESHOP_SHOPPING_CART');
			// Set title of the page
			$siteNamePosition = $app->getCfg('sitename_pagetitles');
			if($siteNamePosition == 1)
			{
				$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
			}
			elseif ($siteNamePosition == 2)
			{
				$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
			}
			$document->setTitle($title);
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
			$this->coupon_code = $session->get('coupon_code');
			$this->voucher_code = $session->get('voucher_code');
			$this->postcode = $session->get('shipping_postcode');
			$this->shipping_required = $cart->hasShipping();
			if (EshopHelper::getConfigValue('cart_weight') && $cart->hasShipping())
			{
				$eshopWeight = new EshopWeight();
				$this->weight = $eshopWeight->format($cart->getWeight(), EshopHelper::getConfigValue('weight_id'));
			}
			else
			{
				$this->weight = 0;
			}
			if ($this->shipping_required)
			{
				$shippingMethod = $session->get('shipping_method');
				if (is_array($shippingMethod))
				{
					$this->shipping_method = $shippingMethod['name'];
				}
				else
				{
					$this->shipping_method = '';
				}
				$document->addScriptDeclaration(EshopHtmlHelper::getZonesArrayJs());
				//Country list
				$db = JFactory::getDbo();
				$query = $db->getQuery(true);
				$query->select('id, country_name AS name')
					->from('#__eshop_countries')
					->where('published=1')
					->order('country_name');
				$db->setQuery($query);
				$options = array();
				$options[] = JHtml::_('select.option', 0, JText::_('ESHOP_PLEASE_SELECT'), 'id', 'name');
				$options = array_merge($options, $db->loadObjectList());
				$lists['country_id'] = JHtml::_('select.genericlist', $options, 'country_id',
						' class="inputbox" ', 'id', 'name', $session->get('shipping_country_id'));
				//Zone list
				$query->clear();
				$query->select('id, zone_name')
					->from('#__eshop_zones')
					->where('country_id=' . (int) $session->get('shipping_country_id'))
					->where('published=1');
					
				$db->setQuery($query);
				$options = array();
				$options[] = JHtml::_('select.option', 0, JText::_('ESHOP_PLEASE_SELECT'), 'id', 'zone_name');
				$options = array_merge($options, $db->loadObjectList());
				$lists['zone_id'] = JHtml::_('select.genericlist', $options, 'zone_id', ' class="inputbox" ', 'id', 'zone_name', $session->get('shipping_zone_id'));
				$this->lists = $lists;
				$this->shipping_zone_id = $session->get('shipping_zone_id');
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
			$this->user = JFactory::getUser();
			parent::display($tpl);
		}
	}	
}