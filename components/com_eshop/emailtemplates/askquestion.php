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
?>
<div style="width: 680px;">
	<p style="margin-top: 0px; margin-bottom: 20px;">You have received a question about a product.</p>
	<table style="width: 100%; margin-bottom: 20px;">
		<tr>
			<td width="30%" style="font-size: 12px; text-align: right; padding: 7px;" valign="top"><strong><?php echo JText::_('ESHOP_PRODUCT_NAME'); ?>:</strong></td>
			<td width="70%"><?php echo $this->product->product_name; ?></td>
		</tr>
		<tr>		
			<td width="30%" style="font-size: 12px; text-align: right; padding: 7px;" valign="top"><strong><?php echo JText::_('ESHOP_MODEL'); ?>:</strong></td>
			<td width="70%"><?php echo $this->product->product_sku; ?></td>
		</tr>
		<tr>
			<td width="30%" style="font-size: 12px; text-align: right; padding: 7px;" valign="top"><strong><?php echo JText::_('ESHOP_NAME'); ?>:</strong></td>
			<td width="70%"><?php echo $this->data['name']; ?></td>
		</tr>
		<tr>
			<td width="30%" style="font-size: 12px; text-align: right; padding: 7px;" valign="top"><strong><?php echo JText::_('ESHOP_COMPANY'); ?>:</strong></td>
			<td width="70%"><?php echo $this->data['company']; ?></td>
		</tr>
		<tr>
			<td width="30%" style="font-size: 12px; text-align: right; padding: 7px;" valign="top"><strong><?php echo JText::_('ESHOP_EMAIL'); ?>:</strong></td>
			<td width="70%"><?php echo $this->data['email']; ?></td>
		</tr>
		<tr>
			<td width="30%" style="font-size: 12px; text-align: right; padding: 7px;" valign="top"><strong><?php echo JText::_('ESHOP_PHONE'); ?>:</strong></td>
			<td width="70%"><?php echo $this->data['phone']; ?></td>
		</tr>
		<tr>
			<td width="30%" style="font-size: 12px; text-align: right; padding: 7px;" valign="top"><strong><?php echo JText::_('ESHOP_MESSAGE'); ?>:</strong></td>
			<td width="70%"><?php echo nl2br($this->data['message']); ?></td>
		</tr>
	</table>
</div>