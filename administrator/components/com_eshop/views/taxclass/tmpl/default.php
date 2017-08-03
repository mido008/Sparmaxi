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
$editor = JFactory::getEditor(); 	
?>
<script type="text/javascript">	
	Joomla.submitbutton = function(pressbutton)
	{
		var form = document.adminForm;
		if (pressbutton == 'taxclass.cancel') {
			Joomla.submitform(pressbutton, form);
			return;				
		} else {
			//Validate the entered data before submitting
			if (form.taxclass_name.value == '') {
				alert("<?php echo JText::_('ESHOP_ENTER_NAME'); ?>");
				form.taxclass_name.focus();
				return;
			}
			Joomla.submitform(pressbutton, form);
		}
	}
	var rowIndex = <?php echo isset($this->taxrateIds) ? count($this->taxrateIds) : 0; ?>;			
	function addTaxGroup(){
		html = '<tr id="taxrate' + rowIndex + '">>';
		html += '<td><select name="tax_id[]">';				
		html += taxrateOptions;		   
		html += '</select></td>';
		html += '<td><select name="based_on[]">';				
		html += BaseonOptions;		   
		html += '</select></td>';
		html += '<td><input type="text" name="priority[]" size="5"></td>'
		html += '<td><input type="button" onclick="jQuery(\'#taxrate' + rowIndex + '\').remove();" class="btn btn-small btn-primary" value="<?php echo JText::_('ESHOP_BTN_REMOVE'); ?>"></td>';
		html += '</tr>';
		jQuery('#taxRate_list').append(html);
		rowIndex++;
 	}
</script>
<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
	<div class="row-fluid">
		<div class="span5">
			<fieldset class="admintable">
				<legend><?php echo JText::_('ESHOP_TAXCLASS_DETAILS'); ?></legend>
				<table class="admintable adminform" style="width: 100%;">
					<tr>
						<td width="220" class="key">
							<span class="required">*</span>
							<?php echo  JText::_('ESHOP_NAME'); ?>
						</td>
						<td>
							<input class="text_area" type="text" name="taxclass_name" id="taxclass_name" size="40" maxlength="250" value="<?php echo $this->item->taxclass_name; ?>" />
						</td>
					</tr>
					<tr>
						<td width="220" class="key">
							<?php echo  JText::_('ESHOP_DESCRIPTION'); ?>
						</td>
						<td>
							<textarea rows="5" cols="40" name="taxclass_desc"><?php echo $this->item->taxclass_desc; ?></textarea>
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
			</fieldset>
		</div>
		<div class="span7">
			<fieldset class="admintable">
				<legend><?php echo JText::_('ESHOP_TAX_RULES'); ?></legend>
				<table class="adminlist table table-bordered" style="text-align: center;">
					<thead>
						<tr>
							<th class="title" width="30%"><?php echo JText::_('ESHOP_TAX_RATE') ?></th>
							<th class="title" width="30%"><?php echo JText::_('ESHOP_BASED_ON') ?></th>
							<th class="title" width="30%"><?php echo JText::_('ESHOP_PRIORITY') ?></th>
							<th>&nbsp;</th>
						</tr>
					</thead>
					<tbody id="taxRate_list">
					<?php
						$rowIndex = 0;
						if(isset($this->taxrateIds) && count($this->taxrateIds))
						{
							foreach ($this->taxrateIds as $taxrateId)
							{
							?>							
								<tr id="taxrate<?php echo $rowIndex?>">
									<td class="text_center">
										<?php
											echo JHtml::_('select.genericlist', $this->taxrates, 'tax_id[]', ' class="inputbox"', 'id', 'name', $taxrateId->tax_id); 
										?>
									</td>
									<td>
										<?php
											echo JHtml::_('select.genericlist', $this->baseonOptions, 'based_on[]', ' class="inputbox" ', 'value', 'text', $taxrateId->based_on);
										?>
									</td>
									<td>
										<input type="text" name="priority[]" size="5" value="<?php echo $taxrateId->priority ?>">
									</td>
									<td>
										<input type="button" onclick="jQuery('#taxrate<?php echo $rowIndex?>').remove();" class="btn btn-small btn-primary" value="<?php echo JText::_('ESHOP_BTN_REMOVE'); ?>">
									</td>
								</tr>							
							<?php
								$rowIndex ++;
							}
						} 
					?>
					</tbody>
					<tfoot>
						<tr>
			              <td colspan="4">
			              	<input class="btn btn-small btn-primary" type="button" name="add" value="<?php echo JText::_('ESHOP_BTN_ADD')?>" onclick="addTaxGroup();" >
			              </td>
			            </tr>
				    </tfoot>
				</table>	
			</fieldset>
		</div>	
	</div>
	<?php echo JHtml::_( 'form.token' ); ?>
	<input type="hidden" name="option" value="com_eshop" />
	<input type="hidden" name="cid[]" value="<?php echo $this->item->id; ?>" />
	<input type="hidden" name="task" value="" />
</form>