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
class EShopViewCustomer extends EShopView
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
			$session = JFactory::getSession();
			$userInfo = $this->get('user');
			$layout = $this->getLayout();
			if ($layout == 'account')
			{
				$this->_displayAccount($tpl);
				return;
			}
			elseif ($layout == 'orders')
			{
				$this->_displayOrders($tpl);
				return;
			}
			elseif ($layout == 'order')
			{
				$this->_displayOrder($tpl);
				return;
			}
			elseif ($layout == 'downloads')
			{
				$this->_displayDownloads($tpl);
				return;
			}
			elseif ($layout == 'addresses')
			{
				$this->_displayAddresses($tpl);
				return;
			}
			elseif ($layout == 'address')
			{
				$this->_displayAddress($tpl);
				return;
			}
			else
			{
				$user = JFactory::getUser();
				if ($user->id)
				{
					$userInfo = $this->get('user');
					// Success message
					if ($session->get('success'))
					{
						$this->success = $session->get('success');
						$session->clear('success');
					}
					$this->user = $userInfo;
					parent::display($tpl);
				}
				else
				{
					$uri = JUri::getInstance();
					$mainframe->redirect('index.php?option=com_users&view=login&return=' . base64_encode($uri->toString()));
				}
			}			
		}
	}

	/**
	 * 
	 * Function to display edit account page
	 * @param string $tpl
	 */
	function _displayAccount($tpl)
	{
		$user = JFactory::getUser();
		if ($user->id)
		{
			$userInfo = $this->get('user');
			if($userInfo->customergroup_id)
			{
				$selected = $userInfo->customergroup_id;
			}
			else 
			{
				$selected = EshopHelper::getConfigValue('customergroup_id');
			}
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$customerGroupDisplay = EshopHelper::getConfigValue('customer_group_display');
			$countCustomerGroup = count(explode(',', $customerGroupDisplay));
			if ($countCustomerGroup > 1)
			{
				$query->select('a.id, b.customergroup_name AS name')
					->from('#__eshop_customergroups AS a')
					->innerJoin('#__eshop_customergroupdetails AS b ON (a.id = b.customergroup_id)')
					->where('a.published = 1')
					->where('b.language = "' . JFactory::getLanguage()->getTag() . '"');
				if ($customerGroupDisplay != '')
					$query->where('a.id IN (' . $customerGroupDisplay . ')');
				$this->customergroup_id = JHtml::_('select.genericlist', $db->loadObjectList(), 'customergroup_id', ' class="inputbox" ', 'id', 'name', $selected);
			}
			elseif ($countCustomerGroup == 1)
			{
				$this->default_customergroup_id = $customerGroupDisplay;
			}
			$query->order('b.customergroup_name');
			$db->setQuery($query);
			$this->user = $user;
			$this->userInfo = $userInfo;
			parent::display($tpl);
		}
		else
		{
			$mainframe = JFactory::getApplication();
			$uri = JUri::getInstance();
			$mainframe->redirect('index.php?option=com_users&view=login&return=' . base64_encode($uri->toString()));
		}
	}
	
	/**
	 * 
	 * Function to display list orders for user
	 * @param string $tpl
	 */
	function _displayOrders($tpl)
	{
		$user = JFactory::getUser();
		if ($user->id)
		{
			$tax = new EshopTax(EshopHelper::getConfig());
			$currency = new EshopCurrency();
			$orders = $this->get('Orders');
			for ($i = 0; $n = count($orders), $i < $n; $i++)
			{
				$orders[$i]->total = $currency->format($orders[$i]->total, $orders[$i]->currency_code, $orders[$i]->currency_exchanged_value);
			}
			$this->tax		  = $tax;
			$this->orders     = $orders;	
			$this->currency     = $currency;
			// Warning message
			$session = JFactory::getSession();
			if ($session->get('warning'))
			{
				$this->warning = $session->get('warning');
				$session->clear('warning');
			}
			parent::display($tpl);
		}
		else
		{
			$mainframe = JFactory::getApplication();
			$uri = JUri::getInstance();
			$mainframe->redirect('index.php?option=com_users&view=login&return=' . base64_encode($uri->toString()));
		}
		
	}
	
	/**
	 * 
	 * Function to display order information
	 * @param string $tpl
	 */
	function _displayOrder($tpl)
	{
		$user = JFactory::getUser();
		if ($user->id)
		{
			$orderId =JRequest::getInt('order_id');
			//Get order infor
			$orderInfor = EshopHelper::getOrder($orderId);
			if (!is_object($orderInfor) || (is_object($orderInfor) && $orderInfor->customer_id != $user->get('id')))
			{
				$mainframe = JFactory::getApplication();
				$session = JFactory::getSession();
				$session->set('warning', JText::_('ESHOP_ORDER_DOES_NOT_EXITS'));
				$mainframe->redirect(EshopRoute::getViewRoute('customer') . '&layout=orders');
			}
			else
			{
				$db = JFactory::getDbo();
				$query = $db->getQuery(true);
				$tax = new EshopTax(EshopHelper::getConfig());
				$currency = new EshopCurrency();
		
				$orderProducts = EshopHelper::getOrderProducts($orderId);
				for ($i = 0; $n = count($orderProducts), $i < $n; $i++)
				{
					$query->clear();
					$query->select('*')
						->from('#__eshop_orderoptions')
						->where('order_product_id = ' . intval($orderProducts[$i]->id));
					$db->setQuery($query);
					$orderProducts[$i]->options = $db->loadObjectList();
				}
				$orderTotals   = EshopHelper::getOrderTotals($orderId);
				//Payment custom fields here
				$form = new RADForm(EshopHelper::getFormFields('B'));
				$this->paymentFields = $form->getFields();
				//Shipping custom fields here
				$form = new RADForm(EshopHelper::getFormFields('S'));
				$this->shippingFields = $form->getFields();
				$this->orderProducts = $orderProducts;
				$this->orderInfor   = $orderInfor;
				$this->orderTotals   = $orderTotals;
				$this->tax		  = $tax;
				$this->currency     = $currency;
				parent::display($tpl);
			}
		}
		else
		{
			$mainframe = JFactory::getApplication();
			$uri = JUri::getInstance();
			$mainframe->redirect('index.php?option=com_users&view=login&return=' . base64_encode($uri->toString()));
		}
	}
	
	/**
	 *
	 * Function to display list downloads for user
	 * @param string $tpl
	 */
	function _displayDownloads($tpl)
	{
		$user = JFactory::getUser();
		if ($user->id)
		{
			$tax = new EshopTax(EshopHelper::getConfig());
			$currency = new EshopCurrency();
			$downloads = $this->get('Downloads');
			foreach ($downloads as $download)
			{
				$size = filesize(JPATH_SITE.'/media/com_eshop/downloads/'.$download->filename);
				$i = 0;
				$suffix = array(
					'B',
					'KB',
					'MB',
					'GB',
					'TB',
					'PB',
					'EB',
					'ZB',
					'YB'
				);
				while (($size / 1024) > 1)
				{
					$size = $size / 1024;
					$i++;
				}
				$download->size = round(substr($size, 0, strpos($size, '.') + 4), 2) . $suffix[$i];
			}
			$this->downloads = $downloads;
			// Warning message
			$session = JFactory::getSession();
			if ($session->get('warning'))
			{
				$this->warning = $session->get('warning');
				$session->clear('warning');
			}
			parent::display($tpl);
		}
		else
		{
			$mainframe = JFactory::getApplication();
			$uri = JUri::getInstance();
			$mainframe->redirect('index.php?option=com_users&view=login&return=' . base64_encode($uri->toString()));
		}
	}
	
	/**
	 * 
	 * Function to display addresses for user
	 * @param string $tpl
	 */
	function _displayAddresses($tpl)
	{
		$user = JFactory::getUser();
		if ($user->id)
		{
			$addresses = $this->get('addresses');
			$this->addresses = $addresses;
			// Warning message
			$session = JFactory::getSession();
			if ($session->get('success'))
			{
				$this->success = $session->get('success');
				$session->clear('success');
			}
			if ($session->get('warning'))
			{
				$this->warning = $session->get('warning');
				$session->clear('warning');
			}
			parent::display($tpl);
		}
		else
		{
			$mainframe = JFactory::getApplication();
			$uri = JUri::getInstance();
			$mainframe->redirect('index.php?option=com_users&view=login&return=' . base64_encode($uri->toString()));
		}
	}
	
	/**
	 * 
	 * Function to display address form
	 * @param string $tpl
	 */
	function _displayAddress($tpl)
	{
		$user = JFactory::getUser();
		if ($user->id)
		{
			$address = $this->get('address');
			$lists = array();
			if (is_object($address))
			{			
				(EshopHelper::getDefaultAddressId($address->customer_id) == $address->id) ? $isDefault = 1 : $isDefault = 0;
			}
			else 
			{			
				$isDefault = 0;
			}
			$excludedFields = array('email', 'telephone', 'fax');
			$fields =  EshopHelper::getFormFields('A', $excludedFields);
			$form = new RADForm($fields);
			$countryField = $form->getField('country_id');
			$zoneField = $form->getField('zone_id');
			if (is_object($address))
			{
				$data = array();
				foreach ($fields as $field)
				{
					if (property_exists($address, $field->name))
					{
						$data[$field->name] = $address->{$field->name};
					}
				}
				$form->bind($data);
				if ($zoneField instanceof RADFormFieldZone)
				{
					$zoneField->setCountryId($address->country_id);
				}	
			}
			else 
			{
				$countryId = $countryField->getValue();
				if ($countryId)
					$zoneField->setCountryId($countryId);
			}
			$lists['default_address'] =  JHTML::_('select.booleanlist', 'default_address', ' class="inputbox" ', $isDefault);
			
			$this->address = $address;
			$this->lists = $lists;
			$this->form = $form;
			parent::display($tpl);
		}
		else
		{
			$mainframe = JFactory::getApplication();
			$uri = JUri::getInstance();
			$mainframe->redirect('index.php?option=com_users&view=login&return=' . base64_encode($uri->toString()));
		}
	}
	/**
	 *
	 * Private method to get Country List
	 * @param array $lists
	 */
	function _getCountryList(&$lists, $selected = '')
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id, country_name AS name')
			->from('#__eshop_countries')
			->where('published = 1')
			->order('country_name');
		$db->setQuery($query);
		$options = array();
		$options[] = JHtml::_('select.option', 0, JText::_('ESHOP_PLEASE_SELECT'), 'id', 'name');
		$options = array_merge($options, $db->loadObjectList());
		$lists['country_id'] = JHtml::_('select.genericlist', $options, 'country_id', ' class="inputbox" ', 'id', 'name', $selected);
	}
	
	/**
	 *
	 * Private method to get Zone List
	 * @param array $lists
	 */
	function _getZoneList(&$lists, $selected = '', $countryId = '')
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id, zone_name')
		->from('#__eshop_zones')
		->where('country_id=' . (int) $countryId)
		->where('published = 1');
		$db->setQuery($query);
		$options = array();
		$options[] = JHtml::_('select.option', 0, JText::_('ESHOP_PLEASE_SELECT'), 'id', 'zone_name');
		$options = array_merge($options, $db->loadObjectList());
		$lists['zone_id'] = JHtml::_('select.genericlist', $options, 'zone_id', ' class="inputbox" ', 'id', 'zone_name', $selected);
	}
}