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

class EshopPayment
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
		$paymentMethod = $session->get('payment_method');
		$currency = new EshopCurrency();
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('params')
			->from('#__eshop_payments')
			->where('name = "' . $paymentMethod . '"');
		$db->setQuery($query);
		$paymentPlugin = $db->loadObject();
		if (is_object($paymentPlugin))
		{
			$params = new JRegistry($paymentPlugin->params);
			$paymentFee = $params->get('payment_fee');
			$minSubTotal = $params->get('min_sub_total');
			if ($minSubTotal > 0)
			{
				if ($total >= $minSubTotal)
				{
					$paymentFee = 0;
				}
			}
			
			$percentage = false;
			if (strpos($paymentFee, '%'))
			{
				$percentage = true;
				$paymentFee = str_replace('%', '', $paymentFee);
			}
			
			if ($paymentFee > 0)
			{
				if ($percentage)
				{
					$paymentFee = $paymentFee * $total / 100;
				}
				$totalData[] = array(
					'name'		=> 'payment_fee',
					'title'		=> JText::_('ESHOP_PAYMENT_FEE'),
					'text'		=> $currency->format($paymentFee),
					'value'		=> $paymentFee);
				if ($params->get('taxclass_id'))
				{
					$tax = new EshopTax(EshopHelper::getConfig());
					$taxRates = $tax->getTaxRates($paymentFee, $params->get('taxclass_id'));
					foreach ($taxRates as $taxRate)
					{
						if (!isset($taxes[$taxRate['tax_rate_id']]))
						{
							$taxes[$taxRate['tax_rate_id']] = $taxRate['amount'];
						}
						else
						{
							$taxes[$taxRate['tax_rate_id']] += $taxRate['amount'];
						}
					}
				}
				$total += $paymentFee;
			}
		}	
	}
}