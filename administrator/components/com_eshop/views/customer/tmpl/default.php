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
		if (pressbutton == 'customer.cancel') {
			Joomla.submitform(pressbutton, form);
			return;
		} else {
			//Validate the entered data before submitting
			if (form.firstname.value == '') {
				alert("<?php echo JText::_('ESHOP_ENTER_FIRSTNAME'); ?>");
				form.firstname.focus();
				return;
			}
			if (form.lastname.value == '') {
				alert("<?php echo JText::_('ESHOP_ENTER_LASTNAME'); ?>");
				form.lastname.focus();
				return;
			}
			if (form.email.value == '') {
				alert("<?php echo JText::_('ESHOP_ENTER_EMAIL'); ?>");
				form.email.focus();
				return;
			}
			if (form.telephone.value == '') {
				alert("<?php echo JText::_('ESHOP_ENTER_TELEPHONE'); ?>");
				form.telephone.focus();
				return;
			}
			<?php
			if (!$this->item->id)
			{
				?>
				if (form.username.value == '') {
					alert("<?php echo JText::_('ESHOP_ENTER_USERNAME'); ?>");
					form.username.focus();
					return;
				}
				if (form.password.value == '') {
					alert("<?php echo JText::_('ESHOP_ENTER_PASSWORD'); ?>");
					form.password.focus();
					return;
				}
				<?php
			}
			?>
			Joomla.submitform(pressbutton, form);
		}
	}
	<?php
	$addresses = array();
	for ($i = 0; $n = count($this->addresses), $i < $n; $i++)
	{
		$addresses[] = '"'.$this->addresses[$i]->id.'"';
	}
	?>
	var addresses = new Array(<?php echo implode($addresses, ','); ?>);
	var countAddresses = "<?php echo $n; ?>";
	function addAddress() {
		countAddresses++;
		// Change active tab
  		for (var i = 1; i < countAddresses; i++) {
			jQuery('#address_' + i).attr('class', '');
			jQuery('#address-' + i + '-page').attr('class', 'tab-pane');
  	  	}
  	  	// Tab on the left
		var htmlTab = '<li id="address_' + countAddresses + '" class="active">';
		htmlTab += '<a data-toggle="tab" href="#address-' + countAddresses + '-page"><?php echo JText::_('ESHOP_ADDRESS'); ?> ' + countAddresses;
		htmlTab += '<img onclick="removeAddress(' + countAddresses + ');" src="<?php echo JURI::base(); ?>components/com_eshop/assets/images/remove.png" />';
		htmlTab += '</a>';
		htmlTab += '<input type="hidden" name="address[' + countAddresses + '][id]" value=""/>';
		htmlTab += '</li>';
		jQuery('#nav-tabs').append(htmlTab);
		// Content on the right
		var htmlContent = '<div id="address-' + countAddresses + '-page" class="tab-pane active">';
		htmlContent += '<table style="width: 100%;" class="admintable adminform">';
		htmlContent += '<tr>';
		htmlContent += '<td class="key"><?php echo JText::_('ESHOP_FIRST_NAME'); ?></td>';
		htmlContent += '<td><input class="input-memdium" type="text" name="address[' + countAddresses + '][firstname]" maxlength="32" value="" /></td>'
		htmlContent += '</tr>';
		htmlContent += '<tr>';
		htmlContent += '<td class="key"><?php echo JText::_('ESHOP_LAST_NAME'); ?></td>';
		htmlContent += '<td><input class="input-memdium" type="text" name="address[' + countAddresses + '][lastname]" maxlength="32" value="" /></td>'
		htmlContent += '</tr>';
		htmlContent += '<td class="key"><?php echo JText::_('ESHOP_EMAIL'); ?></td>';
		htmlContent += '<td><input class="input-memdium" type="text" name="address[' + countAddresses + '][email]" maxlength="32" value="" /></td>'
		htmlContent += '</tr>';
		htmlContent += '<td class="key"><?php echo JText::_('ESHOP_TELEPHONE'); ?></td>';
		htmlContent += '<td><input class="input-memdium" type="text" name="address[' + countAddresses + '][telephone]" maxlength="32" value="" /></td>'
		htmlContent += '</tr>';
		htmlContent += '<td class="key"><?php echo JText::_('ESHOP_FAX'); ?></td>';
		htmlContent += '<td><input class="input-memdium" type="text" name="address[' + countAddresses + '][fax]" maxlength="32" value="" /></td>'
		htmlContent += '</tr>';
		htmlContent += '<tr>';
		htmlContent += '<td class="key"><?php echo JText::_('ESHOP_COMPANY'); ?></td>';
		htmlContent += '<td><input class="input-memdium" type="text" name="address[' + countAddresses + '][company]" maxlength="100" value="" /></td>'
		htmlContent += '</tr>';
		htmlContent += '<tr>';
		htmlContent += '<td class="key"><?php echo JText::_('ESHOP_COMPANY_ID'); ?></td>';
		htmlContent += '<td><input class="input-memdium" type="text" name="address[' + countAddresses + '][company_id]" maxlength="32" value="" /></td>'
		htmlContent += '</tr>';
		htmlContent += '<tr>';
		htmlContent += '<td class="key"><?php echo JText::_('ESHOP_ADDRESS_1'); ?></td>';
		htmlContent += '<td><input class="input-memdium" type="text" name="address[' + countAddresses + '][address_1]" maxlength="128" value="" /></td>'
		htmlContent += '</tr>';
		htmlContent += '<tr>';
		htmlContent += '<td class="key"><?php echo JText::_('ESHOP_ADDRESS_2'); ?></td>';
		htmlContent += '<td><input class="input-memdium" type="text" name="address[' + countAddresses + '][address_2]" maxlength="128" value="" /></td>'
		htmlContent += '</tr>';
		htmlContent += '<tr>';
		htmlContent += '<td class="key"><?php echo JText::_('ESHOP_CITY'); ?></td>';
		htmlContent += '<td><input class="input-memdium" type="text" name="address[' + countAddresses + '][city]" maxlength="128" value="" /></td>'
		htmlContent += '</tr>';
		htmlContent += '<tr>';
		htmlContent += '<td class="key"><?php echo JText::_('ESHOP_POST_CODE'); ?></td>';
		htmlContent += '<td><input class="input-memdium" type="text" name="address[' + countAddresses + '][postcode]" maxlength="10" value="" /></td>'
		htmlContent += '</tr>';
		htmlContent += '<tr>';
		htmlContent += '<td class="key"><?php echo JText::_('ESHOP_COUNTRY'); ?></td>';
		htmlContent += '<td><select class="inputbox" onchange="country(this, \'' + countAddresses + '\', \'\')" name="address[' + countAddresses + '][country_id]" id="country_id">' + jQuery('#country_id').html() + '</select></td>';
		htmlContent += '</tr>';
		htmlContent += '</tr>';
		htmlContent += '<tr>';
		htmlContent += '<td class="key"><?php echo JText::_('ESHOP_REGION_STATE'); ?></td>';
		htmlContent += '<td><select class="inputbox" name="address[' + countAddresses + '][zone_id]" id="zone_id">' + jQuery('#zone_id').html() + '</select></td>';
		htmlContent += '</tr>';
		htmlContent += '</table>';
		htmlContent += '</div>';
  		jQuery('#tab-content').append(htmlContent);
	}

	function removeAddress(addressIndex)
	{
		jQuery('#address_' + addressIndex).remove();
		jQuery('#address-' + addressIndex + '-page').remove();
	}
	function country(element, index, zoneId) {
		jQuery.ajax({
			url: 'index.php?option=com_eshop&task=customer.country&country_id=' + element.value,
			dataType: 'json',
			beforeSend: function() {
				jQuery('select[name=\'address[' + index + '][country_id]\']').after('<span class="wait">&nbsp;<img src="<?php echo JURI::root(); ?>administrator/components/com_eshop/assets/images/loading.gif" alt="" /></span>');
			},
			complete: function() {
				jQuery('.wait').remove();
			},
			success: function(json) {
				html = '<option value="0"><?php echo JText::_('ESHOP_PLEASE_SELECT'); ?></option>';
				if (json['zones'] != '') {
					for (i = 0; i < json['zones'].length; i++) {
	        			html += '<option value="' + json['zones'][i]['id'] + '"';
						if (json['zones'][i]['id'] == zoneId) {
		      				html += ' selected="selected"';
		    			}
		    			html += '>' + json['zones'][i]['zone_name'] + '</option>';
					}
				}
				jQuery('select[name=\'address[' + index + '][zone_id]\']').html(html);
			},
			error: function(xhr, ajaxOptions, thrownError) {
				alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
			}
		});
	}
</script>
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<div class="row-fluid">
		<ul class="nav nav-tabs">
			<li class="active"><a href="#general-page" data-toggle="tab"><?php echo JText::_('ESHOP_GENERAL'); ?></a></li>
			<li><a href="#address-page" data-toggle="tab"><?php echo JText::_('ESHOP_ADDRESS'); ?></a></li>
		</ul>
		<div class="tab-content" style="overflow: visible !important">
			<div class="tab-pane active" id="general-page">
				<div class="span8">
					<table class="admintable adminform" style="width: 100%;">
						<tr>
							<td class="key">
								<span class="required">*</span>
								<?php echo JText::_('ESHOP_FIRST_NAME'); ?>
							</td>
							<td>
								<input class="input-memdium" type="text" name="firstname" id="firstname" maxlength="32" value="<?php echo $this->item->firstname; ?>" />
							</td>
						</tr>
						<tr>
							<td class="key">
								<span class="required">*</span>
								<?php echo JText::_('ESHOP_LAST_NAME'); ?>
							</td>
							<td>
								<input class="input-memdium" type="text" name="lastname" id="lastname" maxlength="32" value="<?php echo $this->item->lastname; ?>" />
							</td>
						</tr>
						<tr>
							<td class="key">
								<span class="required">*</span>
								<?php echo JText::_('ESHOP_EMAIL'); ?>
							</td>
							<td>
								<input class="input-memdium" type="text" name="email" id="email" maxlength="96" value="<?php echo $this->item->email; ?>" />
							</td>
						</tr>
						<tr>
							<td class="key">
								<span class="required">*</span>
								<?php echo JText::_('ESHOP_TELEPHONE'); ?>
							</td>
							<td>
								<input class="input-memdium" type="text" name="telephone" id="telephone" maxlength="32" value="<?php echo $this->item->telephone; ?>" />
							</td>
						</tr>
						<tr>
							<td class="key">
								<?php echo JText::_('ESHOP_FAX'); ?>
							</td>
							<td>
								<input class="input-memdium" type="text" name="fax" id="fax" maxlength="32" value="<?php echo $this->item->fax; ?>" />
							</td>
						</tr>
						<tr>
							<td class="key">
								<?php echo JText::_('ESHOP_CUSTOMERGROUP'); ?>
							</td>
							<td>
								<?php echo $this->lists['customergroup_id']; ?>
							</td>
						</tr>
						<?php 
						if (!$this->item->id)
						{
						?>
							<tr>
								<td class="key">
									<span class="required">*</span>
									<?php echo JText::_('ESHOP_USERNAME'); ?>
								</td>
								<td>
									<input class="input-memdium" type="text" name="username" maxlength="150" value="" />
								</td>
							</tr>
							<tr>
								<td class="key">
									<span class="required">*</span>
									<?php echo JText::_('ESHOP_PASSWORD'); ?>
								</td>
								<td>
									<input class="input-memdium" type="password" name="password" maxlength="100" value="" />
								</td>
							</tr>
						<?php	
						}
						?>
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
			<div class="tab-pane" id="address-page">
				<div class="span10">
					<div class="tabbable tabs-left">
						<?php echo $this->lists['country_id']; ?>
						<?php echo $this->lists['zone_id']; ?>
						<ul class="nav nav-tabs" id="nav-tabs">
							<li>
								<a style="cursor: pointer;" onclick="addAddress();">
									<?php echo JText::_('ESHOP_ADD_ADDRESS'); ?>
									<img alt="<?php echo JText::_('ESHOP_ADD_ADDRESS'); ?>" src="<?php echo JURI::base(); ?>components/com_eshop/assets/images/add.png" />
								</a>	
							</li>
							<?php
							for ($i = 0; $n = count($this->addresses), $i < $n; $i++)
							{
								$address = $this->addresses[$i];
								?>
								<li <?php echo ($i == 0) ? 'class="active"' : 0; ?> id="address_<?php echo ($i + 1); ?>">
									<a href="#address-<?php echo ($i + 1); ?>-page" data-toggle="tab">
										<?php echo JText::_('ESHOP_ADDRESS') . ' ' . ($i + 1); ?>
										<img src="<?php echo JURI::base(); ?>components/com_eshop/assets/images/remove.png" onclick="removeAddress(<?php echo ($i + 1); ?>);" />
									</a>
									<input type="hidden" name="address[<?php echo $i + 1; ?>][id]" value="<?php echo $address->id; ?>"/>
								</li>
								<?php
							}
							?>
						</ul>	
						<div class="tab-content" id="tab-content">
							<?php
							for ($i = 0; $n = count($this->addresses), $i < $n; $i++)
							{
								$address = $this->addresses[$i];
								?>
								<div class="tab-pane<?php echo ($i == 0) ? ' active' : ''; ?>" id="address-<?php echo ($i + 1); ?>-page">
									<table class="admintable adminform" style="width: 100%;">
										<tr>
											<td class="key">
												<?php echo JText::_('ESHOP_FIRST_NAME'); ?>
											</td>
											<td>
												<input class="input-memdium" type="text" name="address[<?php echo $i + 1; ?>][firstname]" maxlength="32" value="<?php echo $address->firstname; ?>" />
											</td>
										</tr>
										<tr>
											<td class="key">
												<?php echo JText::_('ESHOP_LAST_NAME'); ?>
											</td>
											<td>
												<input class="input-memdium" type="text" name="address[<?php echo $i + 1; ?>][lastname]" maxlength="32" value="<?php echo $address->lastname; ?>" />
											</td>
										</tr>
										<tr>
											<td class="key">
												<?php echo JText::_('ESHOP_EMAIL'); ?>
											</td>
											<td>
												<input class="input-memdium" type="text" name="address[<?php echo $i + 1; ?>][email]" maxlength="32" value="<?php echo $address->email; ?>" />
											</td>
										</tr>
										<tr>
											<td class="key">
												<?php echo JText::_('ESHOP_TELEPHONE'); ?>
											</td>
											<td>
												<input class="input-memdium" type="text" name="address[<?php echo $i + 1; ?>][telephone]" maxlength="32" value="<?php echo $address->telephone; ?>" />
											</td>
										</tr>
										<tr>
											<td class="key">
												<?php echo JText::_('ESHOP_FAX'); ?>
											</td>
											<td>
												<input class="input-memdium" type="text" name="address[<?php echo $i + 1; ?>][fax]" maxlength="32" value="<?php echo $address->fax; ?>" />
											</td>
										</tr>
										<tr>
											<td class="key">
												<?php echo JText::_('ESHOP_COMPANY'); ?>
											</td>
											<td>
												<input class="input-memdium" type="text" name="address[<?php echo $i + 1; ?>][company]" maxlength="100" value="<?php echo $address->company; ?>" />
											</td>
										</tr>
										<tr>
											<td class="key">
												<?php echo JText::_('ESHOP_COMPANY_ID'); ?>
											</td>
											<td>
												<input class="input-memdium" type="text" name="address[<?php echo $i + 1; ?>][company_id]" maxlength="32" value="<?php echo $address->company_id; ?>" />
											</td>
										</tr>
										<tr>
											<td class="key">
												<?php echo JText::_('ESHOP_ADDRESS_1'); ?>
											</td>
											<td>
												<input class="input-medium" type="text" name="address[<?php echo $i + 1; ?>][address_1]" maxlength="128" value="<?php echo $address->address_1; ?>" />
											</td>
										</tr>
										<tr>
											<td class="key">
												<?php echo JText::_('ESHOP_ADDRESS_2'); ?>
											</td>
											<td>
												<input class="input-medium" type="text" name="address[<?php echo $i + 1; ?>][address_2]" maxlength="128" value="<?php echo $address->address_2; ?>" />
											</td>
										</tr>
										<tr>
											<td class="key">
												<?php echo JText::_('ESHOP_CITY'); ?>
											</td>
											<td>
												<input class="input-memdium" type="text" name="address[<?php echo $i + 1; ?>][city]" maxlength="128" value="<?php echo $address->city; ?>" />
											</td>
										</tr>
										<tr>
											<td class="key">
												<?php echo JText::_('ESHOP_POST_CODE'); ?>
											</td>
											<td>
												<input class="input-memdium" type="text" name="address[<?php echo $i + 1; ?>][postcode]" maxlength="10" value="<?php echo $address->postcode; ?>" />
											</td>
										</tr>
										<tr>
											<td class="key">
												<?php echo JText::_('ESHOP_COUNTRY'); ?>
											</td>
											<td>
												<?php echo $this->lists['country_id_' . $address->id]; ?>
											</td>
										</tr>
										<tr>
											<td class="key">
												<?php echo JText::_('ESHOP_REGION_STATE'); ?>
											</td>
											<td>
												<?php echo $this->lists['zone_id_' . $address->id]; ?>
											</td>
										</tr>
									</table>
								</div>
								<?php
							}
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="clearfix"></div>
	<?php echo JHtml::_( 'form.token' ); ?>
	<input type="hidden" name="option" value="com_eshop" />
	<input type="hidden" name="cid[]" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="customer_id" value="<?php echo $this->item->customer_id; ?>" />
	<input type="hidden" name="task" value="" />
</form>