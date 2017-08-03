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
defined( '_JEXEC' ) or die();
/**
 * EShop controller
 *
 * @package		Joomla
 * @subpackage	EShop
 * @since 1.5
 */
class EshopController extends JControllerLegacy
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
	
	/**
	 * Display information
	 *
	 */
	function display($cachable = false, $urlparams = false)
	{				    	
		$task = $this->getTask();
		$view = JRequest::getVar('view', '');
		if (!$view)
		{
			JRequest::setVar('view', 'dashboard');
		}
		EShopHelper::renderSubmenu(JRequest::getVar('view', 'configuration'));
		parent::display();
		EShopHelper::displayCopyRight();
	}
	
	/**
	 * 
	 * Function to install sample data
	 */
	function installSampleData()
	{
		$mainframe = JFactory::getApplication();
		$db = JFactory::getDbo();
		$sampleSql = JPATH_ADMINISTRATOR.'/components/com_eshop/sql/sample.eshop.sql';
		$query = JFile::read($sampleSql);
		$queries = $db->splitSql($query);
		if (count($queries))
		{
			foreach ($queries as $query)
			{
				$query = trim($query);
				if ($query != '' && $query{0} != '#')
				{
					$db->setQuery($query);
					$db->query();
				}
			}
		}
		$mainframe->enqueueMessage(JText::_('ESHOP_INSTALLATION_DONE'));
		$mainframe->redirect('index.php?option=com_eshop&view=dashboard');
	}

	/**
	 * 
	 * Function to check if extension is up to date or not
	 * @return 0: error, 1: Up to date, 2: Out of date
	 */
	function checkUpdate()
	{
		$installedVersion = EshopHelper::getInstalledVersion();
		$result = array();
		$result['status'] = 0;
		if (function_exists('curl_init'))
		{
			$url = 'http://joomdonationdemo.com/versions/eshop.txt';
			$ch = curl_init();
			curl_setopt($ch, CURLOPT_URL, $url);
			curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
			$latestVersion = curl_exec($ch);
			curl_close($ch);
			if ($latestVersion)
			{
				if (version_compare($latestVersion, $installedVersion, 'gt'))
				{
					$result['status'] = 2;
					$result['message'] = JText::sprintf('ESHOP_UPDATE_CHECKING_UPDATE_FOUND', $latestVersion);
				}
				else
				{
					$result['status'] = 1;
					$result['message'] = JText::_('ESHOP_UPDATE_CHECKING_UP_TO_DATE');
				}
			}
		}
		echo json_encode($result);
		JFactory::getApplication()->close();
	}
}