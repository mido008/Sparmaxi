<?php
	defined ( '_JEXEC' ) or die ( 'Restricted access' );

	$model = new VirtueMartModelProduct();
	$calculator = calculationHelper::getInstance();
	$currency = CurrencyDisplay::getInstance();
    

?>

<div class="product-related-products">
	<h3 class="item-title"><?php echo JText::_('COM_VIRTUEMART_RELATED_PRODUCTS'); ?> <div class="nav_button">
				<div class="prevs"><i class="fa fa-angle-left"></i></div>
				<div class="nexts"><i class="fa fa-angle-right"></i></div>
			</div></h3>


	<div id="yt_relate" class="pro_relate">
		<div class="caroufredsel">
			<ul id="yt_caroufredsel_relate">
					<?php
						foreach ($this->product->customfieldsSorted['related_products'] as $field) {						
						if(!empty($field->display)) {
					?>
						<li class="item">
									<div class="product-content-inner">
										<div class="product-left pull-left">
                                            <?php
                                                //$product = $model->getProductSingle($field->customfield_value,false);

                                                // Load Images
                                                //$model->addImages($product);

                                            ?>
											 <!-- <div class="product-img">
												<a href='<?php echo $product->link; ?>'><img src="<?php echo $product->images[0]->file_url_thumb; ?>" /> </a>
											</div> -->
										</div>
										<div class="product-right  pull-left">
												<div class="price">
                                                    <div class="vote-rating">
                                                     <?php // Product Rating
                                                        $products = VmModel::getModel('product');
                                                        $ratingModel = VmModel::getModel('ratings');
                                                        $product->showRating = $ratingModel->showRating($product->virtuemart_product_id);
                                                        if ($product->showRating) {
                                                             $product->vote = $ratingModel->getVoteByProduct($product->virtuemart_product_id);
                                                             $product->rating = $ratingModel->getRatingByProduct($product->virtuemart_product_id);
                                                             $maxrating = VmConfig::get('vm_maximum_rating_scale', 5);

                                                             if (empty($product->rating)) {
                                                        ?>
                                                             <span class="vote"><?php echo JText::_('COM_VIRTUEMART_RATING') . ' ' . JText::_('COM_VIRTUEMART_UNRATED') ?></span>
                                                             <?php } else { ?>
                                                             <span class="vote">

                                                                <span title=" <?php echo (JText::_("COM_VIRTUEMART_RATING_TITLE") . $product->rating->rating . '/' . $maxrating) ?>" class="vmicon vm2-stars<?php echo round($product->rating->rating); ?>" style="display:inline-block;">
                                                                </span>
                                                             </span>
                                                             <?php } ?>
                                                        <?php }?>
                                                    </div>
													<?php echo $field->display;?>
													<div class="PricesalesPrice">
														<span class="PricesalesPrice">
														<?php
                                                            //var_dump($field);die;
															echo jText::_($field->custom_field_desc);
															$product = $model->getProductSingle($field->custom_value,false);
															$price = $calculator -> getProductPrices($product);
															echo $currency->priceDisplay($price['salesPrice']);
														?>
														</span>
													</div>



												</div>

											 </div>

									</div>

						</li>
					<?php
							}
						}
					?>
			</ul>
		</div>
	</div>
</div>

<?php
$document = JFactory::getDocument();

?>

<?php if(count($this->product->customfieldsSorted['related_products']) > 2){ ?>
	<script type="text/javascript">
		jQuery(document).ready(function($) {
			$('#yt_caroufredsel_relate').carouFredSel({
				responsive: true,
				auto: false,
				scroll: 1,
				prev: '.nexts',
				next: '.prevs',
				direction: "up",
				mousewheel: true,
				items: {
					width: 290,
					height: 'auto',	//	optionally resize item-height
					visible: {
						min: 1,
						max: 3
					}
				}
			});
		});
	</script>
<?php } ?>
<?php ?>
