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
		if (pressbutton == 'coupon.cancel') {
			Joomla.submitform(pressbutton, form);
			return;				
		} else {
			//Validate the entered data before submitting
			if (form.coupon_name.value == '') {
				alert("<?php echo JText::_('ESHOP_ENTER_NAME'); ?>");
				form.coupon_name.focus();
				return;
			}
			if (form.coupon_start_date.value > form.coupon_end_date.value) {
				alert("<?php echo JText::_('ESHOP_DATE_VALIDATE'); ?>");
				form.coupon_start_date.focus();
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
			<li><a href="#history-page" data-toggle="tab"><?php echo JText::_('ESHOP_COUPON_HISTORY'); ?></a></li>
		</ul>
		<div class="tab-content" style="overflow: visible !important">
			<div class="tab-pane active" id="general-page">
				<div class="span8">
					<table class="admintable adminform" style="width: 100%;">
						<tr>
							<td class="key" width="20%">
								<span class="required">*</span>
								<?php echo  JText::_('ESHOP_COUPON_NAME'); ?>
							</td>
							<td width="35%">
								<input class="input-xlarge" type="text" name="coupon_name" id="coupon_name" maxlength="250" value="<?php echo $this->item->coupon_name; ?>" />
							</td>
							<td width="45%">
								<small><?php echo JText::_('ESHOP_COUPON_NAME_HELP'); ?></small>
							</td>
						</tr>
						<tr>
							<td class="key" width="20%">
								<?php echo  JText::_('ESHOP_CODE'); ?>
							</td>
							<td width="35%">
								<input class="input-large" type="text" name="coupon_code" id="coupon_code" maxlength="250" value="<?php echo $this->item->coupon_code; ?>" />
							</td>
							<td width="45%">
								<small><?php echo JText::_('ESHOP_CODE_HELP'); ?></small>
							</td>
						</tr>
						<tr>
							<td class="key" width="20%">
								<?php echo  JText::_('ESHOP_TYPE'); ?>
							</td>
							<td width="35%">
								<?php echo $this->lists['coupon_type']; ?>
							</td>
							<td width="45%">
								<small><?php echo JText::_('ESHOP_TYPE_HELP'); ?></small>
							</td>
						</tr>
						<tr>
							<td class="key" width="20%">
								<?php echo  JText::_('ESHOP_VALUE'); ?>
							</td>
							<td width="35%">
								<input class="input-small" type="text" name="coupon_value" id="coupon_value" maxlength="250" value="<?php echo number_format($this->item->coupon_value, 2); ?>" />
							</td>
							<td width="45%">
								<small><?php echo JText::_('ESHOP_VALUE_HELP'); ?></small>
							</td>
						</tr>
						<tr>
							<td class="key" width="20%">
								<?php echo  JText::_('ESHOP_MIN_TOTAL'); ?>
							</td>
							<td width="35%">
								<input class="input-small" type="text" name="coupon_min_total" id="coupon_min_total" maxlength="250" value="<?php echo number_format($this->item->coupon_min_total, 2); ?>" />
							</td>
							<td width="45%">
								<small><?php echo JText::_('ESHOP_MIN_TOTAL_HELP'); ?></small>
							</td>
						</tr>
						<tr>
							<td class="key" width="20%">
								<?php echo JText::_('ESHOP_SELECT_PRODUCTS'); ?>
							</td>
							<td width="35%">
								<?php echo $this->lists['product_id']; ?>
							</td>
							<td width="45%">
								<small><?php echo JText::_('ESHOP_SELECT_PRODUCTS_HELP'); ?></small>
							</td>
						</tr>
						<tr>
							<td class="key" width="20%">
								<?php echo JText::_('ESHOP_SELECT_CUSTOMER_GROUPS'); ?>
							</td>
							<td width="35%">
								<?php echo $this->lists['customergroup_id']; ?>
							</td>
							<td width="45%">
								<small><?php echo JText::_('ESHOP_SELECT_CUSTOMER_GROUPS_HELP'); ?></small>
							</td>
						</tr>
						<tr>
							<td class="key" width="20%">
								<?php echo  JText::_('ESHOP_START_DATE'); ?>
							</td>
							<td width="35%">
								<?php echo JHtml::_('calendar', (($this->item->coupon_start_date == $this->nullDate) ||  !$this->item->coupon_start_date) ? '' : JHtml::_('date', $this->item->coupon_start_date, 'Y-m-d', null), 'coupon_start_date', 'coupon_start_date', '%Y-%m-%d', array('style' => 'width: 100px;')); ?>
							</td>
							<td width="45%">
								<small><?php echo JText::_('ESHOP_COUPON_START_DATE_HELP'); ?></small>
							</td>
						</tr>
						<tr>
							<td class="key" width="20%">
								<?php echo  JText::_('ESHOP_END_DATE'); ?>
							</td>
							<td width="35%">
								<?php echo JHtml::_('calendar', (($this->item->coupon_end_date == $this->nullDate) ||  !$this->item->coupon_end_date) ? '' : JHtml::_('date', $this->item->coupon_end_date, 'Y-m-d', null), 'coupon_end_date', 'coupon_end_date', '%Y-%m-%d', array('style' => 'width: 100px;')); ?>
							</td>
							<td width="45%">
								<small><?php echo JText::_('ESHOP_COUPON_END_DATE_HELP'); ?></small>
							</td>
						</tr>
						<tr>
							<td class="key" width="20%">
								<?php echo JText::_('ESHOP_COUPON_SHIPPING'); ?>
							</td>
							<td width="35%">
								<?php echo $this->lists['coupon_shipping']; ?>
							</td>
							<td width="45%">
								<small><?php echo JText::_('ESHOP_COUPON_SHIPPING_HELP'); ?></small>
							</td>
						</tr>
						<tr>
							<td class="key" width="20%">
								<?php echo  JText::_('ESHOP_COUPON_TIME'); ?>
							</td>
							<td width="35%">
								<input class="input-small" type="text" name="coupon_times" id="coupon_times" maxlength="250" value="<?php echo $this->item->coupon_times; ?>" />
							</td>
							<td width="45%">
								<small><?php echo JText::_('ESHOP_COUPON_TIME_HELP'); ?></small>
							</td>
						</tr>
						<tr>
							<td class="key" width="20%">
								<?php echo  JText::_('ESHOP_COUPON_USED'); ?>
							</td>
							<td width="35%">
								<input class="input-small" type="text" name="coupon_used" id="coupon_used" maxlength="250" value="<?php echo $this->item->coupon_used; ?>" />
							</td>
							<td width="45%">
								<small><?php echo JText::_('ESHOP_COUPON_USED_HELP'); ?></small>
							</td>
						</tr>
						<tr>
							<td class="key" width="20%">
								<?php echo  JText::_('ESHOP_COUPON_PER_CUSTOMER'); ?>
							</td>
							<td width="35%">
								<input class="input-small" type="text" name="coupon_per_customer" id="coupon_per_customer" maxlength="250" value="<?php echo $this->item->coupon_per_customer; ?>" />
							</td>
							<td width="45%">
								<small><?php echo JText::_('ESHOP_COUPON_PER_CUSTOMER_HELP'); ?></small>
							</td>
						</tr>
						<tr>
							<td class="key" width="20%">
								<?php echo JText::_('ESHOP_PUBLISHED'); ?>
							</td>
							<td width="35%">
								<?php echo $this->lists['published']; ?>
							</td>
							<td width="45%">&nbsp;</td>
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
							$couponHistories = $this->couponHistories;
							if (count($couponHistories) == 0)
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
								for ($i = 0; $i< count($couponHistories); $i++)
								{
									$couponHistory = $couponHistories[$i];
									?>
									<tr>
										<td align="center">
											<?php echo $couponHistory->order_id; ?>
										</td>
										<td align="center">
											<?php echo number_format($couponHistory->amount, 2); ?>
										</td>
										<td align="center">
											<?php echo JHtml::_('date', $couponHistory->created_date, EshopHelper::getConfigValue('date_format', 'm-d-Y')); ?>
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