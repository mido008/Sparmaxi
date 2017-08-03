<?php
/**
 * @package Sj Vm Listing
 * @version 2.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2012 YouTech Company. All Rights Reserved.
 * @author YouTech Company http://www.smartaddons.com
 * 
 */
defined( '_JEXEC' ) or die;

if(!empty($list)){
	
	$options=$params->toObject();
	$nb_cols = (int)$options->nb_cols;
	$nb_rows = (int)$options->nb_rows;
	 if($nb_cols <= 0){
		$nb_cols = 1;
	}
	if($nb_rows <= 0){
		$nb_rows = 1;
	} 
	$nb_page = $nb_cols * $nb_rows;
	if($params->get('small_image_width')<32){
		$params->set('small_image_width',32);
	}
	if($params->get('small_image_width')>=(($options->width_module-30)/$nb_cols-90)){
		$params->set('small_image_width',(($options->width_module-30)/$nb_cols -90));
	}
	if($params->get('small_image_height')<32){
		$params->set('small_image_height',32);
	}
	
	$image_config = array(
			'output_width'  => $params->get('item_image_width',  200),
			'output_height' => $params->get('item_image_height', 200),
			'function'		=> $params->get('item_image_function', 'resize_none'),
			'background'	=> $params->get('item_image_background', null)
	);
	$small_image_config=array(
			'output_width'  => $params->get('small_image_width',  200),
			'output_height' => $params->get('small_image_height', 200),
			'function'		=> $params->get('small_image_function', 'resize_none'),
			'background'	=> $params->get('small_image_background', null)
	);
	$vm_currency_display = &CurrencyDisplay::getInstance();
	$uniquied=rand().time();

?>
	<script type="text/javascript">
		//<![CDATA[
			$jsmart(document).ready(function($) {
				$('#sj_vm_listing_<?php echo $uniquied ?> .lt-wrap-inner').jsmart_listing();
			});
		//]]>
	</script>
	
	<div  id="sj_vm_listing_<?php echo $uniquied ?>"    class="sj-listing" style="width:<?php echo $options->width_module; ?>px;">
		<?php if(!empty($options->pretext)){ ?>
			<div class="intro_text" ><?php echo $options->pretext;?></div>
		<?php } ?>
	<div class="lt-wrap">
	<?php  foreach ($list as $cat) {  $items=$cat->child; 	?>				 			 			    			      
		<div class="lt-wrap-inner <?php echo $options->theme ?>" style="width:<?php echo $options->width_module - 30; ?>px;" >
			<div class="lt-header">
				<?php if($options->cat_title_linkable==1){?>
					<a class="lt-cat-name" href="<?php echo $cat->link; ?>" title="<?php $cat->category_name ?>" <?php echo YTools::parseTarget($options->link_target)?> >
						<?php echo Ytools::truncate($cat->category_name,$options->cat_title_max_characters) ;?>
					</a>
				<?php }else { ?>
					<span class="lt-cat-name" >
						 <?php  echo Ytools::truncate($cat->category_name,$options->cat_title_max_characters) ; ?>
					</span>
				<?php 		}?>
				<?php if($options->item_all_display==1) {?>
					<a class="lt-seeall" href="<?php echo $cat->link; ?>" title="<?php $cat->category_name ?>" <?php echo YTools::parseTarget($options->link_target)?> >
						See all &raquo;
					</a>
				<?php }?>
			</div>	
			<?php if(!empty($items)) { ?>
			<div  class="lt-items"   >				
				<?php $i=0; foreach ($items as $key => $item){ $i++;
				$curr_class = ($key==0)?'curr':'';
				$nb_items = count($items); 
				if($i%$nb_page == 1 || $nb_page == 1){?>
				<div class="lt-content-page <?php echo $curr_class; ?>">
				<?php } ?>
					<div class="lt-item" style="width:<?php echo 100/$nb_cols; ?>%; height:<?php echo  $options->small_image_height + 16 + 28 ?>px;" >		
						<div class="lt-item-inner">																					
							<?php if($options->item_small_image_display==1){?>		
							<div class="lt-icon" style="width:<?php echo $options->small_image_width + 16 ?>px; height:<?php echo $options->small_image_height +16 ?>px;" >														
								<div class="lt-icon-inner" style="width:<?php echo $options->small_image_width ?>px; height:<?php echo $options->small_image_height ?>px;" >
								<?php if($options->item_image_linkable==1) {?>									
									<a href="<?php echo $item->link; ?>" title="<?php echo $item->product_name ?>" <?php echo YTools::parseTarget($options->link_target)?> >
										<img src="<?php  echo YTools::resize($item->image,$small_image_config);?>" title="<?php echo $item->product_name;?>" alt="<?php echo $item->product_name?>"/>
									</a> 
								<?php }else{?>
										<img src="<?php  echo YTools::resize($item->image,$small_image_config);?>" title="<?php echo $item->product_name;?>" alt="<?php echo $item->product_name?>"/>
								<?php } ?>
								</div>
							</div>
							<?php } ?> 																					
							<?php if($options->item_title_display == 1 || $options->price_display == 1 ){ ?>
							<div class="lt-summary" style="height:<?php echo $options->small_image_height +16 ?>px;" >																			  
								<?php if($options->item_title_display==1) {?>
								<div class="lt-title">	
									<?php if($options->item_title_linkable==1) {?>
										<a  href="<?php echo $item->link; ?>" title="<?php echo $item->product_name ?>" <?php echo YTools::parseTarget($options->link_target)?> >
											<?php echo Ytools::truncate($item->product_name,$options->item_title_max_characters) ;?>
										</a>		  
									<?php } else{ ?> 
										<span> 
											<?php echo  Ytools::truncate($item->product_name,$options->item_title_max_characters);?>
										</span>
									<?php  }?>		  
									</div>                 					 
								<?php  }?>
								<?php if( $options->price_display == 1 ): ?>
									<div class="lt-prices" >								
										<div class="sale-price">
											<?php echo $va= $vm_currency_display->priceDisplay($item->prices['salesPrice']); ?>
										</div>
										<?php if ( $item->prices['discountAmount'] >0 ){
											$price_before_discount = $item->prices['salesPrice'] + $item->prices['discountAmount']; ?>
											<div class="sale-price-before" >
											 <?php echo $vm_currency_display->priceDisplay($price_before_discount); ?>
											</div>
										<?php } ?>	
									</div>
								<?php endif;  ?>
							</div>
							<?php }?>
							<?php  if ($options->item_description_display == 1) {?>
							<div class="lt-more" >
								<?php  if ($options->item_description_display == 1 ) {?>
								<div class="lt-desc" >
									<?php
										$desc = "";
										if(!empty($item->product_s_desc)){
											YTools::extractImages($item->product_s_desc);
											$desc = $item->product_s_desc;
										}else{
											YTools::extractImages($item->product_desc);
											$desc = $item->product_desc;
										}
										if ( (int)$params->get('item_description_striptags', 1) ){
											$keep_tags = $params->get('item_description_keeptags', '');
											$keep_tags = str_replace(array(' '), array(''), $keep_tags);
											$tmp_desc = strip_tags($desc ,$keep_tags );
											echo YTools::truncate($tmp_desc, (int)$params->get('item_description_max_characters'));
										} else {
											echo YTools::truncate($desc, (int)$params->get('item_description_max_characters'));
										}
									?>
								</div><!-- End sj_item_content -->
								<?php	}?>		       																																									  
								<?php if ($options->item_readmore_display == 1) {?>   
								<div class="lt-readmore"   >
									<a href="<?php echo $item->link; ?>" title="<?php echo $item->product_name ?>" <?php echo YTools::parseTarget($options->link_target)?> >
										<?php echo $options->item_readmore_text;?>
									</a>
								</div><!--End sj_item_read_more-->
								<?php }?>
								<?php if($options->item_show_reviews==1) {?>
								<div class="lt-reviews">
									<?php  echo (int)$item->hits ?><?php echo ((int)($item->hits) > 1)?JText::_(" reviews"):JText::_(" review")?>
								</div>
								<?php }?>
							</div>
							<?php } ?>
						</div>        				 															    
					</div>
				<?php 
				if($i%$nb_page==0 || $i==$nb_items){ ?>
					<div class="clear"></div>
				</div>
				<?php }
				}?>
				<div class="clear"></div>
			</div>
			
			<?php
				$number_item_on_page = $nb_page;
                if($nb_items){
                	$number_page = $nb_items*1.0 / $number_item_on_page;
                    if (intval($number_page)<$number_page){
                    	$number_page = intval($number_page) + 1;
                    } else {
                    	$number_page = intval($number_page);
                    }	
                 }
			?>
            <?php if($number_page>1){ ?>    
			<div class="lt-control">
				<ul class="lt-control-inner" style="width:<?php // echo ($number_page*15)+36; ?>px;">
					<li class="lt-previous lt-page"></li>
                    <?php for($nb=0; $nb<$number_page; $nb++){
								$active_class = $nb==0 ? " active" : ""; ?>
                    		<li class="lt-page lt-page-<?php echo $nb.$active_class; ?>"></li>
                    <?php }?>
					<li class="lt-next lt-page"></li>
				</ul>
			</div>
			<?php }?>
			<?php } else {
				echo JText::_('There are no items matching the selection!');
			}?>
		</div>
	<?php } ?>
	</div>
		<?php if(!empty($options->posttext)){ ?>	
		<div class="footer_text"><?php echo $options->posttext;?></div>
		<?php } ?>
		<div class="clear"></div>
	</div>
	
	<!-- add css header -->
	<?php ob_start(); ?> 
	<?php if($options->item_small_image_display==1){?>
	#sj_vm_listing_<?php echo $uniquied ?> .lt-wrap-inner .lt-items .lt-item .lt-item-inner:before{
		height:<?php echo $options->small_image_height + 16 + 18; ?>px;
		width:<?php echo $options->small_image_width + 16 + 18; ?>px;
		left:-<?php echo $options->small_image_width + 16 + 17; ?>px;
	}
	
	#sj_vm_listing_<?php echo $uniquied ?> .lt-wrap-inner.theme1 .lt-items .lt-item .lt-item-inner{
		margin-left:<?php echo $options->small_image_width + 16 + 21 ?>px;
	}
	
	#sj_vm_listing_<?php echo $uniquied ?> .lt-wrap-inner.theme1 .lt-items .lt-item .lt-item-inner .lt-icon{
		left:-<?php echo $options->small_image_width + 16 + 17; ?>px;
	}
	
	#sj_vm_listing_<?php echo $uniquied ?> .lt-wrap-inner.theme1 .lt-items .lt-item .lt-item-inner:hover .lt-summary{
		min-height:48px;
	}
	<?php }?>
	
	
	<?php  if ($options->item_description_display == 0 && $options->item_readmore_display == 0 && $options->item_show_reviews==0 && $options->item_title_display == 0 && $options->price_display == 0  ) {?>
	#sj_vm_listing_<?php echo $uniquied ?> .lt-wrap-inner.theme1 .lt-item .lt-item-inner{
		border:none;
	}
	<?php }?>
	
	<?php 
		$stylesheet = ob_get_contents();
		ob_end_clean();
		$docs = JFactory::getDocument();
		$docs->addStyleDeclaration($stylesheet ); 
	?>
 <?php   } else { echo JText::_('Has no content to show!');}?>
 

   
  