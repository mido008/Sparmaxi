<?php // no direct access
defined('_JEXEC') or die('Restricted access');
vmJsApi::jQuery();
vmJsApi::chosenDropDowns();
?>

<!-- Currency Selector Module -->
<?php echo $text_before ?>
<div class="mod-currency">
	<form class="demo" action="<?php echo vmURI::getCleanUrl() ?>" method="post">
	
		<div class="vm-chzn">
	
		<?php echo JHTML::_('select.genericlist', $currencies, 'virtuemart_currency_id', 'class="inputbox selectpicker" onchange="this.form.submit()"', 'virtuemart_currency_id', 'currency_txt', $virtuemart_currency_id) ; ?>
		</div>
	
	</form>
</div>
<script type="text/javascript">
jQuery(document).ready(function($) {
	// Selectpicker
	$('.selectpicker').selectpicker();

	$(".bootstrap-select").bind("hover touchstart", function() {
		$(this).children(".dropdown-menu").stop().slideToggle(350);

	}, function(){
		$(this).children(".dropdown-menu").stop().toggle();
	});
});
</script>