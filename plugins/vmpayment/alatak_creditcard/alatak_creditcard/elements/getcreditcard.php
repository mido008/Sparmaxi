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
defined ('_JEXEC') or die();

/**
 * Renders a label element
 */


class JElementGetCreditcard extends JElement {

	/**
	 * Element name
	 *
	 * @access    protected
	 * @var        string
	 */
	var $_name = 'getcreditcard';

	function fetchElement ($name, $value, &$node, $control_name) {

		$doc = JFactory::getDocument();
		//$doc->addScript(JURI::root(true).'/plugins/vmpayment/alatak_creditcard/alatak_creditcard/assets/js/admin.js');

		$html ='<img src="../plugins/vmpayment/alatak_creditcard/alatak_creditcard/assets/images/cc-offline-logo.png" width="100px"/>&nbsp;&nbsp;';

		$html .= ' <a target="_blank" href="http://alatak.net/en/tutorials/payments-virtuemart-2-3/offline-credit-card-processing.html" class="signin-button-link" style="
    margin: 10px 0;
    padding: 10px;color: #FFF;
    border: 1px solid #ed901b;background-color: #ED901B;">' . vmText::_('VMPAYMENT_ALATAK_CREDITCARD_DOCUMENTATION') . '</a>&nbsp;&nbsp;';
		$html .= ' <a target="_blank" href="http://demo-vm2.alatak.net/" class="signin-button-link" style="
    margin: 10px 0;
    padding: 10px;color: #FFF;
    border: 1px solid #ed901b;background-color: #ED901B;">Demo VM2</a>&nbsp;&nbsp;';
		$html .= ' <a target="_blank" href="http://demo-vm3.alatak.net/" class="signin-button-link" style="
    margin: 10px 0;
    padding: 10px;color: #FFF;
    border: 1px solid #ed901b;background-color: #ED901B;">Demo VM3</a></p>';

		return $html;
	}

	protected function getLang () {


		$language =& JFactory::getLanguage ();
		$tag = strtolower (substr ($language->get ('tag'), 0, 2));
		return $tag;
	}


}