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
class EShopControllerCustomer extends JControllerLegacy
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
	 * Function to download invoice
	 */
	function downloadInvoice()
	{
		$orderId = JRequest::getInt('order_id');
		$user = JFactory::getUser();
		$canDownload = true;
		if (!$user->get('id'))
		{
			$canDownload = false;
		}
		else 
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->select('COUNT(*)')
				->from('#__eshop_orders')
				->where('id = ' . intval($orderId))
				->where('customer_id = ' . intval($user->get('id')));
			$db->setQuery($query);
			if (!$db->loadResult())
			{
				$canDownload = false;
			}
		}
		if (!$canDownload)
		{
			$mainframe = JFactory::getApplication();
			$mainframe->enqueueMessage(JText::_('ESHOP_DOWNLOAD_INVOICE_NOT_AVAILABLE'), 'Error');
			$mainframe->redirect(EshopRoute::getViewRoute('customer') . '&layout=orders');
		}
		else 
		{
			EshopHelper::downloadInvoice(array($orderId));
		}
	}
	
	/**
	 * 
	 * Function to download file
	 */
	function downloadFile()
	{
		$user = JFactory::getUser();
		$orderId = JRequest::getInt('order_id');
		$downloadCode = JRequest::getVar('download_code');
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$canDownload = true;
		$fileName = '';
		if (!$user->get('id'))
		{
			$canDownload = false;
			$message = JText::_('ESHOP_LOGIN_TO_DOWNLOAD');
		}
		else
		{
			$query->select('a.*')
				->from('#__eshop_orderdownloads AS a')
				->innerJoin('#__eshop_orders AS b ON (a.order_id = b.id)')
				->where('a.order_id = ' . $orderId)
				->where('a.download_code = "' . $downloadCode . '"')
				->where('b.customer_id = ' . $user->get('id'));
			$db->setQuery($query);
			$row = $db->loadObject();
			if ($row)
			{
				if ($row->remaining)
				{
					$fileName = $row->filename;
					//Update remaining
					$query->clear();
					$query->update('#__eshop_orderdownloads')
						->set('remaining = remaining - 1')
						->where('id = ' . $row->id);
					$db->setQuery($query);
					$db->query();
				}
				else 
				{
					$canDownload = false;
					$message = JText::_('ESHOP_TOTAL_DOWNLOAD_ALLOWED_REACH');
				}
			}
			else 
			{
				$canDownload = false;
				$message = JText::_('ESHOP_DO_NOT_HAVE_DOWNLOAD_PERMISSION');				
			}
		}
		if ($canDownload)
		{
			while (@ob_end_clean());
			$filePath = JPATH_ROOT . '/media/com_eshop/downloads/'.$fileName;
			EshopHelper::processDownload($filePath, $fileName, true);
		}
		else
		{
			$application = JFactory::getApplication();
			$application->enqueueMessage($message, 'notice');
			$application->redirect('index.php');
		}
	}
	
	/**
	 * Function to process payment method
	 */
	function processUser()
	{
		$post = JRequest::get('post', JREQUEST_ALLOWHTML);
		$model = $this->getModel('Customer');
		$json = $model->processUser($post);
		echo json_encode($json);
		exit();
	}
	
	/**
	 * 
	 * Function to process (add/update) address
	 */
	function processAddress()
	{
		$post = JRequest::get('post', JREQUEST_ALLOWHTML);
		$model = $this->getModel('Customer');
		$json = $model->processAddress($post);
		$session = JFactory::getSession();
		if ($session->get('shipping_address_id') && $session->get('shipping_address_id') == $post['id'])
		{
			$session->set('shipping_country_id', $post['country_id']);
			$session->set('shipping_zone_id', $post['zone_id']);
			$session->set('shipping_postcode', $post['postcode']);
	
			$session->clear('shipping_method');
			$session->clear('shipping_methods');
		}
		if ($session->get('payment_address_id') && $session->get('payment_address_id') == $post['id'])
		{
			$session->set('payment_country_id', $post['country_id']);
			$session->set('payment_zone_id', $post['zone_id']);
			
			$session->clear('payment_method');
		}
		echo json_encode($json);
		exit();
	}
	
	/**
	 * 
	 * Function to delete address
	 */
	function deleteAddress()
	{
		$model =  $this->getModel('Customer');
		$id = JRequest::getVar('aid') ;
		$json = $model->deleteAddress($id);
		echo json_encode($json);
		exit();
	}
}