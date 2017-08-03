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
class EShopModelLabel extends EShopModel
{

	public function __construct($config)
	{
		$config['translatable'] = true;
		$config['translatable_fields'] = array('label_name');
		
		parent::__construct($config);
	}

	function store(&$data)
	{
		$imagePath = JPATH_ROOT . '/media/com_eshop/labels/';
		$imageWidth = $data['label_image_width'] > 0 ? $data['label_image_width'] : EshopHelper::getConfigValue('label_image_width');
		if (!$imageWidth)
			$imageWidth = 50;
		$imageHeight = $data['label_image_height'] > 0 ? $data['label_image_height'] : EshopHelper::getConfigValue('label_image_height');
		if (!$imageHeight)
			$imageHeight = 50;
		
		if (JRequest::getInt('remove_image') && $data['id'])
		{
			//Remove image first
			$row = new EShopTable('#__eshop_labels', 'id', $this->getDbo());
			$row->load($data['id']);
			
			if (JFile::exists($imagePath . $row->label_image))
				JFile::delete($imagePath . $row->label_image);
			if (JFile::exists($imagePath . 'resized/' . JFile::stripExt($row->label_image).'-'.$imageWidth.'x'.$imageHeight.'.'.JFile::getExt($row->label_image)))
				JFile::delete($imagePath . 'resized/' . JFile::stripExt($row->label_image).'-'.$imageWidth.'x'.$imageHeight.'.'.JFile::getExt($row->label_image));
			$data['label_image'] = '';
		}
		
		$labelImage = $_FILES['label_image'];
		if ($labelImage['name'])
		{
			$checkFileUpload = EshopFile::checkFileUpload($labelImage);
			if (is_array($checkFileUpload))
			{
				$mainframe = JFactory::getApplication();
				$mainframe->enqueueMessage(sprintf(JText::_('ESHOP_UPLOAD_IMAGE_ERROR'), implode(' / ', $checkFileUpload)), 'error');
				$mainframe->redirect('index.php?option=com_eshop&task=label.edit&cid[]=' . $data['id']);
			}
			else
			{
				if (is_uploaded_file($labelImage['tmp_name']) && file_exists($labelImage['tmp_name']))
				{
					if ($data['id'])
					{
						// Delete the old image
						$row = new EShopTable('#__eshop_labels', 'id', $this->getDbo());
						$row->load($data['id']);
							
						if (JFile::exists($imagePath . $row->label_image))
							JFile::delete($imagePath . $row->label_image);
						if (JFile::exists($imagePath . 'resized/' . JFile::stripExt($row->label_image).'-'.$imageWidth.'x'.$imageHeight.'.'.JFile::getExt($row->label_image)))
							JFile::delete($imagePath . 'resized/' . JFile::stripExt($row->label_image).'-'.$imageWidth.'x'.$imageHeight.'.'.JFile::getExt($row->label_image));
					}
					if (JFile::exists($imagePath . $labelImage['name']))
					{
						$imageFileName = uniqid('image_') . '_' . $labelImage['name'];
					}
					else
					{
						$imageFileName = $labelImage['name'];
					}
					JFile::upload($labelImage['tmp_name'], $imagePath . $imageFileName);
					$data['label_image'] = $imageFileName;
				}	
			}
		}
		//Delete label elements first
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		if ($data['id'])
		{
			$query->delete('#__eshop_labelelements')
				->where('label_id = ' . intval($data['id']));
			$db->setQuery($query);
			$db->query();
		}
		parent::store($data);
		$row = new EShopTable('#__eshop_labels', 'id', $this->getDbo());
		$row->load($data['id']);
		if ($row->label_image && !JFile::exists($imagePath . 'resized/' . JFile::stripExt($row->label_image).'-'.$imageWidth.'x'.$imageHeight.'.'.JFile::getExt($row->label_image)))
		{
			$imageSizeFunction = EshopHelper::getConfigValue('image_size_function', 'resizeImage');
			call_user_func_array(array('EshopHelper', $imageSizeFunction), array($row->label_image, JPATH_ROOT . '/media/com_eshop/labels/', $imageWidth, $imageHeight));
		}
		//Label for products
		if (isset($data['product_id']))
		{
			$productIds = $data['product_id'];
			if (count($productIds))
			{
				$query->clear();
				$query->insert('#__eshop_labelelements')
					->columns('label_id, element_id, element_type');
				$labelId = $data['id'];
				for ($i = 0; $i < count($productIds); $i++)
				{
					$productId = $productIds[$i];
					$query->values("$labelId, $productId, 'product'");
				}
				$db->setQuery($query);
				$db->query();
			}
		}
		//Label for manufacturers
		if (isset($data['manufacturer_id']))
		{
			$manufacturerIds = $data['manufacturer_id'];
			if (count($manufacturerIds))
			{
				$query->clear();
				$query->insert('#__eshop_labelelements')
					->columns('label_id, element_id, element_type');
				$labelId = $data['id'];
				for ($i = 0; $i < count($manufacturerIds); $i++)
				{
					$manufacturerId = $manufacturerIds[$i];
					$query->values("$labelId, $manufacturerId, 'manufacturer'");
				}
				$db->setQuery($query);
				$db->query();
			}
		}
		//Label for manufacturers
		if (isset($data['category_id']))
		{
			$categoryIds = $data['category_id'];
			if (count($categoryIds))
			{
				$query->clear();
				$query->insert('#__eshop_labelelements')
					->columns('label_id, element_id, element_type');
				$labelId = $data['id'];
				for ($i = 0; $i < count($categoryIds); $i++)
				{
					$categoryId = $categoryIds[$i];
					$query->values("$labelId, $categoryId, 'category'");
				}
				$db->setQuery($query);
				$db->query();
			}
		}
		return true;
	}
	
	/**
	 * Method to remove labels
	 *
	 * @access	public
	 * @return boolean True on success
	 * @since	1.5
	 */
	public function delete($cid = array())
	{
		//Remove label elements
		if (count($cid))
		{
			$db = $this->getDbo();
			$query = $db->getQuery(true);
			$query->delete('#__eshop_labelelements')
				->where('label_id IN (' . implode(',', $cid) . ')');
			$db->setQuery($query);
			$db->query();
		}
		parent::delete($cid);
	}
}