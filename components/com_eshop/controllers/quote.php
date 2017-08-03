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
 * EShop controller
 *
 * @package		Joomla
 * @subpackage	EShop
 * @since 1.5
 */
class EShopControllerQuote extends JControllerLegacy
{
	/**
	 * Constructor function
	 *
	 * @param array $config
	 */
	function __construct($config = array())
	{
		parent::__construct($config);
	}
	
	/**
	 * 
	 * Function to add a product to the quote
	 */
	function add()
	{
		$quote = new EshopQuote();
		$json = array();
		$productId = JRequest::getInt('id');
		$quantity = JRequest::getInt('quantity') > 0 ? JRequest::getInt('quantity') : 1;
		if (JRequest::getVar('options'))
		{
			$options = array_filter(JRequest::getVar('options'));
		}
		else
		{
			$options = array();
		}
		//Validate options first
		$productOptions = EshopHelper::getProductOptions($productId, JFactory::getLanguage()->getTag());
		for ($i = 0; $n = count($productOptions), $i < $n; $i++)
		{
			$productOption = $productOptions[$i];
			if ($productOption->required && empty($options[$productOption->product_option_id]))
			{
				$json['error']['option'][$productOption->product_option_id] = $productOption->option_name . ' ' . JText::_('ESHOP_REQUIRED');
			}
		}
		if (!$json)
		{
			$product = EshopHelper::getProduct($productId, JFactory::getLanguage()->getTag());
			$quote->add($productId, $quantity, $options);
			$viewProductLink = JRoute::_(EshopRoute::getProductRoute($productId, EshopHelper::getProductCategory($productId)));
			$viewQuoteLink = JRoute::_(EshopRoute::getViewRoute('quote'));
			if (EshopHelper::getConfigValue('active_https'))
			{
				$viewCheckoutLink = JRoute::_(EshopRoute::getViewRoute('checkout'), true, 1);
			}
			else 
			{
				$viewCheckoutLink = JRoute::_(EshopRoute::getViewRoute('checkout'));
			}
			$message = '<div>' . sprintf(JText::_('ESHOP_ADD_TO_QUOTE_SUCCESS_MESSAGE'), $viewProductLink, $product->product_name, $viewQuoteLink) . '</div>';
			$json['success']['message'] = $message;
		}
		else
		{
			$json['redirect'] = JRoute::_(EshopRoute::getProductRoute($productId, EshopHelper::getProductCategory($productId)));
		}
		echo json_encode($json);
		JFactory::getApplication()->close();
	}
	
	/**
	 * 
	 * Function to update quantity of a product in the quote
	 */
	function update()
	{
		$session = JFactory::getSession();
		$session->set('success', JText::_('ESHOP_QUOTE_UPDATE_MESSAGE'));
		$key = JRequest::getVar('key');
		$quantity = JRequest::getInt('quantity');
		$quote = new EshopQuote();
		$quote->update($key, $quantity);
	}
	
	/**
	 *
	 * Function to update quantity of all products in the quote
	 */
	function updates()
	{
		$session = JFactory::getSession();
		$session->set('success', JText::_('ESHOP_QUOTE_UPDATE_MESSAGE'));
		$key = JRequest::getVar('key');
		$quantity = JRequest::getVar('quantity');
		$quote = new EshopQuote();
		$quote->updates($key, $quantity);
	}
	
	/**
	 * 
	 * Function to remove a product from the quote
	 */
	function remove()
	{
		$session = JFactory::getSession();
		$key = JRequest::getVar('key');
		$quote = new EshopQuote();
		$quote->remove($key);
		{
			$session->set('success', JText::_('ESHOP_QUOTE_REMOVED_MESSAGE'));
		}
	}
	
	/**
	 * Function to process quote
	 */
	function processQuote()
	{
		$post = JRequest::get('post', JREQUEST_ALLOWHTML);
		$model = $this->getModel('Quote');
		$json = $model->processQuote($post);
		echo json_encode($json);
		exit();
	}
}