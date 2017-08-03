<?php
// Check to ensure this file is within the rest of the framework
defined ('_JEXEC') or die();
/**
 * @version 2.5.0
 * @package VirtueMart
 * @subpackage Plugins - vmpayment
 * @author 		    Valérie Isaksen (www.alatak.net)
 * @copyright       Copyright (C) 2012-2015 Alatak.net. All rights reserved
 * @license		    gpl-2.0.txt
 *
 */
/*
 * This class is used by VirtueMart Payment  Plugins
 * which uses JParameter
 * So It should be an extension of JElement
 * Those plugins cannot be configured througth the Plugin Manager anyway.
 */
if (!class_exists( 'VmConfig' )) require(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_virtuemart'.DS.'helpers'.DS.'config.php');
if (!class_exists('ShopFunctions'))
    require(JPATH_VM_ADMINISTRATOR . DS . 'helpers' . DS . 'shopfunctions.php');
JFormHelper::loadFieldClass('list');
jimport('joomla.form.formfield');



/**
 * Renders a multiple item select element
 *
 */
class JFormFieldCreditCards extends JFormFieldList {

	public $type = 'creditcards';

		protected function getOptions() {
			return parent::getOptions();
		}


}