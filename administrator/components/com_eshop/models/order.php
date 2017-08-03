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
 * Eshop Component Model
 *
 * @package		Joomla
 * @subpackage	EShop
 * @since 1.5
 */
class EShopModelOrder extends EShopModel
{
	
	function __construct($config)
	{
		parent::__construct($config);
	}
	
	function store(&$data)
	{
		foreach ($data as $key => $value)
		{
			if (is_array($value))
			{
				$data[$key] = json_encode($value);
			}
		}
		$row = new EShopTable('#__eshop_orders', 'id', $this->getDbo());
		$row->load($data['id']);
		$paymentCountryInfo = EshopHelper::getCountry($data['payment_country_id']);
		if (is_object($paymentCountryInfo))
		{
			$data['payment_country_name'] = $paymentCountryInfo->country_name;
		}
		$paymentZoneInfo = EshopHelper::getZone($data['payment_zone_id']);
		if (is_object($paymentZoneInfo))
		{
			$data['payment_zone_name'] = $paymentZoneInfo->zone_name;
		}
		$shippingCountryInfo = EshopHelper::getCountry($data['shipping_country_id']);
		if (is_object($shippingCountryInfo))
		{
			$data['shipping_country_name'] = $shippingCountryInfo->country_name;
		}
		$shippingZoneInfo = EshopHelper::getZone($data['shipping_zone_id']);
		if (is_object($shippingZoneInfo))
		{
			$data['shipping_zone_name'] = $shippingZoneInfo->zone_name;
		}
		$orderStatusChanged = false;
		$updateStock = false;
		if ($data['order_status_id'] != $row->order_status_id)
		{
			$orderStatusChanged = true;
			$orderStatusFrom = $row->order_status_id;
			$orderStatusTo = $data['order_status_id'];
		}
		parent::store($data);
		$row->load($data['id']);
		$language = JLanguage::getInstance($row->language, 0);
		$language->load('com_eshop', JPATH_ADMINISTRATOR, $row->language);
		if ($orderStatusChanged)
		{
			if ($data['order_status_id'] == EshopHelper::getConfigValue('complete_status_id'))
			{
				JPluginHelper::importPlugin('eshop');
				$dispatcher = JDispatcher::getInstance();
				$dispatcher->trigger('onAfterCompleteOrder', array($row));
			}
			if (JRequest::getVar('send_notification_email'))
			{
				$jconfig = new JConfig();
				$mailer = JFactory::getMailer();
				$fromName = $jconfig->fromname;
				$fromEmail =  $jconfig->mailfrom;
				$subject = sprintf($language->_('ESHOP_NOTIFICATION_EMAIL_SUBJECT'), EshopHelper::getConfigValue('store_name'));
				$body = EshopHelper::getNotificationEmailBody($row, $orderStatusFrom, $orderStatusTo);
				$mailer->ClearAllRecipients();
				$mailer->sendMail($fromEmail, $fromName, $row->email, $subject, $body, 1);
			}
		}
		if (JRequest::getVar('send_shipping_notification_email') && JRequest::getVar('shipping_tracking_number') != '' && JRequest::getVar('shipping_tracking_url') != '')
		{
			$jconfig = new JConfig();
			$mailer = JFactory::getMailer();
			$fromName = $jconfig->fromname;
			$fromEmail =  $jconfig->mailfrom;
			$subject = $language->_('ESHOP_SHIPPING_NOTIFICATION_EMAIL_SUBJECT');
			$body = EshopHelper::getShippingNotificationEmailBody($row);
			$mailer->ClearAllRecipients();
			$mailer->sendMail($fromEmail, $fromName, $row->email, $subject, $body, 1);
		}
		return true;
	}
	
	/**
	 * Method to remove orders
	 *
	 * @access	public
	 * @return boolean True on success
	 * @since	1.5
	 */
	public function delete($cid = array())
	{
		if (count($cid))
		{
			$db = $this->getDbo();
			$cids = implode(',', $cid);
			$query = $db->getQuery(true);
			$query->delete('#__eshop_orders')
				->where('id IN (' . $cids . ')');
			$db->setQuery($query);
			if (!$db->query())
				//Removed error
				return 0;
			$numItemsDeleted = $db->getAffectedRows();
			//Delete order products
			$query->clear();
			$query->delete('#__eshop_orderproducts')
				->where('order_id IN (' . $cids . ')');
			$db->setQuery($query);
			if (!$db->query())
				//Removed error
				return 0;
			//Delete order totals
			$query->clear();
			$query->delete('#__eshop_ordertotals')
				->where('order_id IN (' . $cids . ')');
			$db->setQuery($query);
			if (!$db->query())
				//Removed error
				return 0;
			//Delete order options
			$query->clear();
			$query->delete('#__eshop_orderoptions')
				->where('order_id IN (' . $cids . ')');
			$db->setQuery($query);
			if (!$db->query())
				//Removed error
				return 0;
			
			if ($numItemsDeleted < count($cid))
			{
				//Removed warning
				return 2;
			}
		}
		//Removed success
		return 1;
	}
	
	/**
	 * 
	 * Function to download file
	 * @param int $id
	 */
	function downloadFile($id, $download = true)
	{
		$app = JFactory::getApplication();
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('option_value')
			->from('#__eshop_orderoptions')
			->where('id = ' . intval($id));
		$db->setQuery($query);
		$filename = $db->loadResult();
		while (@ob_end_clean());
		EshopHelper::processDownload(JPATH_ROOT . '/media/com_eshop/files/' . $filename, $filename, $download);
		$app->close(0);
	}
}