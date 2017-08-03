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
if (isset($this->warning))
{
	?>
	<div class="warning"><?php echo $this->warning; ?></div>
	<?php
}
?>
<h1><?php echo JText::_('ESHOP_DOWNLOADS'); ?></h1><br />
<?php
if (!count($this->downloads))
{
	?>
	<div class="no-content"><?php echo JText::_('ESHOP_NO_DOWNLOADS'); ?></div>
	<?php
}
else
{
	?>
	<div class="row-fluid">
		<form id="adminForm" class="download-list">
			<?php
			foreach ($this->downloads as $download)
			{
				?>
				<div class="order-id"><b><?php echo JText::_('ESHOP_ORDER_ID'); ?>: </b> #<?php echo $download->order_id; ?></div>
				<div class="download-size"><b><?php echo JText::_('ESHOP_SIZE'); ?>: </b><?php echo $download->size;  ?></div>
				<div class="download-content">
					<div>
						<b><?php echo JText::_('ESHOP_NAME'); ?>: </b><?php echo $download->download_name; ?><br />
					</div>
					<div>
						<b><?php echo JText::_('ESHOP_REMAINING'); ?>: </b> <?php echo $download->remaining; ?>
					</div>
					<div class="download-info" align="right">
						<a href="<?php echo JRoute::_('index.php?option=com_eshop&task=customer.downloadFile&order_id='.intval($download->order_id).'&download_code='.$download->download_code); ?>" title="<?php echo JText::_('ESHOP_DOWNLOAD'); ?>">
							<img src="<?php echo JUri::root(true); ?>/components/com_eshop/themes/default/images/download.png" />
						</a>
					</div>
				</div>
				<?php
			}
			?>
		</form>
	</div>
	<?php
}
?>
<div class="row-fluid">
	<div class="span2">
		<input type="button" value="<?php echo JText::_('ESHOP_BACK'); ?>" id="button-back-download" class="btn btn-primary pull-left" />
	</div>
</div>
<script type="text/javascript">
	Eshop.jQuery(function($){
		$(document).ready(function(){
			$('#button-back-download').click(function() {
				var url = '<?php echo JRoute::_(EshopRoute::getViewRoute('customer')); ?>';
				$(location).attr('href', url);
			});
		})
	});
</script>