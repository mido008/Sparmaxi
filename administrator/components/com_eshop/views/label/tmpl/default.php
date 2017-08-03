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
$translatable = JLanguageMultilang::isEnabled() && count($this->languages) > 1;
EshopHelper::chosen();
?>
<script type="text/javascript">	
	Joomla.submitbutton = function(pressbutton)
	{
		var form = document.adminForm;
		if (pressbutton == 'label.cancel') {
			Joomla.submitform(pressbutton, form);
			return;
		} else {
			//Validate the entered data before submitting
			<?php
			if ($translatable)
			{
				foreach ($this->languages as $language)
				{
					$langId = $language->lang_id;
					?>
					if (document.getElementById('label_name_<?php echo $langId; ?>').value == '') {
						alert("<?php echo JText::_('ESHOP_ENTER_NAME'); ?>");
						document.getElementById('label_name_<?php echo $langId; ?>').focus();
						return;
					}
					<?php
				}
			}
			else
			{
				?>
				if (form.label_name.value == '') {
					alert("<?php echo JText::_('ESHOP_ENTER_NAME'); ?>");
					form.label_name.focus();
					return;
				}
				<?php
			}
			?>
			if (form.label_start_date.value > form.label_end_date.value) {
				alert("<?php echo JText::_('ESHOP_DATE_VALIDATE'); ?>");
				form.label_start_date.focus();
				return;
			}
			Joomla.submitform(pressbutton, form);
		}
	}
</script>
<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
	<div class="row-fluid">
		<div class="span6">
			<table class="admintable adminform" style="width: 100%;">
				<tr>
					<td class="key" width="20%">
						<span class="required">*</span>
						<?php echo  JText::_('ESHOP_NAME'); ?>
					</td>
					<td>
						<?php
						if ($translatable)
						{
							foreach ($this->languages as $language)
							{
								$langId = $language->lang_id;
								$langCode = $language->lang_code;
								?>
								<input class="input-xlarge" type="text" name="label_name_<?php echo $langCode; ?>" id="label_name_<?php echo $langId; ?>" size="" maxlength="255" value="<?php echo isset($this->item->{'label_name_'.$langCode}) ? $this->item->{'label_name_'.$langCode} : ''; ?>" />
								<img src="<?php echo JURI::root(); ?>media/com_eshop/flags/<?php echo $this->languageData['flag'][$langCode]; ?>" />
								<br />
								<?php
							}
						}
						else 
						{
							?>
							<input class="input-xlarge" type="text" name="label_name" id="label_name" maxlength="255" value="<?php echo $this->item->label_name; ?>" />
							<?php
						}
						?>
					</td>
				</tr>
				<tr>
					<td class="key" width="20%">
						<?php echo JText::_('ESHOP_LABEL_STYLE'); ?>
					</td>
					<td>
						<?php echo $this->lists['label_style']; ?>
					</td>
				</tr>
				<tr>
					<td class="key" width="20%">
						<?php echo JText::_('ESHOP_LABEL_POSITION'); ?>
					</td>
					<td>
						<?php echo $this->lists['label_position']; ?>
					</td>
				</tr>
				<tr>
					<td class="key" width="20%">
						<?php echo JText::_('ESHOP_LABEL_BOLD'); ?>
					</td>
					<td>
						<?php echo $this->lists['label_bold']; ?>
					</td>
				</tr>
				<tr>
					<td class="key" width="20%">
						<?php echo JText::_('ESHOP_LABEL_BACKGROUND_COLOR'); ?>
					</td>
					<td>
						<input type="text" name="label_background_color" class="inputbox color {required:false}" value="<?php echo $this->item->label_background_color; ?>" size="5" />
					</td>
				</tr>
				<tr>
					<td class="key" width="20%">
						<?php echo JText::_('ESHOP_LABEL_FOREGROUND_COLOR'); ?>
					</td>
					<td>
						<input type="text" name="label_foreground_color" class="inputbox color {required:false}" value="<?php echo $this->item->label_foreground_color; ?>" size="5" />
					</td>
				</tr>
				<tr>
					<td class="key" width="20%">
						<?php echo JText::_('ESHOP_LABEL_OPACITY'); ?>
					</td>
					<td>
						<?php echo $this->lists['label_opacity']; ?>
					</td>
				</tr>
				<tr>
					<td class="key" width="20%">
						<?php echo JText::_('ESHOP_ENABLE_IMAGE'); ?>
					</td>
					<td>
						<?php echo $this->lists['enable_image']; ?>
					</td>
				</tr>
				<tr>
					<td class="key" width="20%">
						<?php echo JText::_('ESHOP_LABEL_IMAGE'); ?>
					</td>
					<td>
						<input type="file" class="input-large" accept="image/*" name="label_image" />								
						<?php
						if (JFile::exists(JPATH_ROOT.'/media/com_eshop/labels/'.$this->item->label_image))
						{
							$imageWidth = $this->item->label_image_width > 0 ? $this->item->label_image_width : EshopHelper::getConfigValue('label_image_width');
							if (!$imageWidth)
								$imageWidth = 40;
							$imageHeight = $this->item->label_image_height > 0 ? $this->item->label_image_height : EshopHelper::getConfigValue('label_image_height');
							if (!$imageHeight)
								$imageHeight = 40;
							$viewImage = JFile::stripExt($this->item->label_image).'-'.$imageWidth.'x'.$imageHeight.'.'.JFile::getExt($this->item->label_image);
							if (Jfile::exists(JPATH_ROOT.'/media/com_eshop/labels/resized/'.$viewImage))
							{
								?>
								<img src="<?php echo JURI::root().'media/com_eshop/labels/resized/'.$viewImage; ?>" />
								<?php
							}
							else 
							{
								?>
								<img src="<?php echo JURI::root().'media/com_eshop/labels/'.$this->item->label_image; ?>" height="100" />
								<?php
							}
							?>
							<label class="checkbox">
								<input type="checkbox" name="remove_image" value="1" />
								<?php echo JText::_('ESHOP_REMOVE_IMAGE'); ?>
							</label>
							<?php
						}
						?>
					</td>
				</tr>
				<tr>
					<td class="key" width="20%">
						<?php echo JText::_('ESHOP_LABEL_IMAGE_WIDTH'); ?>
					</td>
					<td>
						<input type="text" class="input-large" name="label_image_width" id="label_image_width" value="<?php echo $this->item->label_image_width; ?>" />
					</td>
				</tr>
				<tr>
					<td class="key" width="20%">
						<?php echo JText::_('ESHOP_LABEL_IMAGE_HEIGHT'); ?>
					</td>
					<td>
						<input type="text" class="input-large" name="label_image_height" id="label_image_height" value="<?php echo $this->item->label_image_height; ?>" />
					</td>
				</tr>
				<tr>
					<td class="key" width="20%">
						<?php echo JText::_('ESHOP_SELECT_PRODUCTS'); ?>
					</td>
					<td>
						<?php echo $this->lists['products']; ?>
					</td>
				</tr>
				<tr>
					<td class="key" width="20%">
						<?php echo JText::_('ESHOP_SELECT_MANUFACTURERS'); ?>
					</td>
					<td>
						<?php echo $this->lists['manufacturers']; ?>
					</td>
				</tr>
				<tr>
					<td class="key" width="20%">
						<?php echo JText::_('ESHOP_CATEGORIES'); ?>
					</td>
					<td>
						<?php echo $this->lists['categories']; ?>
					</td>
				</tr>
				<tr>
					<td class="key" width="20%">
						<?php echo  JText::_('ESHOP_START_DATE'); ?>
					</td>
					<td width="35%">
						<?php echo JHtml::_('calendar', (($this->item->label_start_date == $this->nullDate) ||  !$this->item->label_start_date) ? '' : JHtml::_('date', $this->item->label_start_date, 'Y-m-d', null), 'label_start_date', 'label_start_date', '%Y-%m-%d', array('style' => 'width: 100px;')); ?>
					</td>
				</tr>
				<tr>
					<td class="key" width="20%">
						<?php echo  JText::_('ESHOP_END_DATE'); ?>
					</td>
					<td width="35%">
						<?php echo JHtml::_('calendar', (($this->item->label_end_date == $this->nullDate) ||  !$this->item->label_end_date) ? '' : JHtml::_('date', $this->item->label_end_date, 'Y-m-d', null), 'label_end_date', 'label_end_date', '%Y-%m-%d', array('style' => 'width: 100px;')); ?>
					</td>
				</tr>
				<tr>
					<td class="key" width="20%">
						<?php echo JText::_('ESHOP_PUBLISHED'); ?>
					</td>
					<td>
						<?php echo $this->lists['published']; ?>
					</td>
				</tr>
			</table>
		</div>
	</div>
	<?php echo JHtml::_( 'form.token' ); ?>
	<input type="hidden" name="option" value="com_eshop" />
	<input type="hidden" name="cid[]" value="<?php echo $this->item->id; ?>" />
	<?php
	if ($translatable)
	{
		foreach ($this->languages as $language)
		{
			$langCode = $language->lang_code;
			?>
			<input type="hidden" name="details_id_<?php echo $langCode; ?>" value="<?php echo isset($this->item->{'details_id_' . $langCode}) ? $this->item->{'details_id_' . $langCode} : ''; ?>" />
			<?php
		}
	}
	elseif ($this->translatable)
	{
	?>
		<input type="hidden" name="details_id" value="<?php echo isset($this->item->{'details_id'}) ? $this->item->{'details_id'} : ''; ?>" />
		<?php
	}
	?>
	<input type="hidden" name="task" value="" />
</form>