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

if (isset($this->warning))
{
?>
	<div class="warning"><?php echo $this->warning; ?></div>
<?php
}
//Display Shop Instroduction
if (EshopHelper::getConfigValue('shop_introduction') != '' && EshopHelper::getConfigValue('introduction_display_on', 'front_page') == 'categories_page')
{
	?>
	<div class="eshop-shop-introduction"><?php echo EshopHelper::getConfigValue('shop_introduction'); ?></div>
	<?php
}
if (count($this->items)) 
{
	if ($this->params->get('show_page_heading'))
	{
	?>
		<h1 class="eshop-categories-heading"><?php echo JText::_('ESHOP_CATEGORIES'); ?></h1>
	<?php
	}
	?>
	<div class="eshop-categories-list"><?php echo EshopHtmlHelper::loadCommonLayout('common/categories.php', array ('categories' => $this->items, 'categoriesPerRow' => $this->categoriesPerRow)); ?></div>
	<?php
	if ($this->pagination->total > $this->pagination->limit) 
	{
	?>
		<div class="pagination">
			<?php echo $this->pagination->getPagesLinks(); ?>
		</div>
	<?php
	}
}