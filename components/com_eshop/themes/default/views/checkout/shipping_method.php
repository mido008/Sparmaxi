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

if (isset($this->shipping_methods))
{
	?>
	<div>
		<p><?php echo JText::_('ESHOP_SHIPPING_METHOD_TITLE'); ?></p>
		<?php
		foreach ($this->shipping_methods as $shippingMethod)
		{
			?>
			<div>
				<strong><?php echo $shippingMethod['title']; ?></strong><br />
				<?php
				foreach ($shippingMethod['quote'] as $quote)
				{
					$checkedStr = ' ';
					if ($quote['name'] == $this->shipping_method || count($shippingMethod['quote']) == 1)
					{
						$checkedStr = ' checked = "checked" ';
					}
					?>
					<label class="radio">
						<input type="radio" value="<?php echo $quote['name']; ?>" name="shipping_method" <?php echo $checkedStr; ?>/>
						<?php echo $quote['title'] . ' (' . $quote['text'] . ')'; ?>
					</label>
					<?php
				}
				?>
			</div>
			<?php
		}
		?>
	</div>
	<?php
}
if (EshopHelper::getConfigValue('delivery_date'))
{
	?>
	<script language="JavaScript" type="text/javascript">
		Calendar.setup({
			// Id of the input field
			inputField: "delivery_date",
			// Format of the input field
			ifFormat: "%Y-%m-%d",
			// Trigger for the calendar (button ID)
			button: "delivery_date_img",
			// Alignment (defaults to "Bl")
			align: "Tl",
			singleClick: true,
			firstDay: 0
		});
	</script>
	<br />
	<div class="control-group">
		<label for="textarea" class="control-label"><?php echo JText::_('ESHOP_DELIVERY_DATE'); ?></label>
		<div class="controls">
			<?php echo JHtml::_('calendar', $this->delivery_date ? $this->delivery_date : '', 'delivery_date', 'delivery_date', '%Y-%m-%d'); ?>
		</div>
	</div>
	<?php
}
?>
<div class="control-group">
	<label for="textarea" class="control-label"><?php echo JText::_('ESHOP_COMMENT_ORDER'); ?></label>
	<div class="controls">
		<textarea rows="8" id="textarea" class="input-xlarge span12" name="comment"><?php echo $this->comment; ?></textarea>
	</div>
</div>
<div class="no_margin_left">
	<input type="button" class="btn btn-primary pull-right" id="button-shipping-method" value="<?php echo JText::_('ESHOP_CONTINUE'); ?>" />
</div>
<script type="text/javascript">
	//Shipping Method
	Eshop.jQuery(function($){
		$('#button-shipping-method').click(function(){
			$.ajax({
				url: '<?php echo EshopHelper::getSiteUrl(); ?>index.php?option=com_eshop&task=checkout.processShippingMethod<?php echo EshopHelper::getAttachedLangLink(); ?>',
				type: 'post',
				data: $('#shipping-method input[type=\'radio\']:checked, #shipping-method textarea, #shipping-method input[type=\'text\']'),
				dataType: 'json',
				beforeSend: function() {
					$('#button-shipping-method').attr('disabled', true);
					$('#button-shipping-method').after('<span class="wait">&nbsp;<img src="components/com_eshop/assets/images/loading.gif" alt="" /></span>');
				},	
				complete: function() {
					$('#button-shipping-method').attr('disabled', false);
					$('.wait').remove();
				},			
				success: function(json) {
					$('.warning, .error').remove();
					if (json['return']) {
						window.location.href = json['return'];
					} else if (json['error']) {
						if (json['error']['warning']) {
							$('#shipping-method .checkout-content').prepend('<div class="warning" style="display: none;">' + json['error']['warning'] + '</div>');
							$('.warning').fadeIn('slow');
						}
					} else if (json['total'] > 0) {
						$.ajax({
							url: '<?php echo EshopHelper::getSiteUrl(); ?>index.php?option=com_eshop&view=checkout&layout=payment_method&format=raw<?php echo EshopHelper::getAttachedLangLink(); ?>',
							dataType: 'html',
							success: function(html) {
								$('#payment-method .checkout-content').html(html);
								$('#shipping-method .checkout-content').slideUp('slow');
								$('#payment-method .checkout-content').slideDown('slow');
								$('#shipping-method .checkout-heading a').remove();
								$('#payment-method .checkout-heading a').remove();
								$('#shipping-method .checkout-heading').append('<a><?php echo JText::_('ESHOP_EDIT'); ?></a>');
							},
							error: function(xhr, ajaxOptions, thrownError) {
								alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
							}
						});
					} else {
						$.ajax({
							url: '<?php echo EshopHelper::getSiteUrl(); ?>index.php?option=com_eshop&view=checkout&layout=confirm&format=raw<?php echo EshopHelper::getAttachedLangLink(); ?>',
							dataType: 'html',
							success: function(html) {
								$('#confirm .checkout-content').html(html);
								$('#shipping-method .checkout-content').slideUp('slow');
								$('#confirm .checkout-content').slideDown('slow');
								$('#shipping-method .checkout-heading a').remove();
								$('#payment-method .checkout-heading a').remove();
								$('#shipping-method .checkout-heading').append('<a><?php echo JText::_('ESHOP_EDIT'); ?></a>');
							},
							error: function(xhr, ajaxOptions, thrownError) {
								alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
							}
						});
					}
				},
				error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		});
	})
</script>