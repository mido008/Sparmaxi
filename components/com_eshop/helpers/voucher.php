<?php
/**
 * @version		1.4.1
 * @package		Joomla
 * @subpackage	EShop
 * @author  	Giang Dinh Truong
 * @copyright	Copyright (C) 2013 Ossolution Team
 * @license		GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die();

class EshopVoucher
{

	/**
	 * 
	 * Function to get Costs, passed by reference to update
	 * @param  array $totalData
	 * @param  float $total
	 * @param  array $taxes
	 */
	public function getCosts(&$totalData, &$total, &$taxes)
	{
		$session = JFactory::getSession();
		$tax = new EshopTax(EshopHelper::getConfig());
		$currency = new EshopCurrency();
		$voucherData = $this->getVoucherData($session->get('voucher_code'));
		if (count($voucherData))
		{
			if ($voucherData['voucher_amount'] > $total)
			{
				$amount = $total;
			}
			else 
			{
				$amount = $voucherData['voucher_amount'];
			}
			$totalData[] = array(
				'name'		=> 'voucher',
				'title'		=> sprintf(JText::_('ESHOP_VOUCHER'), $session->get('voucher_code')), 
				'text'		=> $currency->format(-$amount), 
				'value'		=> -$amount);
			$total -= $amount;
		}
	}

	/**
	 * 
	 * Function to get information for a specific voucher
	 * @param string $code
	 */
	public function getVoucherData($code)
	{
		$status = true;
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			->from('#__eshop_vouchers')
			->where('voucher_code = ' . $db->quote($code))
			->where('(voucher_start_date = "0000-00-00 00:00:00" OR voucher_start_date < NOW())')
			->where('(voucher_end_date = "0000-00-00 00:00:00" OR voucher_end_date > NOW())')
			->where('published = 1');
		$db->setQuery($query);
		$voucher = $db->loadObject();
		if (is_object($voucher))
		{
			//Check if current user used this voucher or not
			$user = JFactory::getUser();
			$query->clear();
			$query->select('COUNT(*)')
				->from('#__eshop_voucherhistory')
				->where('voucher_id = ' . intval($voucher->id))
				->where('user_id = ' . intval($user->get('id')));
			$db->setQuery($query);
			if ($db->loadResult() > 0)
			{
				$status = false;
			}
			
		}
		else
		{
			$status = false;
		}
		//Return
		if ($status)
		{
			return array(
				'voucher_id'			=> $voucher->id,  
				'voucher_code'			=> $voucher->voucher_code, 
				'voucher_amount'		=> $voucher->voucher_amount, 
				'voucher_start_date'	=> $voucher->voucher_start_date, 
				'voucher_end_date'		=> $voucher->voucher_end_date);
		}
		else
		{
			return array();
		}
	}

	/**
	 * 
	 * Function to add voucher history
	 * @param int $voucherId
	 * @param int $orderId
	 * @param int $userId
	 * @param float $amount
	 */
	public function addVoucherHistory($voucherId, $orderId, $userId, $amount)
	{
		$row = JTable::getInstance('Eshop', 'Voucherhistory');
		$row->id = '';
		$row->order_id = $orderId;
		$row->voucher_id = $voucherId;
		$row->user_id = $userId;
		$row->amount = $amount;
		$row->created_date = JFactory::getDate()->toSql();
		$row->store();
	}
}