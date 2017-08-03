<?php
/**
 * @version 2.5.0
 * @package VirtueMart
 * @subpackage Plugins - vmpayment
 * @author 		    Valérie Isaksen (www.alatak.net)
 * @copyright       Copyright (C) 2012-2015 Alatak.net. All rights reserved
 * @license		    gpl-2.0.txt
 *
 */
defined('_JEXEC') or die();
?>

<div class="offline-creditcard">


		<div class="response">
			<?php echo vmText::sprintf('VMPAYMENT_ALATAK_CREDITCARD_ORDER_DONE',   $viewData["order_number"] , $viewData['amountInCurrency'] ); ?>
		</div>


		<div class="vieworder">
			<a class="<?php echo $viewData["css_order_done"] ?>" href="<?php echo JRoute::_('index.php?option=com_virtuemart&view=orders&layout=details&order_number=' . $viewData["order_number"] . '&order_pass=' . $viewData["order_pass"], false) ?>"><?php echo vmText::_('COM_VIRTUEMART_ORDER_VIEW_ORDER'); ?></a>
		</div>

</div>