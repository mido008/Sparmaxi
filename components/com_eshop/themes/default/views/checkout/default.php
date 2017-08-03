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
<h1><?php echo JText::_('ESHOP_CHECKOUT'); ?></h1><br />
<div class="row-fluid">
	<div id="checkout-options">
		<div class="checkout-heading"><?php echo JText::_('ESHOP_CHECKOUT_STEP_1'); ?></div>
		<div class="checkout-content"></div>
	</div>
	<div id="payment-address">
		<div class="checkout-heading">
			<?php
			if (EshopHelper::getCheckoutType() == 'guest_only')
			{
				echo JText::_('ESHOP_CHECKOUT_STEP_2_GUEST');
			}
			else 
			{
				echo JText::_('ESHOP_CHECKOUT_STEP_2_REGISTER');
			}
			?>
		</div>
		<div class="checkout-content"></div>
	</div>
	<?php
	if ($this->shipping_required)
	{
		?>
		<div id="shipping-address">
			<div class="checkout-heading"><?php echo JText::_('ESHOP_CHECKOUT_STEP_3'); ?></div>
			<div class="checkout-content"></div>
		</div>
		<div id="shipping-method">
			<div class="checkout-heading"><?php echo JText::_('ESHOP_CHECKOUT_STEP_4'); ?></div>
			<div class="checkout-content"></div>
		</div>
		<?php
	}
	?>
	<div id="payment-method">
		<div class="checkout-heading"><?php echo JText::_('ESHOP_CHECKOUT_STEP_5'); ?></div>
		<div class="checkout-content"></div>
	</div>
	<div id="confirm">
		<div class="checkout-heading"><?php echo JText::_('ESHOP_CHECKOUT_STEP_6'); ?></div>
		<div class="checkout-content"></div>
	</div>
</div>
<script type="text/javascript">
	Eshop.jQuery(function($){
		//Script to allow Edit step
		$('.checkout-heading a').live('click', function() {
			$('.checkout-content').slideUp('slow');
			$(this).parent().parent().find('.checkout-content').slideDown('slow');
		});
		//If user is not logged in, then show login layout
		<?php
		if (!$this->user->get('id'))
		{
			if (EshopHelper::getConfigValue('checkout_type') == 'guest_only')
			{
				?>
				$(document).ready(function() {
					$.ajax({
						url: '<?php echo EshopHelper::getSiteUrl(); ?>index.php?option=com_eshop&view=checkout&layout=guest&format=raw<?php echo EshopHelper::getAttachedLangLink(); ?>',
						dataType: 'html',
						success: function(html) {
							$('#payment-address .checkout-content').html(html);
							$('#payment-address .checkout-content').slideDown('slow');
						},
						error: function(xhr, ajaxOptions, thrownError) {
							alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
						}
					});
				});
				<?php
			}
			else 
			{
				?>
				$(document).ready(function() {
					$.ajax({
						url: '<?php echo EshopHelper::getSiteUrl(); ?>index.php?option=com_eshop&view=checkout&layout=login&format=raw<?php echo EshopHelper::getAttachedLangLink(); ?>',
						dataType: 'html',
						success: function(html) {
							$('#checkout-options .checkout-content').html(html);
							$('#checkout-options .checkout-content').slideDown('slow');
						},
						error: function(xhr, ajaxOptions, thrownError) {
							alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
						}
					});
				});
				<?php
			}
		}
		//Else, show payment address layout
		else
		{
			?>
			$('#payment-address .checkout-heading').html('<?php echo JText::_('ESHOP_CHECKOUT_STEP_2_GUEST'); ?>');
			$(document).ready(function() {
				$.ajax({
					url: '<?php echo EshopHelper::getSiteUrl(); ?>index.php?option=com_eshop&view=checkout&layout=payment_address&format=raw<?php echo EshopHelper::getAttachedLangLink(); ?>',
					dataType: 'html',
					success: function(html) {
						$('#payment-address .checkout-content').html(html);
						$('#payment-address .checkout-content').slideDown('slow');
					},
					error: function(xhr, ajaxOptions, thrownError) {
						alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
					}
				});
			});
			<?php
		}
		?>
	});
</script>