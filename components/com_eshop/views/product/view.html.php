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
 * HTML View class for EShop component
 *
 * @static
 * @package		Joomla
 * @subpackage	EShop
 * @since 1.5
 */
class EShopViewProduct extends EShopView
{

	/**
	 * Display function
	 * @see JView::display()
	 */
	function display($tpl = null)
	{
		$mainframe = JFactory::getApplication();
		$session = JFactory::getSession();
		$document = JFactory::getDocument();
		$item = $this->get('Data');		
		$this->currency = new EshopCurrency();
		if (!is_object($item))
		{
			// Requested product does not existed.
			$session->set('warning', JText::_('ESHOP_PRODUCT_DOES_NOT_EXIST'));
			$mainframe->redirect(JRoute::_(EshopRoute::getViewRoute('categories')));
		}
		else 
		{
			$document->addStyleSheet(JUri::base(true).'/components/com_eshop/assets/colorbox/colorbox.css');
			if (EshopHelper::getConfigValue('view_image') == 'zoom')
			{
				$document->addStyleSheet(JUri::base(true).'/components/com_eshop/assets/css/jquery.jqzoom.css');
			}
			$document->addStyleSheet(JUri::base(true).'/components/com_eshop/assets/css/labels.css');
			$productId = JRequest::getInt('id');
			//Set session for viewed products
			$viewedProductIds = $session->get('viewed_product_ids');
			if (!count($viewedProductIds))
			{
				$viewedProductIds = array();
			}
			if (!in_array($productId, $viewedProductIds))
			{
				$viewedProductIds[] = $productId;
			}
			$session->set('viewed_product_ids', $viewedProductIds);
			// Handle breadcrumb
			$db = JFactory::getDbo();
			$categoryId = JRequest::getVar('catid');
			if (!$categoryId)
			{
				$query = $db->getQuery(true);
				$query->select('a.id')
					->from('#__eshop_categories AS a')
					->innerJoin('#__eshop_productcategories AS b ON a.id = b.category_id')
					->where('b.product_id = ' . (int)$productId);
				$db->setQuery($query);
				$categoryId = (int)$db->loadResult();
			}
			$app		= JFactory::getApplication();
			$menu		= $app->getMenu();
			$menuItem = $menu->getActive();
			if ($menuItem)
			{
				if (isset($menuItem->query['view']) && ($menuItem->query['view']== 'frontpage' || $menuItem->query['view']== 'categories' || $menuItem->query['view'] == 'category'))
				{
					$parentId = isset($menuItem->query['id']) ? (int)$menuItem->query['id'] : '0';
					if ($categoryId)
					{
						$pathway = $app->getPathway();
						$paths = EshopHelper::getCategoriesBreadcrumb($categoryId, $parentId);
						for ($i = count($paths) - 1; $i >= 0; $i--)
						{
							$category = $paths[$i];
							$pathUrl = EshopRoute::getCategoryRoute($category->id);
							$pathway->addItem($category->category_name, $pathUrl);
						}
						$pathway->addItem($item->product_name);
					}
				}
			}
			// Update hits for product
			EshopHelper::updateHits($productId, 'products');
			// Set title of the page
	        $siteNamePosition = $app->getCfg('sitename_pagetitles');
	        $productPageTitle = $item->product_page_title != '' ? $item->product_page_title : $item->product_name;
			if ($siteNamePosition == 0)
			{
				$title = $productPageTitle;
			}
			elseif($siteNamePosition == 1)
			{
				$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $productPageTitle);
			}
			else
			{
				$title = JText::sprintf('JPAGETITLE', $productPageTitle, $app->getCfg('sitename'));
			}
			$document->setTitle($title);
			$additionalImageSizeFunction = EshopHelper::getConfigValue('additional_image_size_function', 'resizeImage');
			$thumbImageSizeFunction = EshopHelper::getConfigValue('thumb_image_size_function', 'resizeImage');
			$popupImageSizeFunction = EshopHelper::getConfigValue('popup_image_size_function', 'resizeImage');
			$relatedImageSizeFunction = EshopHelper::getConfigValue('related_image_size_function', 'resizeImage');
			// Main image resize
			if ($item->product_image && JFile::exists(JPATH_ROOT.'/media/com_eshop/products/' . $item->product_image))
			{
				$smallThumbImage = call_user_func_array(array('EshopHelper', $additionalImageSizeFunction), array($item->product_image, JPATH_ROOT . '/media/com_eshop/products/', EshopHelper::getConfigValue('image_additional_width'), EshopHelper::getConfigValue('image_additional_height')));
				$thumbImage = call_user_func_array(array('EshopHelper', $thumbImageSizeFunction), array($item->product_image, JPATH_ROOT . '/media/com_eshop/products/', EshopHelper::getConfigValue('image_thumb_width'), EshopHelper::getConfigValue('image_thumb_height')));
				$popupImage = call_user_func_array(array('EshopHelper', $popupImageSizeFunction), array($item->product_image, JPATH_ROOT . '/media/com_eshop/products/', EshopHelper::getConfigValue('image_popup_width'), EshopHelper::getConfigValue('image_popup_height')));
			}
			else
			{
				$smallThumbImage = call_user_func_array(array('EshopHelper', $additionalImageSizeFunction), array('no-image.png', JPATH_ROOT . '/media/com_eshop/products/', EshopHelper::getConfigValue('image_additional_width'), EshopHelper::getConfigValue('image_additional_height')));
				$thumbImage = call_user_func_array(array('EshopHelper', $thumbImageSizeFunction), array('no-image.png', JPATH_ROOT . '/media/com_eshop/products/', EshopHelper::getConfigValue('image_thumb_width'), EshopHelper::getConfigValue('image_thumb_height')));
				$popupImage = call_user_func_array(array('EshopHelper', $popupImageSizeFunction), array('no-image.png', JPATH_ROOT . '/media/com_eshop/products/', EshopHelper::getConfigValue('image_popup_width'), EshopHelper::getConfigValue('image_popup_height')));
			}
			$item->small_thumb_image = JUri::base(true) . '/media/com_eshop/products/resized/' . $smallThumbImage;
			$item->thumb_image = JUri::base(true) . '/media/com_eshop/products/resized/' . $thumbImage;
			$item->popup_image = JUri::base(true) . '/media/com_eshop/products/resized/' . $popupImage;
			// Set metakey and metadesc
			$metaKey = $item->meta_key;
			$metaDesc = $item->meta_desc;
			if ($metaKey)
			{
				$document->setMetaData('keywords', $metaKey);
			}
			if ($metaDesc)
			{
				$document->setMetaData('description', $metaDesc);
			}
			// Product availability
			if ($item->product_quantity <= 0)
			{
				$availability = EshopHelper::getStockStatusName($item->product_stock_status_id ? $item->product_stock_status_id : EshopHelper::getConfigValue('stock_status_id'), JFactory::getLanguage()->getTag());
			}
			elseif (EshopHelper::getConfigValue('stock_display'))
			{
				$availability = $item->product_quantity;
			}
			else
			{
				$availability = JText::_('ESHOP_IN_STOCK');
			}
			$item->availability = $availability;
			// Product tags
			$productTags = EshopHelper::getProductTags($item->id);
			$this->productTags = $productTags;
			$item->product_desc = JHtml::_('content.prepare', $item->product_desc);
			if ($item->tab1_title != '' && $item->tab1_content != '')
				$item->tab1_content = JHtml::_('content.prepare', $item->tab1_content);
			if ($item->tab2_title != '' && $item->tab2_content != '')
				$item->tab2_content = JHtml::_('content.prepare', $item->tab2_content);
			if ($item->tab3_title != '' && $item->tab3_content != '')
				$item->tab3_content = JHtml::_('content.prepare', $item->tab3_content);
			if ($item->tab4_title != '' && $item->tab4_content != '')
				$item->tab4_content = JHtml::_('content.prepare', $item->tab4_content);
			if ($item->tab5_title != '' && $item->tab5_content != '')
				$item->tab5_content = JHtml::_('content.prepare', $item->tab5_content);
			// Get information related to this current product
			$productImages = EshopHelper::getProductImages($productId);
			// Additional images resize
			for ($i = 0; $n = count($productImages), $i < $n; $i++)
			{
				if ($productImages[$i]->image && JFile::exists(JPATH_ROOT.'/media/com_eshop/products/' . $productImages[$i]->image))
				{
					$smallThumbImage = call_user_func_array(array('EshopHelper', $additionalImageSizeFunction), array($productImages[$i]->image, JPATH_ROOT . '/media/com_eshop/products/', EshopHelper::getConfigValue('image_additional_width'), EshopHelper::getConfigValue('image_additional_height')));
					$thumbImage = call_user_func_array(array('EshopHelper', $thumbImageSizeFunction), array($productImages[$i]->image, JPATH_ROOT . '/media/com_eshop/products/', EshopHelper::getConfigValue('image_thumb_width'), EshopHelper::getConfigValue('image_thumb_height')));
					$popupImage = call_user_func_array(array('EshopHelper', $popupImageSizeFunction), array($productImages[$i]->image, JPATH_ROOT . '/media/com_eshop/products/', EshopHelper::getConfigValue('image_popup_width'), EshopHelper::getConfigValue('image_popup_height')));
				}
				else
				{
					$smallThumbImage = call_user_func_array(array('EshopHelper', $additionalImageSizeFunction), array('no-image.png', JPATH_ROOT . '/media/com_eshop/products/', EshopHelper::getConfigValue('image_additional_width'), EshopHelper::getConfigValue('image_additional_height')));
					$thumbImage = call_user_func_array(array('EshopHelper', $thumbImageSizeFunction), array('no-image.png', JPATH_ROOT . '/media/com_eshop/products/', EshopHelper::getConfigValue('image_thumb_width'), EshopHelper::getConfigValue('image_thumb_height')));
					$popupImage = call_user_func_array(array('EshopHelper', $popupImageSizeFunction), array('no-image.png', JPATH_ROOT . '/media/com_eshop/products/', EshopHelper::getConfigValue('image_popup_width'), EshopHelper::getConfigValue('image_popup_height')));
				}
				$productImages[$i]->small_thumb_image = JUri::base(true) . '/media/com_eshop/products/resized/' . $smallThumbImage;
				$productImages[$i]->thumb_image = JUri::base(true) . '/media/com_eshop/products/resized/' . $thumbImage;
				$productImages[$i]->popup_image = JUri::base(true) . '/media/com_eshop/products/resized/' . $popupImage;
			}
			$discountPrices = EshopHelper::getDiscountPrices($productId);
			$productOptions = EshopHelper::getProductOptions($productId, JFactory::getLanguage()->getTag());
			$hasSpecification = false;
			$attributeGroups = EshopHelper::getAttributeGroups(JFactory::getLanguage()->getTag());
			$productAttributes = array();
			for ($i = 0; $n = count($attributeGroups), $i < $n; $i++)
			{
				$productAttributes[] = EshopHelper::getAttributes($productId, $attributeGroups[$i]->id, JFactory::getLanguage()->getTag());
				if (count($productAttributes[$i])) $hasSpecification = true;
			}
			$productRelations = EshopHelper::getProductRelations($productId, JFactory::getLanguage()->getTag());
			// Related products images resize
			for ($i = 0; $n = count($productRelations), $i < $n; $i++)
			{
				if ($productRelations[$i]->product_image && JFile::exists(JPATH_ROOT.'/media/com_eshop/products/' . $productRelations[$i]->product_image))
				{
					$thumbImage = call_user_func_array(array('EshopHelper', $relatedImageSizeFunction), array($productRelations[$i]->product_image, JPATH_ROOT . '/media/com_eshop/products/', EshopHelper::getConfigValue('image_related_width'), EshopHelper::getConfigValue('image_related_height')));
				}
				else
				{
					$thumbImage = call_user_func_array(array('EshopHelper', $relatedImageSizeFunction), array('no-image.png', JPATH_ROOT . '/media/com_eshop/products/', EshopHelper::getConfigValue('image_related_width'), EshopHelper::getConfigValue('image_related_height')));
				}
				$productRelations[$i]->thumb_image = JUri::base(true) . '/media/com_eshop/products/resized/' . $thumbImage;
			}
			if (EshopHelper::getConfigValue('allow_reviews'))
			{
				$productReviews = EshopHelper::getProductReviews($productId);
				$this->productReviews = $productReviews;
			}
			$tax = new EshopTax(EshopHelper::getConfig());
			if (EshopHelper::getConfigValue('social_enable'))
			{
				EshopHelper::loadShareScripts($item);
			}
			$this->item = $item;
			$this->productImages = $productImages;
			$this->discountPrices = $discountPrices;
			$this->productOptions = $productOptions;
			$this->hasSpecification = $hasSpecification;
			$this->attributeGroups = $attributeGroups;
			$this->productAttributes = $productAttributes;
			$this->productRelations = $productRelations;
			$this->manufacturer = EshopHelper::getProductManufacturer($productId, JFactory::getLanguage()->getTag());
			$this->tax = $tax;
			// Preparing rating html
			$ratingHtml = '<b>' . JText::_('ESHOP_BAD') . '</b>';
			for ($i = 1; $i <= 5; $i++)
			{
				$ratingHtml .= '<input type="radio" name="rating" value="' . $i . '" style="width: 25px;" />';
			}
			$ratingHtml .= '<b>' . JText::_('ESHOP_EXCELLENT') . '</b>';
			$this->ratingHtml = $ratingHtml;
			$this->productsNavigation = EshopHelper::getProductsNavigation($item->id);
			$this->labels = EshopHelper::getProductLabels($item->id);
			//Captcha
			$showCaptcha = 0;
			if (EshopHelper::getConfigValue('enable_reviews_captcha'))
			{
				$captchaPlugin = JFactory::getApplication()->getParams()->get('captcha', JFactory::getConfig()->get('captcha'));
				if ($captchaPlugin == 'recaptcha')
				{
					$showCaptcha = 1;
					$this->captcha = JCaptcha::getInstance($captchaPlugin)->display('dynamic_recaptcha_1', 'dynamic_recaptcha_1', 'required');
				}
			}
			$this->showCaptcha = $showCaptcha;
			parent::display($tpl);
		}
	}
}