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
class EShopViewQuote extends EShopView
{		
	function display($tpl = null)
	{
		$mainframe = JFactory::getApplication();
		if (!EshopHelper::getConfigValue('quote_cart_mode'))
		{
			$session = JFactory::getSession();
			$session->set('warning', JText::_('ESHOP_QUOTE_CART_MODE_OFF'));
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
					$pathway->addItem(JText::_('ESHOP_QUOTE_CART'), $pathUrl);
				}
			}
			$document = JFactory::getDocument();
			$document->addStyleSheet(JUri::base(true).'/components/com_eshop/assets/colorbox/colorbox.css');
			$title = JText::_('ESHOP_QUOTE_CART');
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
			//Captcha
			$showCaptcha = 0;
			if (EshopHelper::getConfigValue('enable_quote_captcha'))
			{
				$captchaPlugin = JFactory::getApplication()->getParams()->get('captcha', JFactory::getConfig()->get('captcha'));
				if ($captchaPlugin == 'recaptcha')
				{
					$showCaptcha = 1;
					$this->captcha = JCaptcha::getInstance($captchaPlugin)->display('dynamic_recaptcha_1', 'dynamic_recaptcha_1', 'required');
				}
			}
			$this->showCaptcha = $showCaptcha;
			parent::display($tpl);
		}
	}
}