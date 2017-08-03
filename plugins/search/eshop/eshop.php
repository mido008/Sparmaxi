<?php
/**
 * @version		1.3.1
 * @package		Joomla
 * @subpackage	EShop
 * @author  	Giang Dinh Truong
 * @copyright	Copyright (C) 2012 Ossolution Team
 * @license		GNU/GPL, see LICENSE.php
 */
defined('_JEXEC') or die();

jimport('joomla.plugin.plugin');
require_once(JPATH_ADMINISTRATOR.'/components/com_search/helpers/search.php');
require_once(JPATH_SITE.'/components/com_eshop/helpers/helper.php');
require_once(JPATH_SITE . '/components/com_eshop/helpers/' . ((version_compare(JVERSION, '3.0', 'ge') && JLanguageMultilang::isEnabled()) ? 'routev3.php' : 'route.php'));

class plgSearchEshop extends JPlugin
{
	
    public function __construct(&$subject, $config)
    {
        parent::__construct($subject, $config);
        $this->loadLanguage();
    }

    public function onContentSearch($text, $phrase = '', $ordering = '', $areas = null)
    {
        return $this->onSearch($text, $phrase, $ordering, $areas);
    }

    public function onContentSearchAreas()
    {
        return $this->onSearchAreas();
    }

	public function onSearchAreas()
	{
        static $areas = array('eshop' => 'Products');
        return $areas;
	}

	public function onSearch($text, $phrase = '', $ordering = '', $areas = null)
	{
		if (is_array($areas))
		{
			if (!array_intersect($areas, array_keys($this->onContentSearchAreas())))
			{
				return array();
			}
		}
		$plugin = JPluginHelper::getPlugin('search', 'eshop');
		$params = new JRegistry($plugin->params);
		$text = JString::trim($text);
		if ($text == '')
		{
			return array();
		}
		$text = JString::strtolower($text);
		$db	= JFactory::getDBO();
        $limit = $params->get('search_limit', 50);
        switch ($ordering)
        {
            case 'oldest':
                $orderBy = 'p.created_date ASC';
                break;
            case 'popular':
                $orderBy = 'p.hits DESC';
                break;
            case 'alpha':
                $orderBy = 'pd.product_name ASC';
                break;
            case 'category':
                $orderBy = 'cd.category_name ASC, pd.product_name ASC';
                break;
            case 'newest':
            default :
                $orderBy = 'p.created_date DESC';
                break;
        }
		$query = "SELECT DISTINCT pd.product_id, pd.product_name AS title, pd.product_desc AS text, p.created_date AS created" .
			" FROM #__eshop_products AS p" .
			" INNER JOIN #__eshop_productdetails AS pd ON p.id = pd.product_id" .
			" WHERE (LOWER(p.product_sku) LIKE '%" . $text. "%' OR
				LOWER(pd.product_name) LIKE '%" . $text . "%' OR
				LOWER(pd.product_short_desc) LIKE '%" . $text. "%' OR
				LOWER(pd.product_desc) LIKE '%" . $text. "%')" .
			" AND p.published = '1'" .
			" AND p.product_available_date <= NOW()" .
			" GROUP BY p.id" .
			" ORDER BY {$orderBy}" .
			" LIMIT " . $limit;
		$db->setQuery($query);
		$results = $db->loadObjectList();
		$ret = array();
		if (empty($results))
		{
			return $ret;
		}
		foreach ($results as $result)
		{
			$categoryId = EshopHelper::getProductCategory($result->product_id);
			if ($categoryId > 0)
			{
				$category = EshopHelper::getCategory($categoryId, false);
				$result->href = EshopRoute::getProductRoute($result->product_id, $categoryId);
				$result->section = $category->category_name;
				$result->browsernav = 2;
				$ret[] = $result;
			}
		}
		return $ret;
	}
}