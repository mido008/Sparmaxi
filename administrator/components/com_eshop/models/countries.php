<?php
/**
 * @version		1.4.1
 * @package		Joomla
 * @subpackage	EShop
 * @author  	Giang Dinh Truong
 * @copyright	Copyright (C) 2012 Ossolution Team
 * @license		GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die();

/**
 * Eshop Component Model
 *
 * @package		Joomla
 * @subpackage	EShop
 * @since 1.5
 */
class EShopModelCountries extends EShopModelList
{
	function __construct($config)
	{
		$config['search_fields'] = array('country_name', 'iso_code_2', 'iso_code_3');
		$config['state_vars'] = array('filter_order' => array('a.country_name', 'string', 1));
		parent::__construct($config);
	}
}