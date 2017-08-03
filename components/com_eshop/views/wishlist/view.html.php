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
 * HTML View class for EShop component
 *
 * @static
 * @package		Joomla
 * @subpackage	EShop
 * @since 1.5
 */
class EShopViewWishlist extends EShopView
{		
	function display($tpl = null)
	{
		$mainframe = JFactory::getApplication();
		$tax = new EshopTax(EshopHelper::getConfig());
		$currency = new EshopCurrency();
		$user = JFactory::getUser();
		if (!$user->get('id'))
		{
			$mainframe->enqueueMessage(JText::_('ESHOP_YOU_MUST_LOGIN_TO_VIEW_WISHLIST'), 'Notice');
			$mainframe->redirect('index.php?option=com_users&view=login&return=' . base64_encode('index.php?option=com_eshop&view=wishlist'));
		}
		else
		{
			$document = JFactory::getDocument();
			$app		= JFactory::getApplication();
			$title = JText::_('ESHOP_WISHLIST');
			// Set title of the page
			$siteNamePosition = $app->getCfg('sitename_pagetitles');
			if($siteNamePosition == 1)
			{
				$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $title);
			}
			elseif ($siteNamePosition == 2)
			{
				$title = JText::sprintf('JPAGETITLE', $title, $app->getCfg('sitename'));
			}
			$document->setTitle($title);
			$document->addStyleSheet(JUri::base(true).'/components/com_eshop/assets/colorbox/colorbox.css');
			$session = JFactory::getSession();
			$wishlist = ($session->get('wishlist') ? $session->get('wishlist') : array());
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			if (count($wishlist))
			{
				//Save wishlist from session
				foreach ($wishlist as $productId)
				{
					$query->clear();
					$query->select('COUNT(*)')
						->from('#__eshop_wishlists')
						->where('customer_id = ' . intval($user->get('id')))
						->where('product_id = ' . intval($productId));
					$db->setQuery($query);
					if (!$db->loadResult())
					{
						$row = JTable::getInstance('Eshop', 'Wishlist');
						$row->customer_id = $user->get('id');
						$row->product_id = $productId;
						$row->store();
					}
				}
				$session->clear('wishlist');
			}
			$query->clear();
			$query->select('a.*, b.product_name')
				->from('#__eshop_products AS a')
				->innerJoin('#__eshop_wishlists AS w ON (a.id = w.product_id)')
				->innerJoin('#__eshop_productdetails AS b ON (a.id = b.product_id)')
				->where('a.published = 1')
				->where('w.customer_id = ' . intval($user->get('id')))
				->where('b.language = "' . JFactory::getLanguage()->getTag() . '"');
			$db->setQuery($query);
			$products = $db->loadObjectList();
			for ($i = 0; $n = count($products), $i < $n; $i++)
			{
				// Resize wishlist images
				$imageSizeFunction = EshopHelper::getConfigValue('wishlist_image_size_function', 'resizeImage');
				if ($products[$i]->product_image && JFile::exists(JPATH_ROOT.'/media/com_eshop/products/'.$products[$i]->product_image))
				{
					$image = call_user_func_array(array('EshopHelper', $imageSizeFunction), array($products[$i]->product_image, JPATH_ROOT . '/media/com_eshop/products/', EshopHelper::getConfigValue('image_wishlist_width'), EshopHelper::getConfigValue('image_wishlist_height')));
				}
				else
				{
					$image = call_user_func_array(array('EshopHelper', $imageSizeFunction), array('no-image.png', JPATH_ROOT . '/media/com_eshop/products/', EshopHelper::getConfigValue('image_wishlist_width'), EshopHelper::getConfigValue('image_wishlist_height')));
				}
				$products[$i]->image = JUri::base(true) . '/media/com_eshop/products/resized/' . $image;
				// Product availability
				if ($products[$i]->product_quantity <= 0)
				{
					$availability = EshopHelper::getStockStatusName($products[$i]->product_stock_status_id ? $products[$i]->product_stock_status_id : EshopHelper::getConfigValue('stock_status_id'), JFactory::getLanguage()->getTag());
				}
				elseif (EshopHelper::getConfigValue('stock_display'))
				{
					$availability = $products[$i]->product_quantity;
				}
				else
				{
					$availability = JText::_('ESHOP_IN_STOCK');
				}
				$products[$i]->availability = $availability;
				// Price
				$productPriceArray = EshopHelper::getProductPriceArray($products[$i]->id, $products[$i]->product_price);
				if ($productPriceArray['salePrice'])
				{
					$basePrice = $currency->format($tax->calculate($productPriceArray['basePrice'], $productInfo->product_taxclass_id, EshopHelper::getConfigValue('tax')));
					$salePrice = $currency->format($tax->calculate($productPriceArray['salePrice'], $productInfo->product_taxclass_id, EshopHelper::getConfigValue('tax')));
				}
				else
				{
					$basePrice = $currency->format($tax->calculate($productPriceArray['basePrice'], $productInfo->product_taxclass_id, EshopHelper::getConfigValue('tax')));
					$salePrice = 0;
				}
				$products[$i]->base_price = $basePrice;
				$products[$i]->sale_price = $salePrice;
			}
			if ($session->get('success'))
			{
			$this->success = $session->get('success');
			$session->clear('success');
			}
			$this->products = $products;
			$this->tax = $tax;
			$this->currency = $currency;
		}
		parent::display($tpl);
	}
}