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
if (!is_object($this->orderInfor))
{
	?>
	<p><?php echo JText::_('ESHOP_ORDER_NOT_EXISTED'); ?></p>
	<?php
}
else
{
	// iDevAffiliate integration
	if (EshopHelper::getConfigValue('idevaffiliate_integration') && file_exists( JPATH_SITE . "/" . EshopHelper::getConfigValue('idevaffiliate_path') . "/sale.php" ))
	{
		?>
		<img border="0" src="<?php echo self::getSiteUrl() . self::getConfigValue('idevaffiliate_path'); ?>/sale.php?profile=72198&idev_saleamt=<?php echo $this->orderInfor->total; ?>&idev_ordernum=<?php echo $this->orderInfor->order_number; ?>" width="1" height="1" />
		<?php
		EshopHelper::iDevAffiliate($this->orderInfor);
	}
	$hasShipping = $this->orderInfor->shipping_method;
	?>
	<h1><?php echo sprintf(JText::_('ESHOP_ORDER_COMPLETED_TITLE'), $this->orderInfor->id); ?></h1>
	<p><?php echo sprintf(JText::_('ESHOP_ORDER_COMPLETED_DESC'), $this->orderInfor->id); ?></p>
	<table cellpadding="0" cellspacing="0" class="list">
		<thead>
			<tr>
				<td colspan="2" class="left">
					<?php echo JText::_('ESHOP_ORDER_DETAILS'); ?>
				</td>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td style="width: 50%;" class="left">
					<b><?php echo JText::_('ESHOP_ORDER_ID'); ?>: </b> #<?php echo $this->orderInfor->id; ?><br />
					<b><?php echo JText::_('ESHOP_DATE_ADDED'); ?>: </b> <?php echo JHtml::date($this->orderInfor->created_date, EshopHelper::getConfigValue('date_format', 'm-d-Y')); ?>
	         	</td>
				<td style="width: 50%;" class="left">
					<b><?php echo JText::_('ESHOP_PAYMENT_METHOD'); ?>: </b> <?php echo JText::_($this->orderInfor->payment_method_title); ?><br />
					<b><?php echo JText::_('ESHOP_SHIPPING_METHOD'); ?>: </b> <?php echo $this->orderInfor->shipping_method_title; ?><br />
				</td>
			</tr>
		</tbody>
		</table>
		<table cellpadding="0" cellspacing="0" class="list">
			<thead>
				<tr>
					<td class="left">
						<?php echo JText::_('ESHOP_PAYMENT_ADDRESS'); ?>
				</td>
				<?php
				if ($hasShipping)
				{
					?>
					<td class="left">
						<?php echo JText::_('ESHOP_SHIPPING_ADDRESS'); ?>
					</td>
					<?php
				}
				?>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td class="left">
					<?php
					echo EshopHelper::getPaymentAddress($this->orderInfor);
					$excludedFields = array('firstname', 'lastname', 'email', 'telephone', 'fax', 'company', 'company_id', 'address_1', 'address_2', 'city', 'postcode', 'country_id', 'zone_id');
					foreach ($this->paymentFields as $field)
					{
						$fieldName = $field->name;
						if (!in_array($fieldName, $excludedFields))
						{
							$fieldValue = $this->orderInfor->{'payment_'.$fieldName};
							if (is_string($fieldValue) && is_array(json_decode($fieldValue)))
							{
								$fieldValue = implode(', ', json_decode($fieldValue));
							}
							if ($fieldValue != '')
							{
								echo '<br />' . JText::_($field->title) . ': ' . $fieldValue;
							}
						}
					}
					?>
				</td>
				<?php
				if ($hasShipping)
				{
					?>
					<td class="left">
						<?php
						echo EshopHelper::getShippingAddress($this->orderInfor);
						foreach ($this->shippingFields as $field)
						{
							$fieldName = $field->name;
							if (!in_array($fieldName, $excludedFields))
							{
								$fieldValue = $this->orderInfor->{'shipping_'.$fieldName};
								if (is_string($fieldValue) && is_array(json_decode($fieldValue)))
								{
									$fieldValue = implode(', ', json_decode($fieldValue));
								}
								if ($fieldValue != '')
								{
									echo '<br />' . JText::_($field->title) . ': ' . $fieldValue;
								}
							}
						}
						?>
					</td>
					<?php
				}
				?>
			</tr>
		</tbody>
	</table>
	<table cellpadding="0" cellspacing="0" class="list">
		<thead>
			<tr>
				<td class="left">
					<?php echo JText::_('ESHOP_PRODUCT_NAME'); ?>
				</td>
				<td class="left">
					<?php echo JText::_('ESHOP_MODEL'); ?>
				</td>
				<td class="left">
					<?php echo JText::_('ESHOP_QUANTITY'); ?>
				</td>
				<td class="left">
					<?php echo JText::_('ESHOP_PRICE'); ?>
				</td>
				<td class="left">
					<?php echo JText::_('ESHOP_TOTAL'); ?>
				</td>
			</tr>
		</thead>
		<tbody>
			<?php
			foreach ($this->orderProducts as $product)
			{
				$options = $product->options;
				?>
				<tr>
					<td class="left">
						<?php
						echo '<b>' . $product->product_name . '</b>';
						for ($i = 0; $n = count($options), $i < $n; $i++)
						{
							echo '<br />- ' . $options[$i]->option_name . ': ' . $options[$i]->option_value . (isset($options[$i]->sku) && $options[$i]->sku != '' ? ' (' . $options[$i]->sku . ')' : '');
						}
						?>
					</td>
					<td class="left"><?php echo $product->product_sku; ?></td>
					<td class="left"><?php echo $product->quantity; ?></td>
					<td class="right"><?php echo $product->price; ?></td>
					<td class="right"><?php echo $product->total_price; ?></td>
				</tr>
				<?php
			}
			?>
		</tbody>
		<tfoot>
			<?php
				foreach ($this->orderTotals as $ordertotal)
				{ 
			?>
			<tr>
				<td colspan="3"></td>
				<td class="right">
					<b><?php echo $ordertotal->title?>: </b>
				</td>
				<td class="right">
					<?php echo $ordertotal->text?>
				</td>
			</tr>
			<?php
				} 
			?>
		</tfoot>
	</table>
	<?php
}	
?>