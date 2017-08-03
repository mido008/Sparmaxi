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
<table class="admintable adminform">
	<tr>
		<td width="50%">
			<?php echo JText::_('ESHOP_CONFIG_INVOICE_ENABLE'); ?>:<br>
			<span class="help"><?php echo JText::_('ESHOP_CONFIG_INVOICE_ENABLE_HELP'); ?></span>
		</td>
		<td>
			<?php echo $this->lists['invoice_enable']; ?>
		</td>
	</tr>
	<tr>
		<td width="50%">
			<?php echo JText::_('ESHOP_CONFIG_INVOICE_START_NUMBER'); ?>:<br>
			<span class="help"><?php echo JText::_('ESHOP_CONFIG_INVOICE_START_NUMBER_HELP'); ?></span>
		</td>
		<td>
			<input class="text_area" type="text" name="invoice_start_number" id="invoice_start_number" size="15" value="<?php echo $this->config->invoice_start_number; ?>" />
		</td>
	</tr>
	<tr>
		<td width="50%">
			<?php echo JText::_('ESHOP_CONFIG_RESET_INVOICE_NUMBER'); ?>:<br>
			<span class="help"><?php echo JText::_('ESHOP_CONFIG_RESET_INVOICE_NUMBER_HELP'); ?></span>
		</td>
		<td>
			<?php echo $this->lists['reset_invoice_number']; ?>
		</td>
	</tr>
	<tr>
		<td width="50%">
			<?php echo JText::_('ESHOP_CONFIG_INVOICE_PREFIX'); ?>:<br>
			<span class="help"><?php echo JText::_('ESHOP_CONFIG_INVOICE_PREFIX_HELP'); ?></span>
		</td>
		<td>
			<input class="text_area" type="text" name="invoice_prefix" id="invoice_prefix" size="15" value="<?php echo $this->config->invoice_prefix; ?>" />
		</td>
	</tr>
	<tr>
		<td width="50%">
			<?php echo JText::_('ESHOP_CONFIG_INVOICE_NUMBER_LENGTH'); ?>:<br>
			<span class="help"><?php echo JText::_('ESHOP_CONFIG_INVOICE_NUMBER_LENGTH_HELP'); ?></span>
		</td>
		<td>
			<input class="text_area" type="text" name="invoice_number_length" id="invoice_number_length" size="15" value="<?php echo $this->config->invoice_number_length; ?>" />
		</td>
	</tr>
	<tr>
		<td width="50%">
			<?php echo JText::_('ESHOP_CONFIG_INVOICE_SEND_TO_CUSTOMER'); ?>:<br>
			<span class="help"><?php echo JText::_('ESHOP_CONFIG_INVOICE_SEND_TO_CUSTOMER_HELP'); ?></span>
		</td>
		<td>
			<?php echo $this->lists['send_invoice_to_customer']; ?>
		</td>
	</tr>
</table>	