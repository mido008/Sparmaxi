<?php  defined ('_JEXEC') or die();
/**
 * @version 2.5.0
 * @package VirtueMart
 * @subpackage Plugins - vmpayment
 * @author 		    Valérie Isaksen (www.alatak.net)
 * @copyright       Copyright (C) 2012-2015 Alatak.net. All rights reserved
 * @license		    gpl-2.0.txt
 *
 */

if ($viewData['include_css'] ){
	JFactory::getDocument()->addStyleSheet(JURI::root(true) . '/plugins/vmpayment/alatak_creditcard/alatak_creditcard/assets/css/alatak_creditcard.css');
}


	$js='
jQuery(document).ready( function($) {
	 jQuery("#card_number_' . $viewData['virtuemart_paymentmethod_id'] . '").validateCreditCard(function (e) {
            return jQuery("#card_number_' . $viewData['virtuemart_paymentmethod_id'] . '").removeAttr("class").attr("class","").addClass("card_number"), null == e.card_type ? void jQuery(".vertical.maestro").slideUp({duration: 200}).animate({opacity: 0}, {
                queue: !1,
                duration: 200
            }) : (jQuery("#card_number_' . $viewData['virtuemart_paymentmethod_id'] . '").addClass(e.card_type.name), "maestro" === e.card_type.name ? jQuery(".vertical.maestro").slideDown({duration: 200}).animate({opacity: 1}, {queue: !1}) : jQuery(".vertical.maestro").slideUp({duration: 200}).animate({opacity: 0}, {
	queue: !1,
                duration: 200
            }), luhn_valid(e,"#card_number_' . $viewData['virtuemart_paymentmethod_id'] . '")
            )
        }, {accept: [' . $viewData['creditcards'] . ']});
        function luhn_valid(e, card_number) {
	        if (e.length_valid && e.luhn_valid) {
	        	 jQuery("#card_number_' . $viewData['virtuemart_paymentmethod_id'] . '").removeAttr("class").attr("class","");
	             jQuery("#card_number_' . $viewData['virtuemart_paymentmethod_id'] . '").addClass("card_number valid " + e.card_type.name);
	             jQuery("#card_type_' . $viewData['virtuemart_paymentmethod_id'] . '").val(e.card_type.name);
				jQuery("#payment_id_' . $viewData['virtuemart_paymentmethod_id'] . '").attr("checked", true);

	        } else {
	            jQuery(card_number).removeClass("valid").addClass("card_number "  + e.card_type.name);
				jQuery("#payment_id_' . $viewData['virtuemart_paymentmethod_id'] . '").attr("checked", false);

	        }
			if (e.card_type.name==="amex") {
					jQuery("#cvv_' . $viewData['virtuemart_paymentmethod_id'] . '").attr("placeholder", "1234");
					jQuery("#cvv_' . $viewData['virtuemart_paymentmethod_id'] . '").attr("maxlength", "4");
			} else {
					jQuery("#cvv_' . $viewData['virtuemart_paymentmethod_id'] . '").attr("placeholder", "123");
					jQuery("#cvv_' . $viewData['virtuemart_paymentmethod_id'] . '").attr("maxlength", "3");
			}
    }
});
'; // addScriptDeclaration
static $jsLoaded = false;

if (VM_VERSION < 3) {

	if (!$jsLoaded) {
		JFactory::getDocument()->addScript(JURI::root(true) . '/plugins/vmpayment/alatak_creditcard/alatak_creditcard/assets/js/jquery.creditCardValidator.js');
	}
		$js = "	//<![CDATA[" . $js . "//]]>";
		JFactory::getDocument()->addScriptDeclaration($js);
		$jsLoaded=true;

} else {
if (!$jsLoaded) {
	vmJsApi::addJScript('/plugins/vmpayment/alatak_creditcard/alatak_creditcard/assets/js/jquery.creditCardValidator.js');
}
	vmJsApi::addJScript('creditCardForm', $js);
}


?>
<div id="ccoffline_form">

	<ul >

		<li>
			<label for="card_number_<?php echo $viewData['virtuemart_paymentmethod_id']; ?>"><?php echo vmText::_('VMPAYMENT_ALATAK_CREDITCARD_CCNUM') ?></label>
			<input type="text" name="card_number_<?php echo $viewData['virtuemart_paymentmethod_id']; ?>" value="<?php echo $viewData['card_number']; ?>" id="card_number_<?php echo $viewData['virtuemart_paymentmethod_id']; ?>" placeholder="1234 5678 9012 3456" class="card_number">
			<input type="hidden" name="card_type_<?php echo $viewData['virtuemart_paymentmethod_id']; ?>" id="card_type_<?php echo $viewData['virtuemart_paymentmethod_id']; ?>" >
		</li>
		<li class="vertical">
			<ul>
				<li>
					<label for="expiry_date_<?php echo $viewData['virtuemart_paymentmethod_id']; ?>"><?php echo vmText::_('VMPAYMENT_ALATAK_CREDITCARD_EXDATE') ?></label>
					<input type="text" name="expiry_date_<?php echo $viewData['virtuemart_paymentmethod_id']; ?>" id="expiry_date_<?php echo $viewData['virtuemart_paymentmethod_id']; ?>" value="<?php echo $viewData['expiry_date']; ?>" maxlength="5" placeholder="mm/yy" class="expiry_date">
				</li>

				<li>
					<label for="cvv_<?php echo $viewData['virtuemart_paymentmethod_id']; ?>"><?php echo vmText::_('VMPAYMENT_ALATAK_CREDITCARD_CVV') ?></label>
					<input type="text" name="cvv_<?php echo $viewData['virtuemart_paymentmethod_id']; ?>" id="cvv_<?php echo $viewData['virtuemart_paymentmethod_id']; ?>" value="<?php echo $viewData['cvv']; ?>" maxlength="3" placeholder="123" class="cvv">
				</li>
			</ul>
		</li>

		<li class="vertical maestro" style="display: none; opacity: 0;">
			<ul>
				<li>
					<label for="issue_date_<?php echo $viewData['virtuemart_paymentmethod_id']; ?>"><?php echo vmText::_('VMPAYMENT_ALATAK_CREDITCARD_ISSUE_DATE') ?></label>
					<input type="text" name="issue_date_<?php echo $viewData['virtuemart_paymentmethod_id']; ?>" id="issue_date_<?php echo $viewData['virtuemart_paymentmethod_id']; ?>"  placeholder="mm/yy" value="<?php echo $viewData['issue_date']; ?>" maxlength="5" class="issue_date">
				</li>

				<li>
					<span class="or">or</span>
					<label for="issue_number_<?php echo $viewData['virtuemart_paymentmethod_id']; ?>"><?php echo vmText::_('VMPAYMENT_ALATAK_CREDITCARD_ISSUE_NUMBER') ?></label>
					<input type="text" name="issue_number_<?php echo $viewData['virtuemart_paymentmethod_id']; ?>" id="issue_number_<?php echo $viewData['virtuemart_paymentmethod_id']; ?>" placeholder="12" maxlength="2" value="<?php echo $viewData['issue_number']; ?>" class="issue_number">
				</li>
			</ul>
		</li>

		<li>
			<label for="name_on_card_<?php echo $viewData['virtuemart_paymentmethod_id']; ?>"><?php echo vmText::_('VMPAYMENT_ALATAK_CREDITCARD_CCNAME') ?></label>
			<input type="text" name="name_on_card_<?php echo $viewData['virtuemart_paymentmethod_id']; ?>" id="name_on_card_<?php echo $viewData['virtuemart_paymentmethod_id']; ?>" value="<?php echo $viewData['name_on_card']; ?>" placeholder="<?php echo vmText::_('VMPAYMENT_ALATAK_CREDITCARD_CCNAME_PLACEHOLDER') ?>">
		</li>
	</ul>

</div>