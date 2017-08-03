<?php
/**
 * @package SJ Categories for VirtueMart
 * @version 2.2.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2014 YouTech Company. All Rights Reserved.
 * @author YouTech Company http://www.smartaddons.com
 *
 */

defined('_JEXEC') or die;

require_once dirname(__FILE__) . '/vmloader.php';
require_once dirname(__FILE__) . '/helper_base.php';

abstract class VmCategoriesHelper extends VmCategoriesBaseHelper
{
	private static $_category_tree;
    public static function getList(&$params)
    {
        if (!class_exists('VmConfig')) return;
        VmConfig::loadConfig();
        JFactory::getLanguage()->load('com_virtuemart');

        $list = array();
        $_list = array();
        $catids = $params->get('catid');
        settype($catids, 'array');
        $count = $params->get('count', 5);
        $categoryModel = VmModel::getModel('Category');
        $categoryModel->_noLimit = true;

        $categories = array();
        if (!empty($catids)) {
            $categories = self::getCategories(true, false, false, '');
			$_catids = array();
            foreach ($categories as $_category) {
                $_catids[] = $_category->virtuemart_category_id;
            }
			$__catids = array();
            foreach ($catids as $cat) {
                if (in_array($cat, $_catids)) {
                    $__catids[] = $cat;
                }
            }

            foreach ($__catids as $_cat) {
                $category = $categoryModel->getCategory($_cat, false);
                $_list[] = $category;
            }

            if (!empty($_list)) {
                $categoryModel->addImages($_list);
                foreach ($_list as $item) {
                    $item->cat_description = self::_cleanText($item->category_description);
                    $item->child_cat = self::_getChildrenCat($categoryModel, $item->virtuemart_category_id, $count);
                    $categoryModel->addImages($item->child_cat);
                    $list[] = $item;
                }
            }
        }
        return $list;
    }

    public static function _getChildrenCat($categoryModel, $cat_id, $count)
    {
        $list = array();
        $items = array();
        $list = $categoryModel->getChildCategoryList(1, $cat_id);
        if (!empty($list)) {
            if ($count == 0) {
                $_list = $list;
            } else {
                $_list = array_slice($list, 0, $count);
            }
            foreach ($_list as $key => $item) {
                $item->number_product = $categoryModel->countProducts($item->virtuemart_category_id);
                $items[] = $item;
            }
            return $items;
        }

    }
	public static function getCategories($onlyPublished = true, $parentId = false, $childId = false, $keyword = "", $vendorId = false) {


		$categoryModel = VmModel::getModel('Category');
		$select = ' c.`virtuemart_category_id`, l.`category_description`, l.`category_name`, c.`ordering`, c.`published`, cx.`category_child_id`, cx.`category_parent_id`, c.`shared` ';

		$joinedTables = ' FROM `#__virtuemart_categories_'.VmConfig::$vmlang.'` l
				  JOIN `#__virtuemart_categories` AS c using (`virtuemart_category_id`)
				  LEFT JOIN `#__virtuemart_category_categories` AS cx
				  ON l.`virtuemart_category_id` = cx.`category_child_id` ';

		$where = array();

		if( $onlyPublished ) {
			$where[] = " c.`published` = 1 ";
		}
		if( $parentId !== false ){
			$where[] = ' cx.`category_parent_id` = '. (int)$parentId;
		}

		if( $childId !== false ){
			$where[] = ' cx.`category_child_id` = '. (int)$childId;
		}

		if($vendorId===false){
			$vendorId = 1;
		}
		if($vendorId!=1){
			echo $vendorId;
			$where[] = ' (c.`virtuemart_vendor_id` = "'. (int)$vendorId. '" OR c.`shared` = "1") ';
		}

		if( !empty( $keyword ) ) {
			$db = JFactory::getDBO();
			$keyword = '"%' . $db->escape( $keyword, true ) . '%"' ;
			//$keyword = $db->Quote($keyword, false);
			$where[] = ' ( l.`category_name` LIKE '.$keyword.'
							   OR l.`category_description` LIKE '.$keyword.') ';
		}

		$whereString = '';
		if (count($where) > 0){
			$whereString = ' WHERE '.implode(' AND ', $where) ;
		} else {
			$whereString = 'WHERE 1 ';
		}

		$ordering = $categoryModel->_getOrdering();

		self::$_category_tree = $categoryModel->exeSortSearchListQuery(0,$select,$joinedTables,$whereString,'GROUP BY virtuemart_category_id',$ordering );
		return self::$_category_tree;

	}

}