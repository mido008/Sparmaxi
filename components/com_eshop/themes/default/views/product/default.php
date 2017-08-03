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
$uri = JUri::getInstance();
?>
<script src="<?php echo JUri::base(true); ?>/components/com_eshop/assets/colorbox/jquery.colorbox.js" type="text/javascript"></script>
<script src="<?php echo JUri::base(true); ?>/components/com_eshop/assets/js/slick.js" type="text/javascript"></script>
<script src="<?php echo JUri::base(true); ?>/components/com_eshop/assets/js/eshop-pagination.js" type="text/javascript"></script>
<?php
if (EshopHelper::getConfigValue('view_image') == 'zoom')
{
	?>
	<script src="<?php echo JUri::base(true); ?>/components/com_eshop/assets/js/jquery.jqzoom-core.js" type="text/javascript"></script>
	<script type="text/javascript">
	    Eshop.jQuery(document).ready(function($) {
			$('.product-image-zoom').jqzoom();
            $('.product-options select, .product-options input[type="radio"], .product-options input[type="checkbox"]').change(function(e){if ((this.length || this.checked ) && $('#option-image-'+$(this).val()).length)$('#option-image-'+$(this).val()).click();else $('.image-additional .slick-slide:first-child').children().click();});
		});
	</script>	
	<?php
}
else
{
	?>
	<script type="text/javascript">
		Eshop.jQuery(document).ready(function($) {
			$(".product-image").colorbox({rel:'colorbox'});
	       var mainimage = $('#main-image-area');
	        $('.option-image').each(function(){$(this).children().each(function(){mainimage.append($(this).clone().removeAttr('class').removeAttr('id').removeAttr('href').addClass($(this).attr('id')).hide().click(function(){$('#' + $(this).attr('class')).click();}));});});
	        $('.product-options select, .product-options input[type="radio"], .product-options input[type="checkbox"]').change(function(e){mainimage.children().hide();if ((this.length || this.checked ) && $('.option-image-'+$(this).val()).length)$('.option-image-'+$(this).val()).show();else mainimage.children('a:first-child').show();});
		});
	</script>
	<?php
}
if (EshopHelper::getConfigValue('show_products_nav') && (is_object($this->productsNavigation[0]) || is_object($this->productsNavigation[1])))
{
	?>
	<div class="row-fluid">
		<div class="span6 eshop-pre-nav">
			<?php
			if (is_object($this->productsNavigation[0]))
			{
				?>
				<a class="pull-left" href="<?php echo JRoute::_(EshopRoute::getProductRoute($this->productsNavigation[0]->id, isset($this->productsNavigation[0]->category_id) ? $this->productsNavigation[0]->category_id : EshopHelper::getProductCategory($this->productsNavigation[0]->id))); ?>" title="<?php echo $this->productsNavigation[0]->product_page_title != '' ? $this->productsNavigation[0]->product_page_title : $this->productsNavigation[0]->product_name; ?>">
					<?php echo $this->productsNavigation[0]->product_name; ?>
				</a>
				<?php
			}
			?>
		</div>
		<div class="span6 eshop-next-nav">
			<?php
			if (is_object($this->productsNavigation[1]))
			{
				?>
				<a class="pull-right" href="<?php echo JRoute::_(EshopRoute::getProductRoute($this->productsNavigation[1]->id, isset($this->productsNavigation[1]->category_id) ? $this->productsNavigation[1]->category_id : EshopHelper::getProductCategory($this->productsNavigation[1]->id))); ?>" title="<?php echo $this->productsNavigation[1]->product_page_title != '' ? $this->productsNavigation[1]->product_page_title : $this->productsNavigation[1]->product_name; ?>">
					<?php echo $this->productsNavigation[1]->product_name; ?>
				</a>
				<?php
			}
			?>
		</div>
	</div>
	<?php
}
?>
<!-- Microdata for Rich Snippets -->
<?php
if (EshopHelper::getConfigValue('rich_snippets') == '1')
{
	?>
	<div itemscope itemtype="http://schema.org/Product" style="display: none;">
		<?php
		if ($this->manufacturer->manufacturer_name != '')
		{
			?>
			<span itemprop="brand"><?php echo $this->manufacturer->manufacturer_name; ?></span>
			<?php
		}
		?>
		<span itemprop="name"><?php echo $this->item->product_name; ?></span>
		<?php
		if ($this->item->thumb_image)
		{
			?>
			<img itemprop="image" src="<?php echo EshopHelper::getSiteUrl() . $this->item->thumb_image; ?>" />
			<?php
		}
		if ($this->item->product_short_desc)
		{
			$description = $this->item->product_short_desc; 
		}
		else 
		{
			$description = $this->item->product_desc;
		}
		$description = utf8_substr(strip_tags(html_entity_decode($description, ENT_QUOTES, 'UTF-8')), 0, 100) . '..';
		?>
		<span itemprop="description"><?php echo $description; ?></span>
		Product #: <span itemprop="mpn"><?php echo $this->item->product_sku; ?></span>
		<?php
		if (EshopHelper::getConfigValue('allow_reviews'))
		{
			?>
			<span itemprop="aggregateRating" itemscope itemtype="http://schema.org/AggregateRating">
				<span itemprop="ratingValue"><img src="components/com_eshop/assets/images/stars-<?php echo round(EshopHelper::getProductRating($this->item->id)); ?>.png" /></span> based on <span itemprop="reviewCount"><?php echo count($this->productReviews); ?></span> reviews
			</span>
			<?php
		}
		?>
		<span itemprop="offers" itemscope itemtype="http://schema.org/Offer">
			<?php
			if (EshopHelper::showPrice() && !$this->item->product_call_for_price)
			{
				?>
				Regular price: $<?php echo number_format($this->item->product_price, 2); ?>
				<meta itemprop="priceCurrency" content="<?php echo $this->currency->getCurrencyCode(); ?>" />
				$<span itemprop="price"><?php echo number_format($this->item->product_price, 2); ?></span>
				<?php
			}
			if (EshopHelper::getConfigValue('show_availability'))
			{
				?>
				<span itemprop="availability" href="http://schema.org/InStock"/><?php echo $this->item->availability; ?></span>
				<?php
			}
			?>
		</span>
    </div>
    <?php
	}
?>
<div class="product-info">
	<div class="row-fluid">
		<div class="span12">
			<h1><?php echo $this->item->product_page_heading != '' ? $this->item->product_page_heading : $this->item->product_name; ?></h1>
		</div>
	</div>
	<div class="row-fluid">
		<div class="span4">
			<?php
			if (EshopHelper::getConfigValue('view_image') == 'zoom')
			{
				?>
				<div class="image img-polaroid" id="main-image-area">
					<a href="<?php echo $this->item->popup_image; ?>" class="product-image-zoom" <?php if (count($this->productImages)) echo 'rel="product-thumbnails"'; ?> title="<?php echo $this->item->product_name; ?>">
						<?php
						if (count($this->labels))
						{
							for ($i = 0; $n = count($this->labels), $i < $n; $i++)
							{
								$label = $this->labels[$i];
								if ($label->label_style == 'rotated' && !($label->enable_image && $label->label_image))
								{
									?>
									<div class="cut-rotated">
									<?php
								}
								if ($label->enable_image && $label->label_image)
								{
									$imageWidth = $label->label_image_width > 0 ? $label->label_image_width : EshopHelper::getConfigValue('label_image_width');
									if (!$imageWidth)
										$imageWidth = 50;
									$imageHeight = $label->label_image_height > 0 ? $label->label_image_height : EshopHelper::getConfigValue('label_image_height');
									if (!$imageHeight)
										$imageHeight = 50;
									?>
									<span class="horizontal <?php echo $label->label_position; ?> small-db" style="opacity: <?php echo $label->label_opacity; ?>;<?php echo 'background-image: url(' . $label->label_image . ')'; ?>; background-repeat: no-repeat; width: <?php echo $imageWidth; ?>px; height: <?php echo $imageHeight; ?>px; box-shadow: none;"></span>
									<?php
								}
								else 
								{
									?>
									<span class="<?php echo $label->label_style; ?> <?php echo $label->label_position; ?> small-db" style="background-color: <?php echo '#'.$label->label_background_color; ?>; color: <?php echo '#'.$label->label_foreground_color; ?>; opacity: <?php echo $label->label_opacity; ?>;<?php if ($label->label_bold) echo 'font-weight: bold;'; ?>">
										<?php echo $label->label_name; ?>
									</span>
									<?php
								}
								if ($label->label_style == 'rotated' && !($label->enable_image && $label->label_image))
								{
									?>
									</div>
									<?php
								}
							}
						}
						?>
						<img src="<?php echo $this->item->thumb_image; ?>" title="<?php echo $this->item->product_page_title != '' ? $this->item->product_page_title : $this->item->product_name; ?>" alt="<?php echo $this->item->product_page_title != '' ? $this->item->product_page_title : $this->item->product_name; ?>" />
					</a>
				</div>	
				<?php
				if (count($this->productImages))
				{
					?>
					<div class="image-additional">
						<div>
							<a class="zoomThumbActive" href="javascript:void(0);" rel="{gallery: 'product-thumbnails', smallimage: '<?php echo $this->item->thumb_image; ?>',largeimage: '<?php echo $this->item->popup_image; ?>'}">
								<img src="<?php echo $this->item->small_thumb_image; ?>">
							</a>
						</div>
						<?php
						for ($i = 0; $n = count($this->productImages), $i < $n; $i++)
						{
							?>
							<div>
								<a href="javascript:void(0);" rel="{gallery: 'product-thumbnails', smallimage: '<?php echo $this->productImages[$i]->thumb_image; ?>',largeimage: '<?php echo $this->productImages[$i]->popup_image; ?>'}">
									<img src="<?php echo $this->productImages[$i]->small_thumb_image; ?>">
								</a>
							</div>	
							<?php
						}
						?>
					</div>
					<?php
				}
			}
			else 
			{
				?>
				<div class="image img-polaroid" id="main-image-area">
					<a class="product-image" href="<?php echo $this->item->popup_image; ?>">
						<?php
						if (count($this->labels))
						{
							for ($i = 0; $n = count($this->labels), $i < $n; $i++)
							{
								$label = $this->labels[$i];
								if ($label->label_style == 'rotated' && !($label->enable_image && $label->label_image))
								{
									?>
									<div class="cut-rotated">
									<?php
								}
								if ($label->enable_image && $label->label_image)
								{
									$imageWidth = $label->label_image_width > 0 ? $label->label_image_width : EshopHelper::getConfigValue('label_image_width');
									if (!$imageWidth)
										$imageWidth = 50;
									$imageHeight = $label->label_image_height > 0 ? $label->label_image_height : EshopHelper::getConfigValue('label_image_height');
									if (!$imageHeight)
										$imageHeight = 50;
									?>
									<span class="horizontal <?php echo $label->label_position; ?> small-db" style="opacity: <?php echo $label->label_opacity; ?>;<?php echo 'background-image: url(' . $label->label_image . ')'; ?>; background-repeat: no-repeat; width: <?php echo $imageWidth; ?>px; height: <?php echo $imageHeight; ?>px; box-shadow: none;"></span>
									<?php
								}
								else 
								{
									?>
									<span class="<?php echo $label->label_style; ?> <?php echo $label->label_position; ?> small-db" style="background-color: <?php echo '#'.$label->label_background_color; ?>; color: <?php echo '#'.$label->label_foreground_color; ?>; opacity: <?php echo $label->label_opacity; ?>;<?php if ($label->label_bold) echo 'font-weight: bold;'; ?>">
										<?php echo $label->label_name; ?>
									</span>
									<?php
								}
								if ($label->label_style == 'rotated' && !($label->enable_image && $label->label_image))
								{
									?>
									</div>
									<?php
								}
							}
						}
						?>
						<img src="<?php echo $this->item->thumb_image; ?>" title="<?php echo $this->item->product_page_title != '' ? $this->item->product_page_title : $this->item->product_name; ?>" alt="<?php echo $this->item->product_page_title != '' ? $this->item->product_page_title : $this->item->product_name; ?>" />
					</a>
				</div>
				<?php
				if (count($this->productImages) > 0)
				{
					?>
					<div class="image-additional">
						<?php
						for ($i = 0; $n = count($this->productImages), $i < $n; $i++)
						{
							?>
							<div>
								<a class="product-image" href="<?php echo $this->productImages[$i]->popup_image; ?>">
									<img src="<?php echo $this->productImages[$i]->small_thumb_image; ?>" />
								</a>
							</div>
							<?php
						}
						?>
					</div>
					<?php
				}
			}
			?>
		</div>
		<div class="span8">
        	<div class="row-fluid">
        		<?php
        		if (EshopHelper::getConfigValue('show_manufacturer') || EshopHelper::getConfigValue('show_sku') || EshopHelper::getConfigValue('show_availability'))
				{
        			?>
        			<div>
	                    <div class="product-desc">
                        	<?php
                        	if (EshopHelper::getConfigValue('show_manufacturer'))
							{
                        		?>
                        		<div class="product-manufacturer">
	                        		<strong><?php echo JText::_('ESHOP_BRAND'); ?>:</strong>
	                        		<span><?php echo isset($this->manufacturer->manufacturer_name) ? $this->manufacturer->manufacturer_name : ''; ?></span>
	                        	</div>
	                        	<?php
                        	}
                        	if (EshopHelper::getConfigValue('show_sku'))
							{
                        		?>
                        		<div class="product-sku">
	                        		<strong><?php echo JText::_('ESHOP_PRODUCT_CODE'); ?>:</strong>
	                        		<span><?php echo $this->item->product_sku; ?></span>
	                        	</div>	
                        		<?php
                        	}
                        	if (EshopHelper::getConfigValue('show_availability'))
							{
                        		?>
                        		<div class="product-availability">
	                        		<strong><?php echo JText::_('ESHOP_AVAILABILITY'); ?>:</strong>
	                        		<span><?php echo $this->item->availability; ?></span>
	                        	</div>
                        		<?php
                        	}
                        	if (EshopHelper::getConfigValue('show_product_weight'))
                        	{
                        		?>
								<div class="product-weight">
									<strong><?php echo JText::_('ESHOP_PRODUCT_WEIGHT'); ?>:</strong>
									<span><?php echo number_format($this->item->product_weight, 2).EshopHelper::getWeightUnit($this->item->product_weight_id, JFactory::getLanguage()->getTag()); ?></span>
								</div>
								<?php
							}
							if (EshopHelper::getConfigValue('show_product_dimensions'))
							{
								?>
								<div class="product-dimensions">
									<strong><?php echo JText::_('ESHOP_PRODUCT_DIMENSIONS'); ?>:</strong>
									<span><?php echo number_format($this->item->product_length, 2).EshopHelper::getLengthUnit($this->item->product_length_id, JFactory::getLanguage()->getTag()) . ' x ' . number_format($this->item->product_width, 2).EshopHelper::getLengthUnit($this->item->product_length_id, JFactory::getLanguage()->getTag()) . ' x ' . number_format($this->item->product_height, 2).EshopHelper::getLengthUnit($this->item->product_length_id, JFactory::getLanguage()->getTag()); ?></span>
								</div>
								<?php
							}
							if (EshopHelper::getConfigValue('show_product_tags') && count($this->productTags))
							{
								?>
								<div class="product-tags">
									<strong><?php echo JText::_('ESHOP_PRODUCT_TAGS'); ?>:</strong>
									<span>
										<?php
										for ($i = 0; $n = count($this->productTags), $i < $n; $i++)
										{
											$tagName = trim($this->productTags[$i]->tag_name);
											$searchTagLink = JRoute::_('index.php?option=com_eshop&view=search&keyword=' . $tagName);
											?>
												<a href="<?php echo $searchTagLink; ?>" title="<?php echo $tagName; ?>"><?php echo $tagName; ?></a>
											<?php
											if ($i < ($n - 1))
												echo ", ";
										}
										?>
									</span>
								</div>
								<?php
							}
                        	?>
	                    </div>
	                </div>	
        			<?php
        		}
                if (EshopHelper::showPrice() && !$this->item->product_call_for_price)
				{
					?>
	                <div>
	                    <div class="product-price" id="product-price">
	                        <h2>
	                            <strong>
	                                <?php echo JText::_('ESHOP_PRICE'); ?>:
	                                <?php
	                                $productPriceArray = EshopHelper::getProductPriceArray($this->item->id, $this->item->product_price);
	                                if ($productPriceArray['salePrice'])
	                                {
	                                    ?>
	                                    <span class="eshop-base-price"><?php echo $this->currency->format($this->tax->calculate($productPriceArray['basePrice'], $this->item->product_taxclass_id, EshopHelper::getConfigValue('tax'))); ?></span>&nbsp;
	                                    <span class="eshop-sale-price"><?php echo $this->currency->format($this->tax->calculate($productPriceArray['salePrice'], $this->item->product_taxclass_id, EshopHelper::getConfigValue('tax'))); ?></span>
	                                    <?php
	                                }
	                                else
	                                {
	                                    ?>
	                                    <span class="price"><?php echo $this->currency->format($this->tax->calculate($productPriceArray['basePrice'], $this->item->product_taxclass_id, EshopHelper::getConfigValue('tax'))); ?></span>
	                                    <?php
	                                }
	                                ?>
	                            </strong><br />
	                            <?php
	                            if (EshopHelper::getConfigValue('tax') && EshopHelper::getConfigValue('display_ex_tax'))
								{
	                            	?>
	                            	<small>
		                                <?php echo JText::_('ESHOP_EX_TAX'); ?>:
		                                <?php
		                                if ($productPriceArray['salePrice'])
		                                {
											echo $this->currency->format($productPriceArray['salePrice']);
		                                }
		                                else
		                                {
											echo $this->currency->format($productPriceArray['basePrice']);
		                                }
		                                ?>
		                            </small>
	                            	<?php
	                            }
	                            ?>
	                        </h2>
	                    </div>
	                </div>
	                <?php
	                if (count($this->discountPrices))
	                {
	                    ?>
	                    <div>
	                        <div class="product-discount-price">
	                            <?php
	                            for ($i = 0; $n = count($this->discountPrices), $i < $n; $i++)
	                            {
	                                $discountPrices = $this->discountPrices[$i];
	                                echo $discountPrices->quantity.' '.JText::_('ESHOP_OR_MORE').' '.$this->currency->format($this->tax->calculate($discountPrices->price, $this->item->product_taxclass_id, EshopHelper::getConfigValue('tax'))).'<br />';
	                            }
	                            ?>
	                        </div>
	                    </div>
	                    <?php
	                }
				}
				if ($this->item->product_call_for_price)
				{
					?>
					<div>
						<div class="product-price">
							<?php echo JText::_('ESHOP_CALL_FOR_PRICE'); ?>: <?php echo EshopHelper::getConfigValue('telephone'); ?>
						</div>
					</div>
					<?php
				}
                if (count($this->productOptions))
                {
                    ?>
                    <div>
                        <div class="product-options">
                            <h2>
                                <?php echo JText::_('ESHOP_AVAILABLE_OPTIONS'); ?>
                            </h2>
                            <?php
                            for ($i = 0; $n = count($this->productOptions), $i < $n; $i++)
                            {
                                $option = $this->productOptions[$i];
                                
                                if (!EshopHelper::isCartMode($this->item) && !EshopHelper::isQuoteMode($this->item) && ($option->option_type == 'Text' || $option->option_type == 'Textarea' || $option->option_type == 'File' || $option->option_type == 'Date' || $option->option_type == 'Datetime'))
								{
                                	continue;
                                }
                                ?>
                                <div id="option-<?php echo $option->product_option_id; ?>">
									<div>
										<?php
		                                if ($option->required && (EshopHelper::isCartMode($this->item) || EshopHelper::isQuoteMode($this->item)))
		                                {
		                                    ?>
		                                    <span class="required">*</span>
		                                    <?php
		                                }
		                                ?>
		                                <strong><?php echo $option->option_name; ?>:</strong>
		                                <?php
		                                if ($option->option_type == 'File')
										{
		                                	?>
		                                	<span id="file-<?php echo $option->product_option_id; ?>"></span>
		                                	<?php
		                                }
		                                if ($option->option_desc != '')
										{
		                                	?>
		                                	<p><?php echo $option->option_desc; ?></p>
		                                	<?php
		                                }
		                                else 
		                                {
		                                	?>
		                                	<br/>
		                                	<?php
		                                }
                                        
										echo EshopOption::renderOption($this->item->id, $option->id, $option->option_type, $this->item->product_taxclass_id);
		                                ?>
		                                
									</div>
                                </div>
                                <?php
                            }
                            ?>
                        </div>
                    </div>
                    <?php
                }
                ?>
                <div class="row-fluid">
                	<div class="product-cart clearfix">
	                	<?php
						if (EshopHelper::isCartMode($this->item) || EshopHelper::isQuoteMode($this->item))
						{
							?>
	                        <div class="span6 no_margin_left">
	                        	<input type="hidden" name="id" value="<?php echo $this->item->id; ?>" />
	                        	<?php
	                        	if (EshopHelper::getConfigValue('show_quantity_box_in_product_page'))
								{
	                        		?>
	                        		<div class="input-append input-prepend">
										<label class="btn"><?php echo JText::_('ESHOP_QTY'); ?>:</label>
										<span class="eshop-quantity">
											<a class="btn btn-default button-minus spin-down" id="<?php echo $this->item->id; ?>" data="down">-</a>
											<input type="text" class="eshop-quantity-value" id="quantity_<?php echo $this->item->id; ?>" name="quantity" value="1" />
											<a class="btn btn-default button-plus spin-up" id="<?php echo $this->item->id; ?>" data="up">+</a>
										</span>
									</div>
	                        		<?php
	                        	}
								if (EshopHelper::isCartMode($this->item))
								{
									?>
									<button id="add-to-cart" class="btn btn-primary" type="button"><?php echo JText::_('ESHOP_ADD_TO_CART'); ?></button>
									<?php
								}
								if (EshopHelper::isQuoteMode($this->item))
								{
									?>
									<button id="add-to-quote" class="btn btn-primary" type="button"><?php echo JText::_('ESHOP_ADD_TO_QUOTE'); ?></button>
									<?php
								}
								?>
							</div>
	                        <?php
						}
						if (EshopHelper::getConfigValue('allow_wishlist') || EshopHelper::getConfigValue('allow_compare') || EshopHelper::getConfigValue('allow_ask_question'))
						{
							?>
							<div class="span6">
								<?php
								if (EshopHelper::getConfigValue('allow_wishlist'))
								{
									?>
									<p><a style="cursor: pointer;" onclick="addToWishList(<?php echo $this->item->id; ?>, '<?php echo EshopHelper::getSiteUrl(); ?>', '<?php echo EshopHelper::getAttachedLangLink(); ?>')"><?php echo JText::_('ESHOP_ADD_TO_WISH_LIST'); ?></a></p>
									<?php
								}
								if (EshopHelper::getConfigValue('allow_compare'))
								{
									?>
									<p><a style="cursor: pointer;" onclick="addToCompare(<?php echo $this->item->id; ?>, '<?php echo EshopHelper::getSiteUrl(); ?>', '<?php echo EshopHelper::getAttachedLangLink(); ?>')"><?php echo JText::_('ESHOP_ADD_TO_COMPARE'); ?></a></p>
									<?php
								}
								if (EshopHelper::getConfigValue('allow_ask_question'))
								{
									?>
									<p><a style="cursor: pointer;" onclick="askQuestion(<?php echo $this->item->id; ?>, '<?php echo EshopHelper::getSiteUrl(); ?>', '<?php echo EshopHelper::getAttachedLangLink(); ?>')"><?php echo JText::_('ESHOP_ASK_QUESTION'); ?></a></p>
									<?php
								}
								?>
							</div>
							<?php
						}
	                	?>
                	</div>
                </div>
                <?php
                if (EshopHelper::getConfigValue('allow_reviews'))
				{
                	?>
                	<div>
	                    <div class="product-review">
	                        <p>
	                            <img src="components/com_eshop/assets/images/stars-<?php echo round(EshopHelper::getProductRating($this->item->id)); ?>.png" />
	                            <a onclick="activeReviewsTab();" style="cursor: pointer;"><?php echo count($this->productReviews).' '.JText::_('ESHOP_REVIEWS'); ?></a> | <a onclick="activeReviewsTab();" style="cursor: pointer;"><?php echo JText::_('ESHOP_WRITE_A_REVIEW'); ?></a>
	                        </p>
	                    </div>
	                </div>	
                	<?php
                }
                if (EshopHelper::getConfigValue('social_enable'))
				{
                	?>
                	<div>
						<div class="product-share">
							<div class="ps_area clearfix">
								<?php
								if (EshopHelper::getConfigValue('show_facebook_button'))
								{
									?>
									<div class="ps_facebook_like">
										<div class="fb-like" data-send="true" data-width="<?php echo EshopHelper::getConfigValue('button_width', 450); ?>" data-show-faces="<?php echo EshopHelper::getConfigValue('show_faces', 1); ?>" vdata-font="<?php echo EshopHelper::getConfigValue('button_font', 'arial'); ?>" data-colorscheme="<?php echo EshopHelper::getConfigValue('button_theme', 'light'); ?>" layout="<?php echo EshopHelper::getConfigValue('button_layout', 'button_count'); ?>"></div>
									</div>
									<?php
								}
								if (EshopHelper::getConfigValue('show_twitter_button'))
								{
									?>
									<div class="ps_twitter">
										<a href="https://twitter.com/share" class="twitter-share-button" data-url="<?php echo $uri->toString(); ?>" tw:via="ontwiik" data-lang="en" data-related="anywhereTheJavascriptAPI" data-count="horizontal">Tweet</a>
									</div>
									<?php
								}
								if (EshopHelper::getConfigValue('show_pinit_button'))
								{
									?>
									<div class="ps_pinit">
										<a href="http://pinterest.com/pin/create/button/?url=<?php echo urlencode($uri->toString()); ?>&media=<?php echo urlencode(EshopHelper::getSiteUrl().$this->item->thumb_image); ?>&description=<?php echo $this->item->product_name; ?>" count-layout="horizontal" class="pin-it-button">Pin It</a>
									</div>
									<?php
								}
								if (EshopHelper::getConfigValue('show_linkedin_button'))
								{
									?>
									<div class="ps_linkedin">
										<?php
										if (EshopHelper::getConfigValue('linkedin_layout', 'right') == 'no-count')
										{
											?>
											<script type="IN/Share"></script>
											<?php
										}
										else 
										{
											?>
											<script type="IN/Share" data-counter="<?php echo EshopHelper::getConfigValue('linkedin_layout', 'right'); ?>"></script>
											<?php
										}
										?>
									</div>
									<?php
								}
								if (EshopHelper::getConfigValue('show_google_button'))
								{
									?>
									<div class="ps_google">
										<div class="g-plusone"></div>
									</div>
									<?php
								}
								?>
							</div>
						</div>
					</div>
                	<?php
                }
                ?>
           	</div>
		</div>
	</div>
	<div class="row-fluid">
		<div class="span12">
			<div class="tabbable">
				<ul class="nav nav-tabs" id="productTab">
					<li class="active"><a href="#description" data-toggle="tab"><?php echo JText::_('ESHOP_DESCRIPTION'); ?></a></li>
					<?php
					if ($this->item->tab1_title != '' && $this->item->tab1_content != '')
					{
						?>
						<li><a href="#tab1-content" data-toggle="tab"><?php echo $this->item->tab1_title; ?></a></li>
						<?php
					}
					if ($this->item->tab2_title != '' && $this->item->tab2_content != '')
					{
						?>
						<li><a href="#tab2-content" data-toggle="tab"><?php echo $this->item->tab2_title; ?></a></li>
						<?php
					}
					if ($this->item->tab3_title != '' && $this->item->tab3_content != '')
					{
						?>
						<li><a href="#tab3-content" data-toggle="tab"><?php echo $this->item->tab3_title; ?></a></li>
						<?php
					}
					if ($this->item->tab4_title != '' && $this->item->tab4_content != '')
					{
						?>
						<li><a href="#tab4-content" data-toggle="tab"><?php echo $this->item->tab4_title; ?></a></li>
						<?php
					}
					if ($this->item->tab5_title != '' && $this->item->tab5_content != '')
					{
						?>
						<li><a href="#tab5-content" data-toggle="tab"><?php echo $this->item->tab5_title; ?></a></li>
						<?php
					}
					if (EshopHelper::getConfigValue('show_specification') && $this->hasSpecification)
					{
						?>
						<li><a href="#specification" data-toggle="tab"><?php echo JText::_('ESHOP_SPECIFICATION'); ?></a></li>
						<?php
					}
					if (EshopHelper::getConfigValue('allow_reviews'))
					{
						?>
						<li><a href="#reviews" data-toggle="tab"><?php echo JText::_('ESHOP_REVIEWS') . ' (' . count($this->productReviews) . ')'; ?></a></li>
						<?php
					}
					if (EshopHelper::getConfigValue('show_related_products') && count($this->productRelations))
					{
						?>
						<li><a href="#related-products" data-toggle="tab"><?php echo JText::_('ESHOP_RELATED_PRODUCTS'); ?></a></li>	
						<?php
					}
					?>
				</ul>
				<div class="tab-content">
					<div class="tab-pane active" id="description">
						<p><?php echo $this->item->product_desc; ?></p>
					</div>
					<?php
					if ($this->item->tab1_title != '' && $this->item->tab1_content != '')
					{
						?>
						<div class="tab-pane" id="tab1-content">
							<?php echo $this->item->tab1_content; ?>
						</div>
						<?php
					}
					if ($this->item->tab2_title != '' && $this->item->tab2_content != '')
					{
						?>
						<div class="tab-pane" id="tab2-content">
							<?php echo $this->item->tab2_content; ?>
						</div>
						<?php
					}
					if ($this->item->tab3_title != '' && $this->item->tab3_content != '')
					{
						?>
						<div class="tab-pane" id="tab3-content">
							<?php echo $this->item->tab3_content; ?>
						</div>
						<?php
					}
					if ($this->item->tab4_title != '' && $this->item->tab4_content != '')
					{
						?>
						<div class="tab-pane" id="tab4-content">
							<?php echo $this->item->tab4_content; ?>
						</div>
						<?php
					}
					if ($this->item->tab5_title != '' && $this->item->tab5_content != '')
					{
						?>
						<div class="tab-pane" id="tab5-content">
							<?php echo $this->item->tab5_content; ?>
						</div>
						<?php
					}
					if (EshopHelper::getConfigValue('show_specification') && $this->hasSpecification)
					{
						?>
						<div class="tab-pane" id="specification">
							<table class="table table-bordered">
								<?php
								for ($i = 0; $n = count($this->attributeGroups), $i < $n; $i++)
								{
									if (count($this->productAttributes[$i]))
									{
										?>
										<thead>
											<tr>
												<th colspan="2"><?php echo $this->attributeGroups[$i]->attributegroup_name; ?></th>
											</tr>
										</thead>
										<tbody>
											<?php
											for ($j = 0; $m = count($this->productAttributes[$i]), $j < $m; $j++)
											{
												?>
												<tr>
													<td width="30%"><?php echo $this->productAttributes[$i][$j]->attribute_name; ?></td>
													<td width="70%"><?php echo $this->productAttributes[$i][$j]->value; ?></td>
												</tr>
												<?php
											}
											?>
										</tbody>
										<?php
									}
									?>
									<?php
								}
								?>
							</table>
						</div>
					<?php
					}
					if (EshopHelper::getConfigValue('allow_reviews'))
					{
						?>
						<div class="tab-pane" id="reviews">
							<?php
							if (count($this->productReviews) > 5)
							{
								?>
								<div class="span12 pagination pagination-toolbar" style="text-align: right; margin-top: 20px;"> 
									<ul class="review-pagination-list"></ul>
								</div>
					       	 	<?php
							}
				       	 	?>
							<div id="wrap-review">
								<?php
								if (count($this->productReviews))
								{
									foreach ($this->productReviews as $review)
									{
										?>
										<div class="review-list">
											<div class="author"><b><?php echo $review->author; ?></b> <?php echo JText::_('ESHOP_REVIEW_ON'); ?> <?php echo JHtml::date($review->created_date, EshopHelper::getConfigValue('date_format', 'm-d-Y') . ' h:i A'); ?></div>
											<div class="rating"><img src="components/com_eshop/assets/images/stars-<?php echo $review->rating . '.png'; ?>" alt="" /></div>
											<div class="text"><?php echo nl2br($review->review); ?></div>
										</div>
										<?php
									}
								}
								else
								{
									?>
									<div class="no-content"><?php echo JText::_('ESHOP_NO_PRODUCT_REVIEWS'); ?></div>
									<?php
								}
								?>
							</div>
							<div class="clearfix"></div>
							<div class="row-fluid form-horizontal" id="review-form">
								<legend id="review-title"><?php echo JText::_('ESHOP_WRITE_A_REVIEW'); ?></legend>
								<div class="control-group">
									<label class="control-label" for="author"><span class="required">*</span><?php echo JText::_('ESHOP_YOUR_NAME'); ?>:</label>
									<div class="controls docs-input-sizes">
										<input type="text" class="input-large" name="author" id="author" value="" />
									</div>
								</div>
								<div class="control-group">	
									<label class="control-label" for="author"><span class="required">*</span><?php echo JText::_('ESHOP_YOUR_REVIEW'); ?>:</label>
									<div class="controls docs-input-sizes">
										<textarea rows="5" cols="40" name="review"></textarea>
									</div>
								</div>
								<div class="control-group">	
									<label class="control-label" for="author"><span class="required">*</span><?php echo JText::_('ESHOP_RATING'); ?>:</label>
									<div class="controls docs-input-sizes">
										<?php echo $this->ratingHtml; ?>
									</div>
								</div>	
								<?php
								if ($this->showCaptcha)
								{
									?>
									<div class="control-group">
										<label class="control-label" for="recaptcha_response_field">
											<?php echo JText::_('ESHOP_CAPTCHA'); ?><span class="required">*</span>
										</label>
										<div class="controls docs-input-sizes">
											<?php echo $this->captcha; ?>
										</div>
									</div>
									<?php
								}
								?>
								<input type="button" class="btn btn-primary pull-left" id="button-review" value="<?php echo JText::_('ESHOP_SUBMIT'); ?>" />
								<input type="hidden" name="product_id" value="<?php echo $this->item->id; ?>" />
							</div>
							<?php
							if (EshopHelper::getConfigValue('show_facebook_comment'))
							{
								?>
								<div class="row-fluild">
									<legend id="review-title"><?php echo JText::_('ESHOP_FACEBOOK_COMMENT'); ?></legend>
									<div class="fb-comments" data-num-posts="<?php echo EshopHelper::getConfigValue('num_posts', 10); ?>" data-width="<?php echo EshopHelper::getConfigValue('comment_width', 400); ?>" data-href="<?php echo $uri->toString(); ?>"></div>
								</div>	
								<?php
							}
							?>
						</div>
						<?php
					}
					if (EshopHelper::getConfigValue('show_related_products') && count($this->productRelations))
					{
						?>
						<div class="tab-pane" id="related-products">
							<div class="related_products row-fluid">
								<?php
								for ($i = 0; $n = count($this->productRelations), $i < $n; $i++)
								{
									$productRelation = $this->productRelations[$i];
									?>
									<div class="span3">
										<div class="image img-polaroid">
					            			<a href="<?php echo JRoute::_(EshopRoute::getProductRoute($productRelation->id, EshopHelper::getProductCategory($productRelation->id))); ?>">
					            				<img src="<?php echo $productRelation->thumb_image; ?>" />
						            			</a>
                                          	</div>
                                            <div class="name">
                                                <a href="<?php echo JRoute::_(EshopRoute::getProductRoute($productRelation->id, EshopHelper::getProductCategory($productRelation->id))); ?>">
                                                    <h5><?php echo $productRelation->product_name; ?></h5>
                                                </a>
                                                <?php
                                                if (EshopHelper::showPrice() && !$productRelation->product_call_for_price)
                                                {
                                                    echo JText::_('ESHOP_PRICE'); ?>:
                                                    <?php
                                                    $productRelationPriceArray = EshopHelper::getProductPriceArray($productRelation->id, $productRelation->product_price);
                                                    if ($productRelationPriceArray['salePrice'])
                                                    {
                                                        ?>
                                                        <span class="eshop-base-price"><?php echo $this->currency->format($this->tax->calculate($productRelationPriceArray['basePrice'], $productRelation->product_taxclass_id, EshopHelper::getConfigValue('tax'))); ?></span>&nbsp;
                                                        <span class="eshop-sale-price"><?php echo $this->currency->format($this->tax->calculate($productRelationPriceArray['salePrice'], $productRelation->product_taxclass_id, EshopHelper::getConfigValue('tax'))); ?></span>
                                                        <?php
                                                    }
                                                    else
                                                    {
                                                        ?>
                                                        <span class="price"><?php echo $this->currency->format($this->tax->calculate($productRelationPriceArray['basePrice'], $productRelation->product_taxclass_id, EshopHelper::getConfigValue('tax'))); ?></span>
                                                        <?php
                                                    }
                                                }
                                                ?>
                                            </div>
						        		</div>
										<?php
									if ($i > 0 && ($i + 1) % 4 == 0)
									{
										?>
										</div><div class="related_products row-fluid">
										<?php
									}
								}
								?>
							</div>
						</div>
					<?php
					}
					?>
				</div>
			</div>
		</div>
	</div>
</div>
<input type="hidden" name="review-tab" id="review-tab" value="0" />
<script type="text/javascript">
	// Add to cart button
	Eshop.jQuery(function($){

		$("ul#productTab li a").on('shown.bs.tab', function (e) {
			var isTab = $(this).attr('href');
			var reviewTab = $('#review-tab').val();
			if(isTab == '#reviews' && reviewTab == 0)
			{
				$('#review-tab').val(1);
				loadReviewPagination();
			}
		});
		loadReviewPagination = (function(){
			 $(".review-pagination-list").eshopPagination({
				 containerID: "wrap-review",
				 perPage: 5,
			 });
		})

		$('#add-to-cart').bind('click', function() {
			$.ajax({
				type: 'POST',
				url: '<?php echo EshopHelper::getSiteUrl(); ?>index.php?option=com_eshop&task=cart.add<?php echo EshopHelper::getAttachedLangLink(); ?>',
				data: $('.product-info input[type=\'text\'], .product-info input[type=\'hidden\'], .product-info input[type=\'radio\']:checked, .product-info input[type=\'checkbox\']:checked, .product-info select, .product-info textarea'),
				dataType: 'json',
				beforeSend: function() {
					$('#add-to-cart').attr('disabled', true);
					$('#add-to-cart').after('<span class="wait">&nbsp;<img src="components/com_eshop/assets/images/loading.gif" alt="" /></span>');
				},
				complete: function() {
					$('#add-to-cart').attr('disabled', false);
					$('.wait').remove();
				},
				success: function(json) {
					$('.error').remove();
					if (json['error']) {
						if (json['error']['option']) {
							for (i in json['error']['option']) {
								$('#option-' + i).after('<span class="error">' + json['error']['option'][i] + '</span>');
							}
						}
					}
					if (json['success']) {
						$.ajax({
							url: '<?php echo EshopHelper::getSiteUrl(); ?>index.php?option=com_eshop&view=cart&layout=popout&format=raw<?php echo EshopHelper::getAttachedLangLink(); ?>',
							dataType: 'html',
							success: function(html) {
								$.colorbox({
									overlayClose: true,
									opacity: 0.5,
									href: false,
									html: html
								});
								$.ajax({
									url: '<?php echo EshopHelper::getSiteUrl(); ?>index.php?option=com_eshop&view=cart&layout=mini&format=raw<?php echo EshopHelper::getAttachedLangLink(); ?>',
									dataType: 'html',
									success: function(html) {
										$('#eshop-cart').html(html);
										$('.eshop-content').hide();
									},
									error: function(xhr, ajaxOptions, thrownError) {
										alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
									}
								});
							},
							error: function(xhr, ajaxOptions, thrownError) {
								alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
							}
						});
					}
			  	},
			  	error: function(xhr, ajaxOptions, thrownError) {
					alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
				}
			});
		});
		$('#add-to-quote').bind('click', function() {
			$.ajax({
				type: 'POST',
				url: '<?php echo EshopHelper::getSiteUrl(); ?>index.php?option=com_eshop&task=quote.add<?php echo EshopHelper::getAttachedLangLink(); ?>',
				data: $('.product-info input[type=\'text\'], .product-info input[type=\'hidden\'], .product-info input[type=\'radio\']:checked, .product-info input[type=\'checkbox\']:checked, .product-info select, .product-info textarea'),
				dataType: 'json',
				beforeSend: function() {
					$('#add-to-quote').attr('disabled', true);
					$('#add-to-quote').after('<span class="wait">&nbsp;<img src="components/com_eshop/assets/images/loading.gif" alt="" /></span>');
				},
				complete: function() {
					$('#add-to-quote').attr('disabled', false);
					$('.wait').remove();
				},
				success: function(json) {
					$('.error').remove();
					if (json['error']) {
						if (json['error']['option']) {
							for (i in json['error']['option']) {
								$('#option-' + i).after('<span class="error">' + json['error']['option'][i] + '</span>');
							}
						}
					}
					if (json['success']) {
						$.ajax({
							url: '<?php echo EshopHelper::getSiteUrl(); ?>index.php?option=com_eshop&view=quote&layout=popout&format=raw<?php echo EshopHelper::getAttachedLangLink(); ?>',
							dataType: 'html',
							success: function(html) {
								$.colorbox({
									overlayClose: true,
									opacity: 0.5,
									href: false,
									html: html
								});
								$.ajax({
									url: '<?php echo EshopHelper::getSiteUrl(); ?>index.php?option=com_eshop&view=quote&layout=mini&format=raw<?php echo EshopHelper::getAttachedLangLink(); ?>',
									dataType: 'html',
									success: function(html) {
										$('#eshop-quote').html(html);
										$('.eshop-content').hide();
									},
									error: function(xhr, ajaxOptions, thrownError) {
										alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
									}
								});
							},
							error: function(xhr, ajaxOptions, thrownError) {
								alert(thrownError + "\r\n" + xhr.statusText + "\r\n" + xhr.responseText);
							}
						});
					}
			  	}
			});
		});
		// Submit review button
		$('#button-review').bind('click', function() {
			$.ajax({
				url: '<?php echo EshopHelper::getSiteUrl(); ?>index.php?option=com_eshop&task=product.writeReview<?php echo EshopHelper::getAttachedLangLink(); ?>',
				type: 'post',
				dataType: 'json',
				data: $('#review-form input[type=\'text\'], #review-form textarea, #review-form input[type=\'radio\']:checked, #review-form input[type=\'hidden\']'),
				beforeSend: function() {
					$('.success, .warning').remove();
					$('#button-review').attr('disabled', true);
					$('#button-review').after('<span class="wait">&nbsp;<img src="components/com_eshop/assets/images/loading.gif" alt="" /></span>');
				},
				complete: function() {
					$('#button-review').attr('disabled', false);
					$('.wait').remove();
				},
				success: function(data) {
					if (data['error']) {
						$('#review-title').after('<div class="warning">' + data['error'] + '</div>');
					}
					if (data['success']) {
						$('#review-title').after('<div class="success">' + data['success'] + '</div>');
						$('input[name=\'author\']').val('');
						$('textarea[name=\'review\']').val('');
						$('input[name=\'rating\']:checked').attr('checked', '');
					}
				}
			});
		});

		// Function to active reviews tab
		activeReviewsTab = (function(){
			$('#productTab a[href="#reviews"]').tab('show');
		});
		// Function to update price when options are added	
		updatePrice = (function(){
			$.ajax({
				type: 'POST',
				url: '<?php echo EshopHelper::getSiteUrl(); ?>index.php?option=com_eshop&view=product&id=<?php echo $this->item->id; ?>&layout=price&format=raw<?php echo EshopHelper::getAttachedLangLink(); ?>',
				data: $('.product-info input[type=\'text\'], .product-info input[type=\'hidden\'], .product-info input[type=\'radio\']:checked, .product-info input[type=\'checkbox\']:checked, .product-info select, .product-info textarea'),
				dataType: 'html',
				success: function(html) {
					$('#product-price').html(html);
				}
			});
		})
		$(document).ready(function(){
			  $('.image-additional').slick({
				  dots: false,
				  infinite: false,
				  speed: 300,
				  slidesToShow: 3,
				  touchMove: false,
				  slidesToScroll: 1
				});
		});
		
	})
</script>
<?php
if (count($this->productOptions))
{
	?>
	<script type="text/javascript" src="<?php echo JUri::base(true); ?>/components/com_eshop/assets/js/ajaxupload.js"></script>
	<?php
	foreach ($this->productOptions as $option)
	{
		if ($option->option_type == 'File')
		{
			?>
			<script type="text/javascript">
				new AjaxUpload('#button-option-<?php echo $option->product_option_id; ?>', {
					action: 'index.php',
					name: 'file',
					data: {
						option : 'com_eshop',
						task : 'product.uploadFile'
					},
					autoSubmit: true,
					responseType: 'json',
					onSubmit: function(file, extension) {
						jQuery('#button-option-<?php echo $option->product_option_id; ?>').after('<span class="wait">&nbsp;<img src="components/com_eshop/assets/images/loading.gif" alt="" /></span>');
						jQuery('#button-option-<?php echo $option->product_option_id; ?>').attr('disabled', true);
					},
					onComplete: function(file, json) {
						jQuery('#button-option-<?php echo $option->product_option_id; ?>').attr('disabled', false);
						jQuery('.error').remove();
						if (json['success']) {
							alert(json['success']);
							jQuery('input[name=\'options[<?php echo $option->product_option_id; ?>]\']').attr('value', json['file']);
							jQuery('#file-<?php echo $option->product_option_id; ?>').html(json['file']);
						}
						if (json['error']) {
							jQuery('#option-<?php echo $option->product_option_id; ?>').after('<span class="error">' + json['error'] + '</span>');
						}
						jQuery('.wait').remove();
					}
				});
			</script>
			<?php
		}
	}
}