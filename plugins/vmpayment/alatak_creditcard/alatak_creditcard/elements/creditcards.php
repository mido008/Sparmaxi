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

/**
 * @copyright	Copyright (C) 2009 Open Source Matters. All rights reserved.
 * @license	GNU/GPL
 */


/**
 * Renders a multiple item select element
 *
 */

class JElementCreditCards extends JElement {

    /**
     * Element name
     *
     * @access	protected
     * @var		string
     */

    var $_name = 'creditcards';

    function fetchElement($name, $value, &$node, $control_name) {
	// Base name of the HTML control.
	$ctrl = $control_name . '[' . $name . ']';

	// Construct an array of the HTML OPTION statements.
	$options = array();
	foreach ($node->children() as $option) {
	    $text = $option->data();
	    $val = $option->attributes('value');
	    $options[] = JHTML::_('select.option', $val, vmText::_($text));
	}

	// Construct the various argument calls that are supported.
	$attribs = ' ';

	$attribs .= ' multiple="multiple"';
	$ctrl .= '[]';


	// Render the HTML SELECT list.
	return JHTML::_('select.genericlist', $options, $ctrl, $attribs, 'value', 'text', $value, $control_name . $name);
    }

}