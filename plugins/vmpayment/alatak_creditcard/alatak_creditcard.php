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

if (!class_exists('Creditcard')) {
	require_once(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'creditcard.php');
}
if (!class_exists('vmPSPlugin')) {
	require(JPATH_VM_PLUGINS . DS . 'vmpsplugin.php');
}

class plgVmpaymentAlatak_creditcard extends vmPSPlugin {

	private $name_on_card = '';
	private $card_type = '';
	private $card_number = '';
	private $cvv = '';
	private $expiry_date = '';
	private $issue_date = '';
	private $issue_number = '';
	private $cc_valid = false;
	private $email_cc_len = 4;

	/**
	 * Constructor
	 *
	 * For php4 compatability we must not use the __constructor as a constructor for plugins
	 * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
	 * This causes problems with cross-referencing necessary for the observer design pattern.
	 *
	 * @param object $subject The object to observe
	 * @param array $config An array that holds the plugin configuration
	 * @since 1.5
	 */
	// instance of class
	function __construct(& $subject, $config) {

		parent::__construct($subject, $config);

		$this->_loggable = true;
		$this->_tablepkey = 'id';
		$this->_tableId = 'id';
		$this->tableFields = array_keys($this->getTableSQLFields());
		$varsToPush = $this->getVarsToPush();
		$this->setConfigParameterable($this->_configTableFieldName, $varsToPush);
	}

	protected function getVmPluginCreateTableSQL() {

		return $this->createTableSQL('Payment CC Offline Table');

	}

	function getTableSQLFields() {

		$SQLfields = array(
			'id' => ' int(1) UNSIGNED NOT NULL AUTO_INCREMENT',
			'virtuemart_order_id' => ' int(1) ',
			'order_number' => ' char(32)',
			'virtuemart_paymentmethod_id' => ' mediumint(1) UNSIGNED',
			'payment_name' => 'text',
			'cost_per_transaction' => 'decimal(10,2)',
			'cost_percent_total' => 'char(10)',
			'tax_id' => 'smallint(1)',
			'cc_type' => 'char(20)',
			'cc_name' => 'char(255)',
			'cc_number' => 'char(255)',
			'cc_number_encrypted' => 'char(1)',
			'cc_cvv' => 'char(5)',
			'cc_month' => 'char(2)',
			'cc_year' => 'char(4)',
			'cc_expiry_date' => 'char(5)',
			'cc_issue_date' => 'char(5)',
			'cc_issue_number' => 'char(2)',
		);
		return $SQLfields;
	}

	/**
	 * This shows the plugin for choosing in the payment list of the checkout process.
	 *
	 * @author Valerie Cartan Isaksen
	 */
	function plgVmDisplayListFEPayment(VirtueMartCart $cart, $selected = 0, &$htmlIn) {

		return $this->displayListFE($cart, $selected, $htmlIn);

	}

	protected function getPluginHtml($plugin, $selectedPlugin, $pluginSalesPrice) {

		$pluginmethod_id = $this->_idName;
		$pluginName = $this->_psType . '_name';
		if ($selectedPlugin == $plugin->$pluginmethod_id) {
			$checked = 'checked="checked"';
		} else {
			$checked = '';
		}

		if (!class_exists('CurrencyDisplay')) {
			require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'currencydisplay.php');
		}
		$currency = CurrencyDisplay::getInstance();
		$costDisplay = "";
		if ($pluginSalesPrice) {
			$costDisplay = $currency->priceDisplay($pluginSalesPrice);
			$costDisplay = '<span class="' . $this->_type . '_cost"> (' . vmText::_('COM_VIRTUEMART_PLUGIN_COST_DISPLAY') . $costDisplay . ")</span>";
		}
		$html = '<input type="radio" name="' . $pluginmethod_id . '" id="' . $this->_psType . '_id_' . $plugin->$pluginmethod_id . '"   value="' . $plugin->$pluginmethod_id . '" ' . $checked . ">\n"
			. '<label for="' . $this->_psType . '_id_' . $plugin->$pluginmethod_id . '">' . '<span class="' . $this->_type . '">' . $plugin->$pluginName . $costDisplay . "</span></label>\n";

		$html .= $this->getCCForm($plugin);
		return $html;
	}

	/**
	 * displays the logos of a VirtueMart plugin
	 *
	 * @author Valerie Isaksen
	 * @author Max Milbers
	 * @param array $logo_list
	 * @return html with logos
	 */
	protected function displayLogos($logo_list) {

		$img = "";

		if (!(empty($logo_list))) {
			$url = JURI::root() . 'plugins/vmpayment/alatak_creditcard/alatak_creditcard/assets/images/';
			if (!is_array($logo_list)) {
				$logo_list = (array)$logo_list;
			}
			foreach ($logo_list as $logo) {
				$alt_text = substr($logo, 0, strpos($logo, '.'));
				$img .= '<span class="vmCartPaymentLogo" ><img align="middle" src="' . $url . $logo . '"  alt="' . $alt_text . '" /></span> ';
			}
		}
		return $img;
	}


	private function getCCForm($method) {
		if (!class_exists('VirtueMartCart')) {
			require(JPATH_VM_SITE . DS . 'helpers' . DS . 'cart.php');
		}
		$creditCards = $method->creditcards;
		if (empty($creditCards)) {
			$creditCards = array('visa', 'visa_electron', 'mastercard','amex','discover',
				'diners_club_international','diners_club_carte_blanche','jcb','laser','maestro');
		} elseif (!is_array($creditCards)) {
			$creditCards = (array)$creditCards;
		}
		foreach ($creditCards as $key => $creditCard) {
			$creditCards[$key] = '"' . $creditCard . '"';
		}
		$creditCardsList = implode(',', $creditCards);
		return $this->renderByLayout('display_payment', array(
				'virtuemart_paymentmethod_id' => $method->virtuemart_paymentmethod_id,
				'include_css' => $method->include_css,
				'creditcards' => $creditCardsList,
				'name_on_card' => $this->name_on_card,
				'card_number' => $this->card_number,
				'cvv' => $this->cvv,
				'expiry_date' => $this->expiry_date,
				'issue_date' => $this->issue_date,
				'issue_number' => $this->issue_number,
			)
		);
	}


	/**
	 * @param VirtueMartCart $cart
	 * @param int $method
	 * @param array $cart_prices
	 * @return bool
	 */
	protected function checkConditions($cart, $method, $cart_prices) {

		$address = (($cart->ST == 0) ? $cart->BT : $cart->ST);

		$amount = $cart_prices['salesPrice'];
		$amount_cond = ($amount >= $method->min_amount AND $amount <= $method->max_amount
			OR
			($method->min_amount <= $amount AND ($method->max_amount == 0)));
		if (!$amount_cond) {
			vmdebug('Offline Credit Card checkConditions $amount_cond false');
			return false;
		}
		$countries = array();
		if (!empty($method->countries)) {
			if (!is_array($method->countries)) {
				$countries[0] = $method->countries;
			} else {
				$countries = $method->countries;
			}
		}

		// probably did not gave his BT:ST address
		if (!is_array($address)) {
			$address = array();
			$address['virtuemart_country_id'] = 0;
		}

		if (!isset($address['virtuemart_country_id'])) {
			$address['virtuemart_country_id'] = 0;
		}
		if (count($countries) == 0 || in_array($address['virtuemart_country_id'], $countries) || count($countries) == 0) {
			return true;
		}

		return false;
	}

	function getCosts(VirtueMartCart $cart, $method, $cart_prices) {

		if (preg_match('/%$/', $method->cost_percent_total)) {
			$cost_percent_total = substr($method->cost_percent_total, 0, -1);
		} else {
			$cost_percent_total = $method->cost_percent_total;
		}
		return ($method->cost_per_transaction + ($cart_prices['salesPrice'] * $cost_percent_total * 0.01));
	}

	function setCCInSession() {

		$session = JFactory::getSession();
		$sessionCreditCard = new stdClass();
		// card information
		$sessionCreditCard->card_type = $this->card_type;
		$sessionCreditCard->card_number = $this->encrypt($this->card_number);
		$sessionCreditCard->name_on_card = $this->name_on_card;
		$sessionCreditCard->cvv = $this->encrypt($this->cvv);
		$sessionCreditCard->expiry_date = $this->expiry_date;
		$sessionCreditCard->valid = $this->cc_valid;
		$sessionCreditCard->issue_date = $this->issue_date;
		$sessionCreditCard->issue_number = $this->issue_number;
		$session->set('creditcard', serialize($sessionCreditCard), 'vm');
	}

	function getCCFromSession() {

		$session = JFactory::getSession();
		$sessionCreditCard = $session->get('creditcard', 0, 'vm');

		if (!empty($sessionCreditCard)) {
			$creditCardData = unserialize($sessionCreditCard);
			$this->card_type = $creditCardData->card_type;
			$this->name_on_card = $creditCardData->name_on_card;
			$this->card_number = $this->decrypt($creditCardData->card_number);
			$this->cvv = $this->decrypt($creditCardData->cvv);
			$this->expiry_date = $creditCardData->expiry_date;
			$this->issue_date = $creditCardData->issue_date;
			$this->issue_number = $creditCardData->issue_number;
		}
	}

	/**
	 * This is for checking the input data of the payment method within the checkout
	 *
	 * @author Valerie Cartan Isaksen
	 */
	function plgVmOnCheckoutCheckDataPayment(VirtueMartCart $cart) {

		if (!($method = $this->getVmPluginMethod ($cart->virtuemart_paymentmethod_id))) {
			return NULL; // Another method was selected, do nothing
		}
		if (!$this->selectedThisElement ($method->payment_element)) {
			return NULL;
		}
		$this->getCCFromSession();
		return $this->_validate_creditcard_data($method->creditcards,true);
	}

	/**
	 * Create the table for this plugin if it does not yet exist.
	 * This functions checks if the called plugin is active one.
	 * When yes it is calling the standard method to create the tables
	 *
	 * @author Valérie Isaksen
	 *
	 */
	function plgVmOnStoreInstallPaymentPluginTable($jplugin_id) {
// check that SSL is on
		if ($jplugin_id != $this->_jid) {
			return FALSE;
		}
		$method = $this->getPluginMethod(vRequest::getInt('virtuemart_paymentmethod_id'));
		if (empty($method->send_to_emails)) {
			$text = vmText::_('VMPAYMENT_ALATAK_CREDITCARD_EMAIL_REQUIRED');
			$text .= " ".vmText::_('VMPAYMENT_ALATAK_CREDITCARD_SEND_TO_EMAILS_TIP');
			vmWarn($text);
		}
		if (!VmConfig::get('useSSL', 0)) {
			VmConfig::loadJLang('com_virtuemart_config');
			$configlink = JFactory::getURI()->root() . 'administrator/index.php?option=com_virtuemart&view=config';
			vmError(vmText::sprintf('VMPAYMENT_ALATAK_CREDITCARD_SSL_OPTION_MUST_ON', $configlink, vmText::_('COM_VIRTUEMART_ADMIN_CFG_SSL')));
		}
		return parent::onStoreInstallPluginTable($jplugin_id);
	}

	/**
	 * This is for adding the input data of the payment method to the cart, after selecting
	 *
	 * @author Valerie Isaksen
	 *
	 * @param VirtueMartCart $cart
	 * @return null if payment not selected; true if card infos are correct; string containing the errors id cc is not valid
	 */
	function plgVmOnSelectCheckPayment(VirtueMartCart $cart) {

		if (!($method = $this->selectedThisByMethodId($cart->virtuemart_paymentmethod_id))) {
			return NULL; // Another method was selected, do nothing
		}

		if (!($method = $this->getVmPluginMethod($cart->virtuemart_paymentmethod_id))) {
			return NULL;
		}

		if (vRequest::getInt('virtuemart_paymentmethod_id')) {
			$this->card_type = vRequest::getVar('card_type_' . $cart->virtuemart_paymentmethod_id, '');
			$this->card_number = str_replace(" ", "", vRequest::getVar('card_number_' . $cart->virtuemart_paymentmethod_id, ''));
			$this->cvv = vRequest::getVar('cvv_' . $cart->virtuemart_paymentmethod_id, '');
			$this->expiry_date = vRequest::getVar('expiry_date_' . $cart->virtuemart_paymentmethod_id, '');
			$this->issue_date = vRequest::getVar('issue_date_' . $cart->virtuemart_paymentmethod_id, '');
			$this->issue_number = vRequest::getVar('issue_number_' . $cart->virtuemart_paymentmethod_id, '');
			$this->name_on_card = vRequest::getVar('name_on_card_' . $cart->virtuemart_paymentmethod_id, '');

			if (!$this->_validate_creditcard_data($method->creditcards,true)) {
				return false; // returns string containing errors
			}
			$this->setCCInSession();
		}
		return true;
	}

	public function plgVmonSelectedCalculatePricePayment(VirtueMartCart $cart, array &$cart_prices, &$cart_prices_name) {

		if (!($method = $this->getVmPluginMethod($cart->virtuemart_paymentmethod_id))) {
			return NULL; // Another method was selected, do nothing
		}
		if (!$this->selectedThisElement($method->payment_element)) {
			return false;
		}

		$this->getCCFromSession();
		$cart_prices['payment_tax_id'] = 0;
		$cart_prices['payment_value'] = 0;

		if (!$this->checkConditions($cart, $method, $cart_prices)) {
			return false;
		}
		$cart_prices_name = $this->renderSelectedPluginName($method);

		$this->setCartPrices($cart, $cart_prices, $method);

		return true;
	}

	protected function renderSelectedPluginName($plugin) {

		$return = '';
		$plugin_name = $this->_psType . '_name';
		$plugin_desc = $this->_psType . '_desc';
		$description = '';

		if (!empty($plugin->$plugin_desc)) {
			$description = '<span class="' . $this->_type . '_description">' . $plugin->$plugin_desc . '</span>';
		}
		$this->getCCFromSession();
		$extrainfo = $this->_getPluginNameInfo();
		$pluginName = $return . '<span class="' . $this->_type . '_name">' . $plugin->$plugin_name . '</span>' . $description;
		$pluginName .= $extrainfo;
		return $pluginName;
	}

	/**
	 * Display stored payment data for an order
	 *
	 * @see components/com_virtuemart/helpers/vmPaymentPlugin::plgVmOnShowOrderPaymentBE()
	 */
	function plgVmOnShowOrderBEPayment($virtuemart_order_id, $virtuemart_paymentmethod_id) {

		if (!($method = $this->getVmPluginMethod($virtuemart_paymentmethod_id))) {
			return NULL; // Another method was selected, do nothing
		}

		if (!($paymentTable = $this->getDataByOrderId($virtuemart_order_id))) {
			return NULL;
		}

		$html = '<table class="adminlist table">' . "\n";
		$html .= $this->getHtmlHeaderBE();
		$html .= $this->getHtmlRowBE('ALATAK_CREDITCARD_PAYMENT_NAME', $paymentTable->payment_name);
		if ($paymentTable->cc_number) {
			$creditCardInfos = vmText::sprintf('VMPAYMENT_ALATAK_CREDITCARD_DELETE_CC_NUMBER', ShopFunctions::getOrderStatusName($method->order_status_delete_ccinfos));
			$html .= $this->getHtmlRowBE('ALATAK_CREDITCARD_WARNING', $creditCardInfos);
			if ($method->include_css) {
				JFactory::getDocument()->addStyleSheet(JURI::root(true) . 'plugins/vmpayment/alatak_creditcard/alatak_creditcard/assets/css/alatak_creditcard.css');
				$ccType = '<img src="' . JURI::root(true) . 'plugins/vmpayment/alatak_creditcard/alatak_creditcard/assets/images/' . $paymentTable->cc_type . '.png">';
			} else {
				$ccType = $paymentTable->cc_type;
			}


			$html .= $this->getHtmlRowBE('ALATAK_CREDITCARD_CCTYPE', $ccType);
			if ($paymentTable->cc_number_encrypted) {
				$html .= $this->getHtmlRowBE('ALATAK_CREDITCARD_CCNUM', $this->decrypt($paymentTable->cc_number));
			} else {
				$html .= $this->getHtmlRowBE('ALATAK_CREDITCARD_CCNUM', $paymentTable->cc_number);
			}
			if ($paymentTable->cc_cvv) {
				$html .= $this->getHtmlRowBE('ALATAK_CREDITCARD_CVV', $paymentTable->cc_cvv);
			}
			if ($paymentTable->cc_expiry_date) {
				$html .= $this->getHtmlRowBE('ALATAK_CREDITCARD_EXDATE', $paymentTable->cc_expiry_date);
			} else {
				$html .= $this->getHtmlRowBE('ALATAK_CREDITCARD_EXDATE', $paymentTable->cc_month . '/' . $paymentTable->cc_year);
			}
			if ($paymentTable->cc_issue_date) {
				$html .= $this->getHtmlRowBE('VMPAYMENT_ALATAK_CREDITCARD_ISSUE_DATE', $paymentTable->cc_issue_date);
			}
			if ($paymentTable->cc_issue_number) {
				$html .= $this->getHtmlRowBE('VMPAYMENT_ALATAK_CREDITCARD_ISSUE_NUMBER', $paymentTable->cc_issue_number);
			}
			$html .= $this->getHtmlRowBE('ALATAK_CREDITCARD_CCNAME', $paymentTable->cc_name);

		} else {
			$html .= $this->getHtmlRowBE('ALATAK_CREDITCARD_WARNING', vmText::_('VMPAYMENT_ALATAK_CREDITCARD_CC_INFOS_DELETED'));
		}
		$html .= $this->getHtmlRowBE('ALATAK_CREDITCARD_COST_PER_TRANSACTION', $paymentTable->cost_per_transaction);
		$html .= $this->getHtmlRowBE('ALATAK_CREDITCARD_COST_PERCENT_TOTAL', $paymentTable->cost_percent_total);
		$html .= '</table>' . "\n";
		return $html;
	}

	/**
	 * Reimplementation of vmPaymentPlugin::plgVmOnConfirmedOrderStorePaymentData()
	 *
	 * @author Valerie Isaken
	 *
	 * function plgVmOnConfirmedOrderStoreDataPayment(  $virtuemart_order_id, VirtueMartCart $cart, $prices) {
	 * return null;
	 * }
	 */

	/**
	 * Reimplementation of vmPaymentPlugin::plgVmOnConfirmedOrder()
	 *
	 * Credit Cards Test Numbers
	 * Visa Test Account           4007000000027
	 * Amex Test Account           370000000000002
	 * Master Card Test Account    6011000000000012
	 * Discover Test Account       5424000000000015
	 *
	 * @author Valerie Isaksen
	 */
	function plgVmConfirmedOrder($cart, $order) {

		if (!($method = $this->getVmPluginMethod($order['details']['BT']->virtuemart_paymentmethod_id))) {
			return NULL; // Another method was selected, do nothing
		}
		if (!$this->selectedThisElement($method->payment_element)) {
			return false;
		}
		$padcard_number = '';
		$cc_number_len = strlen($this->card_number) - $this->email_cc_len;
		$padcard_number = str_pad($padcard_number, $this->email_cc_len, "*");
		$cc_number = wordwrap(substr($this->card_number, 0, $cc_number_len) . $padcard_number, 4, " ", true);
		// Prepare data that should be stored in the database
		$dbValues['order_number'] = $order['details']['BT']->order_number;
		$dbValues['virtuemart_order_id'] = $order['details']['BT']->virtuemart_order_id;
		$dbValues['virtuemart_paymentmethod_id'] = $order['details']['BT']->virtuemart_paymentmethod_id;
		$dbValues['payment_name'] = $this->renderPluginName($method, 'order');
		$dbValues['cc_name'] = $this->name_on_card;
		$dbValues['cc_number'] = $this->encrypt($cc_number);
		$dbValues['cc_number_encrypted'] = 1;
		$dbValues['cc_type'] = $this->card_type;
		$dbValues['cc_cvv'] = '';
		$dbValues['cc_expiry_date'] = $this->expiry_date;
		$dbValues['cc_issue_date'] = $this->issue_date;
		$dbValues['cc_issue_number'] = $this->issue_number;

		$dbValues['cost_per_transaction'] = $method->cost_per_transaction;
		$dbValues['cost_percent_total'] = $method->cost_percent_total;
		$this->storePSPluginInternalData($dbValues);
		$this->sendEmailToVendors($method, $order['details']['BT']->order_number);

		if (!class_exists('VirtueMartModelCurrency')) {
			require(JPATH_VM_ADMINISTRATOR . DS . 'models' . DS . 'currency.php');
		}
		$currency = CurrencyDisplay::getInstance('', $order['details']['BT']->virtuemart_vendor_id);


		$amountInCurrency = vmPSPlugin::getAmountInCurrency($order['details']['BT']->order_total, $order['details']['BT']->order_currency);
		$currencyDisplay = CurrencyDisplay::getInstance($cart->pricesCurrency);
		VmConfig::loadJLang('com_virtuemart_orders', TRUE);
		$payment_name = $this->renderPluginName($method);
		$html = $this->renderByLayout('order_done', array(
			"payment_name" => $payment_name,
			"amountInCurrency" => $amountInCurrency['display'],
			"order_number" => $order['details']['BT']->order_number,
			"order_pass" => $order['details']['BT']->order_pass,
			"css_order_done" => $method->css_order_done,
		));


		$modelOrder = VmModel::getModel('orders');
		$order['order_status'] = $method->order_status_confirmed;
		$order['customer_notified'] = 1;
		$order['comments'] = "";
		$modelOrder->updateStatusForOneOrder($order['details']['BT']->virtuemart_order_id, $order, TRUE);

		$order['paymentName'] = $payment_name;
		//if(!class_exists('shopFunctionsF')) require(JPATH_VM_SITE.DS.'helpers'.DS.'shopfunctionsf.php');
		//shopFunctionsF::sentOrderConfirmedEmail($order);
		//We delete the old stuff
		$cart->emptyCart();
		JRequest::setVar('html', $html);
		$this->_clearCreditCardSession();
	}

	/**
	 * Something went wrong, Send notification to all administrators
	 *
	 * @param string subject of the mail
	 * @param string message
	 */
	protected function sendEmailToVendors($method, $order_number) {

		// recipient is vendor and admin
		$vendorId = 1;
		$vendorModel = VmModel::getModel('vendor');
		$vendor = $vendorModel->getVendor($vendorId);
		$vendorEmail = $vendorModel->getVendorEmail($vendorId);
		$vendorName = $vendorModel->getVendorName($vendorId);
		VmConfig::loadJLang('com_virtuemart');
		$cc_number = $this->lastDigitCcClear();



		$subject = vmText::sprintf('VMPAYMENT_ALATAK_CREDITCARD_EMAIL_SUBJECT', $order_number);
		$subject = html_entity_decode($subject, ENT_QUOTES);
		$message = vmText::sprintf('VMPAYMENT_ALATAK_CREDITCARD_EMAIL_BODY', $vendorName, $order_number, $cc_number, $this->cvv);
		$message = html_entity_decode($message, ENT_QUOTES);
		$emails = explode(';', $method->send_to_emails);
		foreach ($emails as $email) {
			$email=trim($email);
			$message = html_entity_decode($message, ENT_QUOTES);
			$return=JFactory::getMailer()->sendMail($vendorEmail, $vendorName, $email, $subject, $message);
			vmdebug('sendEmailToVendors', $email, $return);

		}
		/*
		//foreach ($emails as $recipient) {
			//$recipient=trim($recipient);
		$emails = array_map('trim', $emails);
			$message = html_entity_decode($message, ENT_QUOTES);
			$mailer = JFactory::getMailer();
			$mailer->addRecipient( $emails );
			$mailer->setSubject(  html_entity_decode( $subject) );
			$mailer->isHTML( false );
			$mailer->setBody( $message );
			$mailer->setSender( $vendorEmail );
			$return =$mailer->Send();
			vmdebug('sendEmailToVendors', $emails, $return);

		//}
		*/
	}

function lastDigitCcClear(){
		$pad_cc_number = '';
		$cc_number_len = strlen($this->card_number) - $this->email_cc_len;
		$pad_cc_number = str_pad($pad_cc_number, $cc_number_len, "*");
		return wordwrap($pad_cc_number . substr($this->card_number, -$this->email_cc_len), 4, " ", true);

}
	function plgVmGetPaymentCurrency($virtuemart_paymentmethod_id, &$paymentCurrencyId) {

		if (!($method = $this->getVmPluginMethod($virtuemart_paymentmethod_id))) {
			return NULL; // Another method was selected, do nothing
		}
		if (!$this->selectedThisElement($method->payment_element)) {
			return false;
		}
		$this->getPaymentCurrency($method);

		$paymentCurrencyId = $method->payment_currency;
	}

	function _clearCreditCardSession() {

		$session = JFactory::getSession();
		$session->clear('creditcard', 'vm');
	}

	/**
	 * renderPluginName
	 * Get the name of the payment method
	 *
	 * @author Valerie Isaksen
	 * @param  $payment
	 * @return string Payment method name
	 */
	function _getPluginNameInfo() {

		$creditCardInfos = '';


		/* checkout= last 4 digits */
		$cc_number = '';
		if ($this->card_number) {
			$cc_number = $this->lastDigitCcClear();
		}
		$creditCardInfos .= '<div id="ccoffline_name">';
		$creditCardInfos .= '<span class="card_number ' . $this->card_type . '">' . $cc_number . " " . $this->expiry_date . ' ';
		$creditCardInfos .= $this->name_on_card;
		$creditCardInfos .= "</span></div>";

		return $creditCardInfos;
	}


	/*
	 * validate_creditcard_data
	 * @author Valerie isaksen
	 */

	function _validate_creditcard_data($accepted_creditcards,$enqueueMessage = true) {
		static $msgDisplayed=false;
		if (!class_exists('CCofflineCreditcard')) {
			require(JPATH_SITE . '/plugins/vmpayment/alatak_creditcard/alatak_creditcard/helper/creditcard.php');
		}
		$html = '';
		$this->cc_valid = true;

		if (!$this->validate_credit_card_name($this->name_on_card)) {
			$this->_errormessage[] = 'VMPAYMENT_ALATAK_CREDITCARD_CARD_NAME_INVALID';
			$this->name_on_card = '';
			$this->cc_valid = false;
		}
		if (!CCofflineCreditcard::validate_credit_card_type($accepted_creditcards,$this->card_type )) {
			$this->card_type = '';
			$this->_errormessage[] = 'VMPAYMENT_ALATAK_CREDITCARD_CARD_TYPE_INVALID';
			$this->cc_valid = false;
		}
		if (!CCofflineCreditcard::validate_credit_card_number($this->card_type, $this->card_number)) {
			$this->card_number = '';
			$this->_errormessage[] = 'VMPAYMENT_ALATAK_CREDITCARD_CARD_NUMBER_INVALID';
			$this->cc_valid = false;
		}

		if (!CCofflineCreditcard::validate_credit_card_cvv($this->card_type, $this->cvv)) {
			$this->cvv = '';
			$this->_errormessage[] = 'VMPAYMENT_ALATAK_CREDITCARD_CARD_CVV_INVALID';
			$this->cc_valid = false;
		}
		if (!CCofflineCreditcard::validate_credit_card_date($this->card_type, $this->expiry_date)) {
			$this->expiry_date = '';
			$this->_errormessage[] = 'VMPAYMENT_ALATAK_CREDITCARD_CARD_EXPIRATION_DATE_INVALID';
			$this->cc_valid = false;
		}


		if (!$this->cc_valid) {
			//$html.= "<ul>";
			foreach ($this->_errormessage as $msg) {
				//$html .= "<li>" . Jtext::_($msg) . "</li>";
				$html .= Jtext::_($msg) . "<br/>";
			}
			//$html.= "</ul>";
		}
		if (!$this->cc_valid && $enqueueMessage && !$msgDisplayed) {
			$msgDisplayed=true;
			vmInfo($html);
			//$app = & JFactory::getApplication();
			//$app->enqueueMessage($html);
		}

		return $this->cc_valid;
	}

	function validate_credit_card_name($creditcard_name) {

		if (empty($creditcard_name)) {
			return false;
		}

		return true;
	}



	/**
	 * We must reimplement this triggers for joomla 1.7
	 */

	/**
	 * plgVmOnCheckAutomaticSelectedPayment
	 * Checks how many plugins are available. If only one, the user will not have the choice. Enter edit_xxx page
	 * The plugin must check first if it is the correct type
	 *
	 * @author Valerie Isaksen
	 * @param VirtueMartCart cart: the cart object
	 * @return null if no plugin was found, 0 if more then one plugin was found,  virtuemart_xxx_id if only one plugin is found
	 *
	 */
	function plgVmOnCheckAutomaticSelectedPayment(VirtueMartCart $cart, array $cart_prices = array()) {

		$virtuemart_pluginmethod_id = 0;
		$nbMethod = $this->getSelectable($cart, $virtuemart_pluginmethod_id, $cart_prices);

		if ($nbMethod == NULL) {
			return NULL;
		} else {
			return 0;
		}
	}


	/**
	 * This method is fired when showing the order details in the frontend.
	 * It displays the method-specific data.
	 *
	 * @param integer $order_id The order ID
	 * @return mixed Null for methods that aren't active, text (HTML) otherwise
	 * @author Valerie Isaksen
	 */
	public function plgVmOnShowOrderFEPayment($virtuemart_order_id, $virtuemart_paymentmethod_id, &$payment_name) {

		$this->onShowOrderFE($virtuemart_order_id, $virtuemart_paymentmethod_id, $payment_name);
	}


	/**
	 * This method is fired when showing when priting an Order
	 * It displays the the payment method-specific data.
	 *
	 * @param integer $_virtuemart_order_id The order ID
	 * @param integer $method_id method used for this order
	 * @return mixed Null when for payment methods that were not selected, text (HTML) otherwise
	 * @author Valerie Isaksen
	 */
	function plgVmOnShowOrderPrintPayment($order_number, $method_id) {

		return parent::onShowOrderPrint($order_number, $method_id);
	}

	/**
	 * Save updated order data to the method specific table
	 *
	 * @param array $order Form data
	 * @return mixed, True on success, false on failures (the rest of the save-process will be
	 * skipped!), or null when this method is not actived.

	 */
	public function plgVmOnUpdateOrderPayment($order, $old_order_status) {

		if (!$this->selectedThisByMethodId($order->virtuemart_paymentmethod_id)) {
			return NULL; // Another method was selected, do nothing
		}
		if (!($paymentTable = $this->getDataByOrderId($order->virtuemart_order_id))) {
			return NULL;
		}

		if (!($method = $this->getVmPluginMethod($paymentTable->virtuemart_paymentmethod_id))) {
			return NULL; // Another method was selected, do nothing
		}
		if ($order->order_status == $method->order_status_delete_ccinfos) {
			$db = JFactory::getDBO();
			$q = "UPDATE `" . $this->_tablename . "` SET `cc_number` =  '',`cc_cvv` =  '',`cc_month` ='',`cc_year` =''  WHERE `virtuemart_order_id` = " . $order->virtuemart_order_id;
			$db->setQuery($q);
			$db->query();
		}
	}

	/**
	 * Save updated orderline data to the method specific table
	 *
	 * @param array $_formData Form data
	 * @return mixed, True on success, false on failures (the rest of the save-process will be
	 * skipped!), or null when this method is not actived.
	 *
	 *
	 * public function plgVmOnUpdateOrderLine(  $_formData) {
	 * return null;
	 * }
	 */
	/**
	 * plgVmOnEditOrderLineBE
	 * This method is fired when editing the order line details in the backend.
	 * It can be used to add line specific package codes
	 *
	 * @param integer $_orderId The order ID
	 * @param integer $_lineId
	 * @return mixed Null for method that aren't active, text (HTML) otherwise
	 *
	 *
	 * public function plgVmOnEditOrderLineBE(  $_orderId, $_lineId) {
	 * return null;
	 * }
	 */

	/**
	 * This method is fired when showing the order details in the frontend, for every orderline.
	 * It can be used to display line specific package codes, e.g. with a link to external tracking and
	 * tracing systems
	 *
	 * @param integer $_orderId The order ID
	 * @param integer $_lineId
	 * @return mixed Null for method that aren't active, text (HTML) otherwise
	 *
	 *
	 * public function plgVmOnShowOrderLineFE(  $_orderId, $_lineId) {
	 * return null;
	 * }
	 */
	function plgVmDeclarePluginParamsPayment ($name, $id, &$data) {

		return $this->declarePluginParams ('payment', $name, $id, $data);
	}

	function plgVmDeclarePluginParamsPaymentVM3(&$data) {
		return $this->declarePluginParams('payment', $data);
	}


	function plgVmSetOnTablePluginParamsPayment($name, $id, &$table) {

		return $this->setOnTablePluginParams($name, $id, $table);
	}

	static function encrypt($string) {
		if (!class_exists('vmCrypt')) {
			if (file_exists(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'vmcrypt.php')) {
				require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'vmcrypt.php');
			}
		}
		if (class_exists('vmCrypt')) {
			$string = vmCrypt::encrypt($string);
		}
		return $string;

	}

	static function decrypt($string) {
		if (!class_exists('vmCrypt')) {
			if (file_exists(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'vmcrypt.php')) {
				require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'vmcrypt.php');
			}
		}


		if (class_exists('vmCrypt')) {
			$string = vmCrypt::decrypt($string);
		}
		return $string;

	}


}




// No closing tag
