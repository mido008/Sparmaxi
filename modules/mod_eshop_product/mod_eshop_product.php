<?php
/**
 * @version		1.3.1
 * @package		Joomla
 * @subpackage	EShop
 * @author  	Giang Dinh Truong
 * @copyright	Copyright (C) 2011 Ossolution Team
 * @license		GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die;

require_once (dirname(__FILE__).'/helper.php');
require_once JPATH_ROOT . '/administrator/components/com_eshop/libraries/defines.php';
require_once JPATH_ROOT . '/administrator/components/com_eshop/libraries/inflector.php';
require_once JPATH_ROOT . '/administrator/components/com_eshop/libraries/autoload.php';
// Get parameters
$headerText			= $params->get('header_text', '');
$footerText         = $params->get('footer_text', '');
$productsPerRow 	= $params->get('products_per_row', 4);
$showPrice			= $params->get('show_price', 1);
$showAddcart        = $params->get('show_addtocart', 1);
$showAddToWishlist	= $params->get('show_add_to_wishlist', 1);
$showAddToCompare	= $params->get('show_add_to_compare', 1);
$showRating 		= $params->get('show_rating', 1);
$layout 			= $params->get('layout', 'default');
$showTooltip		= $params->get('show_tooltip', 1);
$tooltipLength		= $params->get('tooltip_length', 0);
$thumbnailWidth     = $params->get('image_width', 100);
$thumbnailHeight    = $params->get('image_height', 100);

$currency = new EshopCurrency();
$tax = new EshopTax(EshopHelper::getConfig());
$document = JFactory::getDocument();
$template = JFactory::getApplication()->getTemplate();

$theme = EshopHelper::getConfigValue('theme');
// Load CSS of corresponding theme
$document = JFactory::getDocument();
$theme = EshopHelper::getConfigValue('theme');
jimport('joomla.filesystem.file');
if (JFile::exists(JPATH_ROOT.'/components/com_eshop/themes/' . $theme . '/css/style.css'))
{
	$document->addStyleSheet(JUri::base(true).'/components/com_eshop/themes/' . $theme . '/css/style.css');
}
else
{
	$document->addStyleSheet(JUri::base(true).'/components/com_eshop/themes/default/css/style.css');
}
// Load custom CSS file
if (JFile::exists(JPATH_ROOT.'/components/com_eshop/themes/' . $theme . '/css/custom.css'))
{
	$document->addStyleSheet(JUri::base(true).'/components/com_eshop/themes/' . $theme . '/css/custom.css');
}
else
{
	$document->addStyleSheet(JUri::base(true).'/components/com_eshop/themes/default/css/custom.css');
}
if (JFile::exists(JPATH_ROOT.'/templates/'. $template .  '/css/'  . $module->module . '.css'))
{
	$document->addStyleSheet(JURI::base().'templates/' . $template . '/css/' . $module->module . '.css');
}
else 
{
	$document->addStyleSheet(JURI::base().'modules/' . $module->module . '/assets/css/style.css');
}

// Load Bootstrap CSS and JS
if (EshopHelper::getConfigValue('load_bootstrap_css'))
{
	EshopHelper::loadBootstrapCss();
}
if (EshopHelper::getConfigValue('load_bootstrap_js'))
{
	EshopHelper::loadBootstrapJs();
}
JHtml::_('script', EshopHelper::getSiteUrl(). 'components/com_eshop/assets/js/noconflict.js', false, false);
JHtml::_('script', EshopHelper::getSiteUrl(). 'components/com_eshop/assets/js/eshop.js', false, false);
JHtml::_('script', EshopHelper::getSiteUrl(). 'components/com_eshop/assets/js/slick.js', false, false);
JHtml::_('script', EshopHelper::getSiteUrl(). 'modules/mod_eshop_product/assets/js/owl.carousel.js', false, false);

//Load javascript and css
$document->addScript(JURI::root().'components/com_eshop/assets/js/eshop.js');
$document->addScript(JURI::root().'components/com_eshop/assets/colorbox/jquery.colorbox.js');
$document->addStyleSheet(JURI::root().'components/com_eshop/assets/colorbox/colorbox.css');
$document->addStyleSheet(JUri::base().'components/com_eshop/assets/css/labels.css');
//Load css module
if($showTooltip == 1){
	$document->addScriptDeclaration("
		(function($){
			$(document).ready(function() {
			    $('.link').tooltip();
			});
		})(jQuery);
	");
}
$items = modEshopProductHelper::getItems($params);
require JModuleHelper::getLayoutPath('mod_eshop_product', $params->get('layout', 'default'));