<?php
/**
 * @version		1.0.0
 * @package		Joomla
 * @subpackage	EShop
 * @author  	Giang Dinh Truong
 * @copyright	Copyright (C) 2011 Ossolution Team
 * @license		GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die;

// Include the syndicate functions only once
require_once( dirname(__FILE__).'/helper.php' );
$tags = modEshopTagsHelper::getListTag($params);
require(JModuleHelper::getLayoutPath('mod_eshop_tags'));