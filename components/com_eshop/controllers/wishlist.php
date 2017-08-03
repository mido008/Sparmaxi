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
class EShopControllerWishlist extends JControllerLegacy
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
	 * Function to add a product into the wishlist
	 */
	function add()
	{
		$productId = JRequest::getInt('product_id');
		$model = $this->getModel('Wishlist');
		$json = $model->add($productId);
		echo json_encode($json);
		exit();
	}
	
	/**
	 *
	 * Function to remove a product from the wishlist
	 */
	function remove()
	{
		$mainframe = JFactory::getApplication();
		$user = JFactory::getUser();
		if (!$user->get('id'))
		{
			$mainframe->enqueueMessage(JText::_('ESHOP_YOU_MUST_LOGIN_TO_VIEW_WISHLIST'), 'Notice');
			$mainframe->redirect('index.php?option=com_users&view=login&return=' . base64_encode('index.php?option=com_eshop&view=wishlist'));
		}
		else 
		{
			$session = JFactory::getSession();
			$productId = JRequest::getInt('product_id');
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->delete('#__eshop_wishlists')
				->where('customer_id = ' . intval($user->get('id')))
				->where('product_id = ' . intval($productId));
			$db->setQuery($query);
			$db->execute();
			$session->set('success', JText::_('ESHOP_WISHLIST_REMOVED_MESSAGE'));
			$json['redirect'] = JRoute::_(EshopRoute::getViewRoute('wishlist'));
			echo json_encode($json);
			exit();
		}
	}
}