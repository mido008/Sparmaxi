<?php
/**
 *
 * Layout for the payment selection
 *
 * @package	VirtueMart
 * @subpackage Cart
 * @author Max Milbers
 *
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: select_payment.php 8847 2015-05-06 12:22:37Z Milbo $
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
$addClass="";


if (VmConfig::get('oncheckout_show_steps', 1)) {
    echo '<div class="checkoutStep" id="checkoutStep3">' . vmText::_('COM_VIRTUEMART_USER_FORM_CART_STEP3') . '</div>';
}

if ($this->layoutName!='default') {
	$headerLevel = 1;
	if($this->cart->getInCheckOut()){
		$buttonclass = 'button vm-button-correct';
	} else {
		$buttonclass = 'default';
	}
?>
	<form method="post" id="paymentForm" name="choosePaymentRate" action="<?php echo JRoute::_('index.php'); ?>" class="form-validate <?php echo $addClass ?>">
<?php } else {
		$headerLevel = 3;
		$buttonclass = 'vm-button-correct';
	}

	if($this->cart->virtuemart_paymentmethod_id){
		echo '<h'.$headerLevel.'>'.vmText::_('COM_VIRTUEMART_CART_SELECTED_PAYMENT_SELECT').'</h'.$headerLevel.'>';
	} else {
		echo '<h'.$headerLevel.'>'.vmText::_('COM_VIRTUEMART_CART_SELECT_PAYMENT').'</h'.$headerLevel.'>';
	} ?>

<div class="buttonBar-right">

<button name="updatecart" class="<?php echo $buttonclass ?>" type="submit"><?php echo vmText::_('COM_VIRTUEMART_SAVE'); ?></button>

   <?php   if ($this->layoutName!='default') { ?>
<button class="<?php echo $buttonclass ?>" type="reset" onClick="window.location.href='<?php echo JRoute::_('index.php?option=com_virtuemart&view=cart&task=cancel'); ?>'" ><?php echo vmText::_('COM_VIRTUEMART_CANCEL'); ?></button>
	<?php  } ?>
    </div>

<?php
     if ($this->found_payment_method ) {
         
         $p = "<script> jQuery(document).ready(function(){
                var pay = jQuery('".'input:radio[name="virtuemart_paymentmethod_id"]'."');
                var ccoffline = jQuery('#ccoffline_form');
                var vorkasse = jQuery('".'label[for="payment_id_7"]'."');
         ";
         
           $p .="var vorkasse_info = '<div id=vorkasse_info style=".'"'."display: inner-block;".'"'.">";
           $p .="<table>";
           $p .="<tr><td>Name</td><td>:</td><td>Sparmaxi</td></tr>";
           $p .="<tr><td>Bankberbindung</td><td>:</td><td>Hypovereinsbank</td></tr>";
           $p .="<tr><td>IBAN</td><td>:</td><td>DE56 72120078 0022748955</td></tr>";
           $p .="<tr><td>BIC</td><td>:</td><td>HYVEDEMM426</td></tr>";
           $p .="<tr><td>Verwendungszweck</td><td>:</td><td></td></tr>";
           $p .="</table></div>';";
           
           $p .="
        
                vorkasse.append(vorkasse_info);
                var billing_vorkasse_inf = jQuery('#vorkasse_info');
        
                if(pay.prevObject[0].activeElement.id != 'payment_id_5')
                    ccoffline.hide();
	            if(pay.prevObject[0].activeElement.id != 'payment_id_7')
                    billing_vorkasse_inf.hide();
         
                pay.click(function(){ 
                    if(pay.prevObject[0].activeElement.id == 'payment_id_5')
                    {
                        ccoffline.show();
                    }
                    else
                    {
                        ccoffline.hide();
                    }
         
                    if(pay.prevObject[0].activeElement.id == 'payment_id_7')
                    {
                         billing_vorkasse_inf.show();                         
                    }
	                else
                    {
                        billing_vorkasse_inf.hide();
                    }
                });
        });
</script>";
         echo $p;
         
    echo '<fieldset class="vm-payment-shipment-select vm-payment-select">';
		foreach ($this->paymentplugins_payments as $paymentplugin_payments) {
		    if (is_array($paymentplugin_payments)) {
			foreach ($paymentplugin_payments as $paymentplugin_payment) {
			    echo '<div class="vm-payment-plugin-single">'.$paymentplugin_payment.'</div>';
			}
		    }
		}
    echo '</fieldset>';

    } else {
	 echo '<h1>'.$this->payment_not_found_text.'</h1>';
    }

if ($this->layoutName!='default') {
?>    <input type="hidden" name="option" value="com_virtuemart" />
    <input type="hidden" name="view" value="cart" />
    <input type="hidden" name="task" value="updatecart" />
    <input type="hidden" name="controller" value="cart" />
</form>
<?php
}
?>