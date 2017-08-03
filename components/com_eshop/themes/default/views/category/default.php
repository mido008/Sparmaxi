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
defined ( '_JEXEC' ) or die ();
echo $this->loadTemplate('category');
if (count ($this->subCategories) && EshopHelper::getConfigValue('show_sub_categories'))
{
	?>
	<div class="eshop-sub-categories-list"><?php echo EshopHtmlHelper::loadCommonLayout('common/sub_categories.php', array ('subCategories' => $this->subCategories, 'subCategoriesPerRow' => $this->subCategoriesPerRow)); ?></div>
	<?php
}
if (count($this->products))
{
	?>
	<div class="eshop-products-list">
		<?php
		echo EshopHtmlHelper::loadCommonLayout('common/products.php', array (
			'products' => $this->products,
			'pagination' => $this->pagination,
			'sort_options' => $this->sort_options,
			'tax' => $this->tax,
			'currency' => $this->currency,
			'productsPerRow' => $this->productsPerRow,
			'catId' => $this->category->id,
			'actionUrl' => $this->actionUrl,
			'showSortOptions' => true
		));
		?>
	</div>
	<?php
}