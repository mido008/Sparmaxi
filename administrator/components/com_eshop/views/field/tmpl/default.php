<?php
/**
 * @version		1.4.1
 * @package		Joomla
 * @subpackage	ESshop
 * @author  	Giang Dinh Truong
 * @copyright	Copyright (C) 2012 Ossolution Team
 * @license		GNU/GPL, see LICENSE.php
 */
// no direct access
defined( '_JEXEC' ) or die();
EshopHelper::chosen();
$editor = JFactory::getEditor();
$translatable = JLanguageMultilang::isEnabled() && count($this->languages) > 1;
?>
<script type="text/javascript">	
	function sanitizeFieldName() 
	{
		var form = document.adminForm ;
		var name = form.name.value ;								
		name = name.replace(/[^a-zA-Z0-9_]*/ig, '');
		form.name.value = name;							
	}
	function buildValidationString()
	{
		(function($) {
            var rules = $('#validation_rule').val();            
            var currentRules = $('#validation_rules_string').val();
            var required = $("input[name='required']:checked").val();					                                                
            var newRules = [];                         
            if (required == 1)
            {
            	newRules.push('required');
            } 
            if (currentRules == '')
            {                                
                if (rules)
                {
                	rules = rules.join('|');
                	newRules.push(rules);    
                }                                
            }          
            else 
            {
                if (rules)
                {
                	for (var i = 0; i < rules.length; i++)
                    {
                        rule = rules[i];    
                        commaIndex = rule.indexOf(',');           
                        if (commaIndex == -1)
                        {
                             //This rule has no param, just push it to new rule
                        	newRules.push(rule);       	    
                        }
                        else 
                        {         
                            //This rule has param, try to find param value from the entered rule                                            	           
                            ruleName = rule.substr(0, commaIndex);
                         	ruleIndex = currentRules.indexOf(ruleName);
                         	if (ruleIndex ==  -1)
                         	{
                             	//There is no rule in this current rule yet, so use default param
                         		newRules.push(rule);                     		    
                         	} 
                         	else 
                         	{
                             	//Get the current rule param
                             	var remainingRules = currentRules.substr(ruleIndex);
                             	if (remainingRules.indexOf('|') != -1)
                             	{                             	
                             		remainingRules = remainingRules.substr(0, remainingRules.indexOf('|'));
                             	}                     
                             	if(remainingRules.indexOf(',') != -1)
                             	{
                                 	commaIndex = remainingRules.indexOf(',');
                                 	param = remainingRules.substr(commaIndex + 1);
                                 	newRules.push(ruleName + ',' + param);                              	
                             	}
                             	else 
                             	{
                             		newRules.push(rule);
                             	}
                         	}                		                  	   
                        }
                	}        
                }            	           
            }            
            var validationRulesString = newRules.join('|');
            if (validationRulesString.length)
            {
            	if (validationRulesString.charAt(validationRulesString.length - 1) == '|')
            	{
            		validationRulesString = validationRulesString.substr(0, validationRulesString.length - 1);
            	}	    	
            }
            $('#validation_rules_string').val(validationRulesString);
        })(jQuery);
	}	
	Joomla.submitbutton = function(pressbutton)
	{
		var form = document.adminForm;
		if (pressbutton == 'field.cancel') {
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
					if (document.getElementById('title_<?php echo $langId; ?>').value == '') {
						alert("<?php echo JText::_('ESHOP_ENTER_TITLE'); ?>");
						document.getElementById('title_<?php echo $langId; ?>').focus();
						return;
					}
					if(jQuery('.field-value').hasClass('required-value'))
					{
						if (document.getElementById('values_<?php echo $langId; ?>').value == '') {
							alert("<?php echo JText::_('ESHOP_ENTER_VALUE'); ?>");
							document.getElementById('values_<?php echo $langId; ?>').focus();
							return;
						}	
					}
					<?php
				}
			}
			else
			{
			?>
				if (form.title.value == '') 
				{
					alert("<?php echo JText::_('ESHOP_ENTER_TITLE'); ?>");
					form.title.focus();
					return;
				}
				if(jQuery('.field-value').hasClass('required-value'))
				{
					if (form.values.value == '') 
					{
						alert("<?php echo JText::_('ESHOP_ENTER_VALUES'); ?>");
						form.values.focus();
						return;
					}
				}
				<?php
			}
			?>
			if (form.name.value == '')
			{
				alert("<?php echo JText::_('ESHOP_ENTER_NAME'); ?>");
				form.name.focus();
				return;
			}
			Joomla.submitform(pressbutton, form);
		}
	}

	function changeField(value)
	{
		if(value == 'List' || value == 'Checkboxes' || value == 'Radio')
		{
			jQuery('.field-value').addClass('required-value');
			jQuery('#required-value').before('<span class="eshop-show-hide required">*</span>');
		}
		else
		{
			jQuery('.field-value').removeClass('required-value');
			jQuery('.eshop-show-hide').remove();
		}
	}
	<?php if ($this->item->id) :?>
	(function($){
		$(document).ready(function(){
			fieldValue = $('#fieldtype').val();
			changeField(fieldValue);
		})
	})(jQuery)
	<?php endif;?>
</script>
<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
	<div class="row-fluid">
		<ul class="nav nav-tabs">
			<li class="active"><a href="#general-page" data-toggle="tab"><?php echo JText::_('ESHOP_GENERAL'); ?></a></li>		
			<li><a href="#data-page" data-toggle="tab"><?php echo JText::_('ESHOP_DATA'); ?></a></li>					
		</ul>
		<div class="tab-content">
			<div class="tab-pane active" id="general-page">
				<div class="span10">
				<?php
				if ($translatable) {
					?>
					<ul class="nav nav-tabs">
						<?php
							$i = 0;
							foreach ($this->languages as $language) 
							{
								$langCode = $language->lang_code;
								?>
								<li <?php echo $i == 0 ? 'class="active"' : ''; ?>><a href="#general-page-<?php echo $langCode; ?>" data-toggle="tab"><?php echo $this->languageData['title'][$langCode]; ?>
								<img src="<?php echo JURI::root(); ?>media/com_eshop/flags/<?php echo $this->languageData['flag'][$langCode]; ?>" /></a></li>
								<?php
								$i++;	
							}
						?>																					
					</ul>
					<div class="tab-content">
					<?php
						$i = 0;
						foreach ($this->languages as $language)
						{
							$langId = $language->lang_id;
							$langCode = $language->lang_code;
						?>
							<div class="tab-pane<?php echo $i == 0 ? ' active' : ''; ?>" id="general-page-<?php echo $langCode; ?>">													
								<table class="admintable adminform" style="width: 100%;">
									<tr>
										<td class="key" width="40%">
											<span class="required">*</span>
											<?php echo  JText::_('ESHOP_TITLE'); ?>
										</td>
										<td>
											<input class="input-xlarge" type="text" name="title_<?php echo $langCode; ?>" id="title_<?php echo $langId; ?>" size="" maxlength="250" value="<?php echo isset($this->item->{'title_'.$langCode}) ? $this->item->{'title_'.$langCode} : ''; ?>" />
										</td>								
									</tr>																												
									<tr>
										<td class="key">
											<?php echo JText::_('ESHOP_DESCRIPTION'); ?>
										</td>
										<td>
											<textarea rows="5" cols="30" name="description_<?php echo $langCode; ?>"><?php echo $this->item->{'description_'.$langCode}; ?></textarea>
										</td>								
									</tr>
									<tr>
										<td class="key">
											<span id="required-value"><?php echo JText::_('ESHOP_VALUES'); ?></span>
										</td>
										<td>
											<textarea class="field-value" rows="5" cols="30" name="values_<?php echo $langCode; ?>" id="values_<?php echo $langId; ?>"><?php echo $this->item->{'values_'.$langCode}; ?></textarea>
										</td>
									</tr>			
									<tr>
										<td class="key">
											<?php echo JText::_('ESHOP_DEFAULT_VALUES'); ?>
										</td>
										<td>
											<textarea rows="5" cols="30" name="default_values_<?php echo $langCode; ?>"><?php echo $this->item->{'default_values_'.$langCode}; ?></textarea>
										</td>
									</tr>
									<tr>
										<td class="key">
											<?php echo JText::_('ESHOP_VALIDATION_ERORR_MESSAGE'); ?>
										</td>
										<td>
											<input class="input-xlarge" type="text" name="validation_error_message_<?php echo $langCode; ?>" id="validation_error_message_<?php echo $langId; ?>" size="" maxlength="250" value="<?php echo isset($this->item->{'validation_error_message_'.$langCode}) ? $this->item->{'validation_error_message_'.$langCode} : ''; ?>" />											
										</td>
									</tr>
								</table>
							</div>							
							<?php
							$i++;
							}
						?>
					</div>
				<?php
				}
				else
				{
				?>
					<table class="admintable adminform" style="width: 100%;">
						<tr>
							<td class="key" width="40%">
								<span class="required">*</span>
								<?php echo  JText::_('ESHOP_TITLE'); ?>
							</td>
							<td>
								<input class="input-xlarge" type="text" name="title" id="title" size="" maxlength="250" value="<?php echo isset($this->item->title) ? $this->item->title : ''; ?>" />
							</td>								
						</tr>																												
						<tr>
							<td class="key">
								<?php echo JText::_('ESHOP_DESCRIPTION'); ?>
							</td>
							<td>
								<textarea rows="5" cols="30" name="description"><?php echo $this->item->description; ?></textarea>
							</td>								
						</tr>
						<tr>
							<td class="key">
								<span id="required-value"><?php echo JText::_('ESHOP_VALUES'); ?></span>
								<p class="small">
									<?php echo JText::_('ESHOP_VALUES_EXPLAIN'); ?>
								</p>	
							</td>
							<td>
								<textarea class="field-value" rows="5" cols="30" name="values"><?php echo $this->item->values; ?></textarea>
							</td>
						</tr>			
						<tr>
							<td class="key">
								<?php echo JText::_('ESHOP_DEFAULT_VALUES'); ?>
								<p class="small">
									<?php echo JText::_('ESHOP_DEFAULT_VALUES_EXPLAIN'); ?>	
								</p>
							</td>
							<td>
								<textarea rows="5" cols="30" name="default_values"><?php echo $this->item->default_values; ?></textarea>
							</td>
						</tr>
						<tr>
							<td class="key">
								<?php echo JText::_('ESHOP_VALIDATION_ERORR_MESSAGE'); ?>
							</td>
							<td>
								<input class="input-xlarge" type="text" name="validation_error_message" id="validation_error_message" size="" maxlength="250" value="<?php echo isset($this->item->validation_error_message) ? $this->item->validation_error_message : ''; ?>" />								
							</td>
						</tr>
					</table>
					<?php
				}
				?>
				</div>
			</div>			
			<div class="tab-pane" id="data-page">
				<div class="span8">
					<table class="admintable adminform" style="width: 100%;">
						<tr>
							<td class="key">
								<span class="required">*</span>
								<?php echo  JText::_('ESHOP_NAME'); ?>
							</td>
							<td>
								<input class="input-large" type="text" name="name" id="name" size="" maxlength="250" onchange="sanitizeFieldName();" value="<?php echo $this->item->name; ?>" <?php if ($this->item->is_core) echo 'disabled'; ?> />
							</td>							
						</tr>
						<tr>
							<td class="key">
								<?php echo  JText::_('ESHOP_FIELD_TYPE'); ?>
							</td>
							<td>
								<?php echo $this->lists['fieldtype']; ?>
							</td>							
						</tr>
						<tr>
							<td class="key">
								<?php echo  JText::_('ESHOP_ADDRESS_TYPE'); ?>
							</td>
							<td>
								<?php echo $this->lists['address_type']; ?>
							</td>							
						</tr>
						<tr>
							<td class="key">
								<?php echo  JText::_('ESHOP_REQUIRED'); ?>
							</td>
							<td>
								<?php echo $this->lists['required']; ?>
							</td>							
						</tr>
						<tr>
							<td class="key">
								<?php echo  JText::_('ESHOP_VALIDATION_RULE'); ?>
							</td>
							<td>
								<?php echo $this->lists['validation_rule']; ?>
							</td>							
						</tr>		
						<tr>
							<td class="key">
								<?php echo  JText::_('ESHOP_VALIDATION_STRING'); ?>
							</td>
							<td>
								<input class="input-xlarge" type="text" name="validation_rules_string" id="validation_rules_string" size="" maxlength="250" value="<?php echo $this->item->validation_rules_string; ?>" />
							</td>							
						</tr>	
						<tr class="eshop-field eshop-text eshop-checkboxes eshop-radio eshop-list">
							<td class="key">
								<?php echo  JText::_('ESHOP_SIZE'); ?>
							</td>
							<td>
								<input class="text_area" type="text" name="size" id="size" size="10" maxlength="250" value="<?php echo $this->item->size;?>" />
							</td>							
						</tr>				
						<tr class="eshop-field eshop-text eshop-textarea">
							<td class="key">
								<?php echo  JText::_('ESHOP_MAX_LENGTH'); ?>				
							</td>
							<td>
								<input class="text_area" type="text" name="max_length" id="max_lenth" size="50" maxlength="250" value="<?php echo $this->item->max_length;?>" />
							</td>							
						</tr>
						<tr	class="eshop-field eshop-textarea">
							<td class="key">
								<?php echo  JText::_('ESHOP_ROWS'); ?>
							</td>
							<td>
								<input class="text_area" type="text" name="rows" id="rows" size="10" maxlength="250" value="<?php echo $this->item->rows;?>" />
							</td>							
						</tr>
						<tr class="eshop-field eshop-textarea">
							<td class="key">
								<?php echo  JText::_('ESHOP_COLS'); ?>
							</td>
							<td>
								<input class="text_area" type="text" name="cols" id="cols" size="10" maxlength="250" value="<?php echo $this->item->cols;?>" />
							</td>
						</tr>		
						<tr>
							<td class="key">
								<?php echo  JText::_('ESHOP_EXTRA'); ?>
							</td>
							<td>
								<input class="text_area" type="text" name="extra_attributes" id="extra" size="40" maxlength="250" value="<?php echo $this->item->extra_attributes;?>" />
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