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
class EShopViewManufacturer extends EShopView
{
	function display($tpl = null)
	{
		$app = JFactory::getApplication();
		$model = $this->getModel();
		$state = $model->getState();		
		$manufacturer = EshopHelper::getManufacturer($state->id, true, true);
		if (!is_object($manufacturer))
		{
			// Requested manufacturer does not existed.
			$session = JFactory::getSession();
			$session->set('warning', JText::_('ESHOP_MANUFACTURER_DOES_NOT_EXIST'));
			$app->redirect(JRoute::_(EshopRoute::getViewRoute('categories')));
		}
		else
		{
			$products = $model->getData();
			$pagination = $model->getPagination();
			$document = JFactory::getDocument();
			$document->addStyleSheet(JUri::base(true).'/components/com_eshop/assets/colorbox/colorbox.css');
			$document->addStyleSheet(JUri::base(true).'/components/com_eshop/assets/css/labels.css');
			// Update hits for manufacturer
			EshopHelper::updateHits($manufacturer->id, 'manufacturers');						
			// Set title of the page			
			$siteNamePosition = $app->getCfg('sitename_pagetitles');
			$manufacturerPageTitle = $manufacturer->manufacturer_page_title != '' ? $manufacturer->manufacturer_page_title : $manufacturer->manufacturer_name; 
			if ($siteNamePosition == 0)
			{
				$title = $manufacturerPageTitle;
			}
			elseif($siteNamePosition == 1)
			{
				$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $manufacturerPageTitle);
			}
			else
			{
				$title = JText::sprintf('JPAGETITLE', $manufacturerPageTitle, $app->getCfg('sitename'));
			}
			$document->setTitle($title);						
			//Sort options
			$sortOptions = EshopHelper::getConfigValue('sort_options');
			$sortOptions = explode(',', $sortOptions);
			$sortValues = array (
					'b.product_name-ASC',
					'b.product_name-DESC',
					'a.product_sku-ASC',
					'a.product_sku-DESC',
					'a.product_price-ASC',
					'a.product_price-DESC',
					'a.product_length-ASC',
					'a.product_length-DESC',
					'a.product_width-ASC',
					'a.product_width-DESC',
					'a.product_height-ASC',
					'a.product_height-DESC',
					'a.product_weight-ASC',
					'a.product_weight-DESC',
					'a.product_quantity-ASC',
					'a.product_quantity-DESC',
					'b.product_short_desc-ASC',
					'b.product_short_desc-DESC',
					'b.product_desc-ASC',
					'b.product_desc-DESC'
			);
			$sortTexts = array (
					JText::_('ESHOP_SORTING_PRODUCT_NAME_ASC'),
					JText::_('ESHOP_SORTING_PRODUCT_NAME_DESC'),
					JText::_('ESHOP_SORTING_PRODUCT_SKU_ASC'),
					JText::_('ESHOP_SORTING_PRODUCT_SKU_DESC'),
					JText::_('ESHOP_SORTING_PRODUCT_PRICE_ASC'),
					JText::_('ESHOP_SORTING_PRODUCT_PRICE_DESC'),
					JText::_('ESHOP_SORTING_PRODUCT_LENGTH_ASC'),
					JText::_('ESHOP_SORTING_PRODUCT_LENGTH_DESC'),
					JText::_('ESHOP_SORTING_PRODUCT_WIDTH_ASC'),
					JText::_('ESHOP_SORTING_PRODUCT_WIDTH_DESC'),
					JText::_('ESHOP_SORTING_PRODUCT_HEIGHT_ASC'),
					JText::_('ESHOP_SORTING_PRODUCT_HEIGHT_DESC'),
					JText::_('ESHOP_SORTING_PRODUCT_WEIGHT_ASC'),
					JText::_('ESHOP_SORTING_PRODUCT_WEIGHT_DESC'),
					JText::_('ESHOP_SORTING_PRODUCT_QUANTITY_ASC'),
					JText::_('ESHOP_SORTING_PRODUCT_QUANTITY_DESC'),
					JText::_('ESHOP_SORTING_PRODUCT_SHORT_DESC_ASC'),
					JText::_('ESHOP_SORTING_PRODUCT_SHORT_DESC_DESC'),
					JText::_('ESHOP_SORTING_PRODUCT_DESC_ASC'),
					JText::_('ESHOP_SORTING_PRODUCT_DESC_DESC')
			);
			$options = array();
			$options[] = JHtml::_('select.option', 'a.id-DESC', JText::_('ESHOP_SORTING_DEFAULT'));
			for ($i = 0; $i< count($sortValues); $i++)
			{
				if (in_array($sortValues[$i], $sortOptions))
				{
					$options[] = JHtml::_('select.option', $sortValues[$i], $sortTexts[$i]);
				}
			}
			if (count($options) > 1)
			{
				$this->sort_options = JHtml::_('select.genericlist', $options, 'sort_options', 'class="inputbox input-xlarge" onchange="this.form.submit();" ', 'value', 'text', JRequest::getVar('sort_options',''));
			}
			else
			{
				$this->sort_options = '';
			}
			$app->setUserState('sort_options', $state->sort_options ? $state->sort_options : EshopHelper::getConfigValue('default_sorting'));
			$app->setUserState('from_view', 'manufacturer');
			JFactory::getSession()->set('continue_shopping_url', JUri::getInstance()->toString());			
			$tax = new EshopTax(EshopHelper::getConfig());
			$currency = new EshopCurrency();
			$this->products = $products;
			$this->pagination = $pagination;
			$this->tax = $tax;
			$this->manufacturer = $manufacturer;
			$this->currency = $currency;
			
			$this->actionUrl = JRoute::_(EshopRoute::getManufacturerRoute($manufacturer->id));
			$this->productsPerRow = EshopHelper::getConfigValue('items_per_row', 3);
			
			parent::display($tpl);
		}
	}
}