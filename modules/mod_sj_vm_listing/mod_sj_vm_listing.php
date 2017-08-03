<?php
/**
 * @package Sj Vm Listing
 * @version 2.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2012 YouTech Company. All Rights Reserved.
 * @author YouTech Company http://www.smartaddons.com
 * 
 */
defined( '_JEXEC' ) or die;

defined('_YTOOLS') or include_once 'core' . DS . 'sjimport.php';

// set current module for working
YTools::setModule($module);
// import jQuery
if (!defined('SMART_JQUERY') && (int)$params->get('include_jquery', '1')){
	YTools::script('jquery-1.5.min.js');
	define('SMART_JQUERY', 1);
}

if (!defined('SMART_NOCONFLICT')){
	YTools::script('jsmart.noconflict.js');
	define('SMART_NOCONFLICT', 1);
}

YTools::script('jsmart.listing.js');
YTools::stylesheet('sj-listing.css');

include_once dirname(__FILE__).'/core/helper.php';

$params->def('reader', 'Reader');
$layout_name = $params->get('theme', 'theme1');
$cacheid = md5(serialize(array ($layout_name, $module->module)));
$cacheparams = new stdClass;
$cacheparams->cachemode = 'id';
$cacheparams->class = 'sj_vm_listing_helper';
$cacheparams->method = 'getList';
$cacheparams->methodparams = array($params, $module);
$cacheparams->modeparams = $cacheid;
$list = JModuleHelper::moduleCache ($module, $params, $cacheparams);
include JModuleHelper::getLayoutPath($module->module,$layout_name);?>
