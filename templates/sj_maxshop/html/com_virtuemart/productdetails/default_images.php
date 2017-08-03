<?php
/**
 *
 * Show the product details page
 *
 * @package    VirtueMart
 * @subpackage
 * @author Max Milbers, Valerie Isaksen

 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * @version $Id: default_images.php 8657 2015-01-19 19:16:02Z Milbo $
 */
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
// Product Main Image
?>

<?php
// Showing The Additional Images
if (!empty($this->product->images) and count ($this->product->images)>1) {   ?>
	<div id="thumb-slider" class="thumb-vertical-outer">
		<a class="prev control"  href="#"><i class="fa fa-angle-up"></i></a>
		<div class="thumb-vertical">
			<ul class="previews-list ">
				<?php
				// List all Images
				if (count($this->product->images) > 0) {
					foreach ($this->product->images as $key=>$image) {
					$imageslarge = YTTemplateUtils::resize($image->file_url, '650', '650', 'fill');
					$imagesradditional = YTTemplateUtils::resize($image->file_url, '450', '450', 'fill');
					?>
					<li class="owl2-item">
						<a href="#" class="img thumbnail" data-image="<?php echo $imagesradditional;?>" data-zoom-image="<?php echo $imageslarge;?>"  >
							<img src="<?php echo $imagesradditional;?>" alt="" />
						</a>
					</li>
				<?php }
				}
				?>
				
			</ul>
		</div>
		<a class="next control" href="#"><i class="fa fa-angle-down"></i></a>
	</div>
	
<?php }  ?>

<?php 
if (!empty($this->product->images[0])) {
    $imagesrcmain = YTTemplateUtils::resize($this->product->images[0]->file_url, '650', '650', 'fill');
?>
    <div class="main-images">
        <div class="large-image">
			<img id="zoom_img_large" itemprop="image" class="product-image-zoom" data-zoom-image="<?php echo $imagesrcmain;?>" src="<?php echo $imagesrcmain;?>" title="" alt="" />
            <span id="zimgex"><i class="fa fa-search-plus"></i></span>
		</div>

    </div>

<?php } ?>


<?php
$document = JFactory::getDocument();
$app = JFactory::getApplication();
$templateDir = JURI::base() . 'templates/' . $app->getTemplate();
?>
<script type="text/javascript" src="<?php echo $templateDir.'/js/jquery.elevateZoom-3.0.8.min.js' ?>"></script>
<script type="text/javascript" src="<?php echo $templateDir.'/js/jquery.jcarousellite.min.js' ?>"></script>


<script type="text/javascript">
    jQuery(document).ready(function($) {
		var zoomCollection = '.large-image img';
        $(zoomCollection).elevateZoom({
            gallery:'thumb-slider',
			galleryActiveClass: "active",
            zoomType	: "inner",
			cursor: "crosshair",
			easing:true
        });
        $("#zimgex").bind("click", function(e) {
            var ez = $('#zoom_img_large').data('elevateZoom');
            $._fancybox(ez.getGalleryList());
            return false;
        });
		
		var _isMobile = {
		  iOS: function() {
                           
		   return navigator.userAgent.match(/iPhone/i);
		  },
		  any: function() {
		   return (_isMobile.iOS());
		  }
		};
		
        $(".thumb-vertical-outer .thumb-vertical").jCarouselLite({
		  btnNext: ".thumb-vertical-outer .next",
		  btnPrev: ".thumb-vertical-outer .prev",
		  circular: false,
        visible:4,
		  vertical: _isMobile.any()? true : true
		});
    });
</script>
