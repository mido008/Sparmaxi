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
EshopHelper::chosen(); 
?>
<script type="text/javascript">	
	Joomla.submitbutton = function(pressbutton)
	{
		var form = document.adminForm;
		if (pressbutton == 'voucher.cancel') {
			Joomla.submitform(pressbutton, form);
			return;				
		} else {
			//Validate the entered data before submitting
			if (form.voucher_code.value == '') {
				alert("<?php echo JText::_('ESHOP_ENTER_VOUCHER_CODE'); ?>");
				form.voucher_code.focus();
				return;
			}
			if (form.voucher_start_date.value > form.voucher_end_date.value) {
				alert("<?php echo JText::_('ESHOP_DATE_VALIDATE'); ?>");
				form.voucher_start_date.focus();
				return;
			}
			Joomla.submitform(pressbutton, form);
		}
	}
</script>
<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
	<div class="row-fluid">
		<ul class="nav nav-tabs">
			<li class="active"><a href="#general-page" data-toggle="tab"><?php echo JText::_('ESHOP_GENERAL'); ?></a></li>
			<li><a href="#history-page" data-toggle="tab"><?php echo JText::_('ESHOP_VOUCHER_HISTORY'); ?></a></li>
		</ul>
		<div class="tab-content" style="overflow: visible !important">
			<div class="tab-pane active" id="general-page">
				<div class="span8">
					<table class="admintable adminform" style="width: 100%;">
						<tr>
							<td class="key">
								<?php echo  JText::_('ESHOP_CODE'); ?>
							</td>
							<td>
								<input class="input-large" type="text" name="voucher_code" id="voucher_code" maxlength="250" value="<?php echo $this->item->voucher_code; ?>" />
							</td>
						</tr>
						<tr>
							<td class="key">
								<?php echo  JText::_('ESHOP_AMOUNT'); ?>
							</td>
							<td>
								<input class="input-small" type="text" name="voucher_amount" id="voucher_amount" maxlength="250" value="<?php echo number_format($this->item->voucher_amount, 2); ?>" />
							</td>
						</tr>
						<tr>
							<td class="key">
								<?php echo  JText::_('ESHOP_START_DATE'); ?>
							</td>
							<td>
								<?php echo JHtml::_('calendar', (($this->item->voucher_start_date == $this->nullDate) ||  !$this->item->voucher_start_date) ? '' : JHtml::_('date', $this->item->voucher_start_date, 'Y-m-d', null), 'voucher_start_date', 'voucher_start_date', '%Y-%m-%d', array('style' => 'width: 100px;')); ?>
							</td>
						</tr>
						<tr>
							<td class="key">
								<?php echo  JText::_('ESHOP_END_DATE'); ?>
							</td>
							<td>
								<?php echo JHtml::_('calendar', (($this->item->voucher_end_date == $this->nullDate) ||  !$this->item->voucher_end_date) ? '' : JHtml::_('date', $this->item->voucher_end_date, 'Y-m-d', null), 'voucher_end_date', 'voucher_end_date', '%Y-%m-%d', array('style' => 'width: 100px;')); ?>
							</td>
						</tr>
						<tr>
							<td class="key">
								<?php echo JText::_('ESHOP_PUBLISHED'); ?>
							</td>
							<td>
								<?php echo $this->lists['published']; ?>
							</td>
						</tr>
					</table>
				</div>
			</div>
			<div class="tab-pane" id="history-page">
				<div class="span6">
					<table class="adminlist" style="text-align: center;">
						<thead>
							<tr>
								<th class="title" width="10%"><?php echo JText::_('ESHOP_ORDER_ID')?></th>
								<th class="title" width="30%"><?php echo JText::_('ESHOP_AMOUNT')?></th>
								<th class="title" width="20%"><?php echo JText::_('ESHOP_CREATED_DATE')?></th>
							</tr>
						</thead>
						<tbody>
							<?php
							$voucherHistories = $this->voucherHistories;
							if (count($voucherHistories) == 0)
							{
								?>
								<tr>
									<td colspan="3" style="text-align: center;">
										<?php echo JText::_('ESHOP_NO_RESULTS'); ?>
									</td>
								</tr>
								<?php
							}
							else
							{
								for ($i = 0; $i< count($voucherHistories); $i++)
								{
									$voucherHistory = $voucherHistories[$i];
									?>
									<tr>
										<td align="center">
											<?php echo $voucherHistory->order_id; ?>
										</td>
										<td align="center">
											<?php echo number_format($voucherHistory->amount, 2); ?>
										</td>
										<td align="center">
											<?php echo JHtml::_('date', $voucherHistory->created_date, EshopHelper::getConfigValue('date_format', 'm-d-Y')); ?>
										</td>
									</tr>
									<?php
								}
							}
							?>
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</div>
	<?php echo JHtml::_( 'form.token' ); ?>
	<input type="hidden" name="option" value="com_eshop" />
	<input type="hidden" name="cid[]" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="task" value="" />	
</form>