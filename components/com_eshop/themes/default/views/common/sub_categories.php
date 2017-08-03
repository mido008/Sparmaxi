<?php
/**
 * @version		1.4.1
 * @package		Joomla
 * @subpackage	EShop
 * @author  	Giang Dinh Truong
 * @copyright	Copyright (C) 2012 Ossolution Team
 * @license		GNU/GPL, see LICENSE.php
 */
$span = intval(12 / $subCategoriesPerRow);
?>
<div class="row-fluid">
	<?php
	if (EshopHelper::getConfigValue('sub_categories_layout') == 'list_with_only_link')
	{
		?>
		<h4><?php echo JText::_('ESHOP_REFINE_SEARCH'); ?></h4>
	    <ul>
			<?php 
			foreach ($subCategories as $subCategory)
			{
				?>
				<li>
					<h5>
						<a href="<?php echo JRoute::_(EshopRoute::getCategoryRoute($subCategory->id)); ?>">
							<?php echo $subCategory->category_name; ?>
						</a>
					</h5>
				</li>
				<?php
			}
			?> 
	    </ul>
		<?php
	}
	else 
	{
		$count = 0;
		foreach ($subCategories as $subCategory) 
		{
			$subCategoryUrl = JRoute::_(EshopRoute::getCategoryRoute($subCategory->id));
			?>
			<div class="span<?php echo $span; ?>">
				<div class="eshop-category-wrap">
		        	<div class="image">
					<a href="<?php echo $subCategoryUrl; ?>" title="<?php echo $subCategory->category_page_title != '' ? $subCategory->category_page_title : $subCategory->category_name; ?>">
						<img src="<?php echo $subCategory->image; ?>" alt="<?php echo $subCategory->category_page_title != '' ? $subCategory->category_page_title : $subCategory->category_name; ?>" />	            
					</a>
		            </div>
					<div class="eshop-info-block">
						<h5>
							<a href="<?php echo $subCategoryUrl; ?>" title="<?php echo $subCategory->category_page_title != '' ? $subCategory->category_page_title : $subCategory->category_name; ?>">
								<?php echo $subCategory->category_name; ?>
							</a>
						</h5>
					</div>
				</div>	
			</div>
			<?php
			$count++;
			if ($count % $subCategoriesPerRow == 0 && $count < count($subCategories))
			{
			?>
				</div><div class="row-fluid">
			<?php
			}
		}
	}
	?>
</div>
<hr />