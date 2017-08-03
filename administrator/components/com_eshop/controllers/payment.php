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
 * EShop controller
 *
 * @package		Joomla
 * @subpackage	EShop
 * @since 1.5
 */
class EShopControllerPayment extends EShopController
{

	/**
	 * Constructor function
	 *
	 * @param array $config
	 */
	function __construct($config = array())
	{
		parent::__construct($config);
	
	}

	function install()
	{
		$model = & $this->getModel('payment');
		$ret = $model->install();
		if ($ret)
		{
			$msg = JText::_('ESHOP_PLUGIN_INSTALLED');
		}
		else
		{
			$msg = JRequest::setVar('msg', 'ESHOP_PLUGIN_INSTALL_ERROR');
		}
		$this->setRedirect('index.php?option=com_eshop&view=payments', $msg);
	}
}