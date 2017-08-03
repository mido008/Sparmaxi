<?php
/**
*
* Shows the products/categories of a category
*
* @package	VirtueMart
OO@subpackage
* @author Max Milbers
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2014 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
 * @version $Id: default.php 6104 2012-06-13 14:15:29Z alatak $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

//defined('DS') or define('DS', DIRECTORY_SEPARATOR);

defined('DS') or define('DS', DIRECTORY_SEPARATOR);
if (!class_exists( 'VmConfig' )) 
{
	echo "jjjjjjjj";
	require(JPATH_ROOT.DS.'administrator'.DS.'components'.DS.'com_virtuemart'.DS.'helpers'.DS.'config.php');
}
else echo "lllllllllllllll";

VmConfig::loadConfig();
VmConfig::loadJLang('mod_virtuemart_product', true);

//		print_r("<pre>");
//		print_r(VmConfig::loadJLang('mod_virtuemart_product', true));
//                print_r($params);
//                print_r("<br>-----------------------------------------<br>");
//                print_r("</pre>");

$categories = $viewData['categories'];
$categories_per_row = VmConfig::get ( 'categories_per_row', 3 );

if ($categories) {

// Category and Columns Counter
$iCol = 1;
$iCategory = 1;

// Calculating Categories Per Row
$category_cellwidth = ' width'.floor ( 100 / $categories_per_row );

// Separator
$verticalseparator = " vertical-separator";
?>

<div class="category-view">

<?php 

// Product Model
$productModel = VmModel::getModel('Product');
print_r("<pre>");
//print_r($productModel);
print_r("</pre>");

// Start the Output
    foreach ( $categories as $category ) {
		
	// Get Products
	
	$products = $productModel->getProductListing(true,10,true,true,false,true,$category->virtuemart_category_id);
	$productModel->addImages($products);

 		print_r("<pre>");
		if($products)
		{
                	//print_r($productModel);
                	print_r("<br>-----------------------------------------<br>");
		}
                print_r("</pre>");

	    // Show the horizontal seperator
	    if ($iCol == 1 && $iCategory > $categories_per_row) { 
	    ?>
	    <div class="horizontal-separator"></div>
	    <?php }

	    // this is an indicator wether a row needs to be opened or not
	    if ($iCol == 1) { ?>
  <div class="row">
        <?php }

        // Show the vertical separator
        if ($iCategory == $categories_per_row or $iCategory % $categories_per_row == 0) {
          $show_vertical_separator = ' ';
        } else {
          $show_vertical_separator = $verticalseparator;
        }

        // Category Link
        $caturl = JRoute::_ ( 'index.php?option=com_virtuemart&view=category&virtuemart_category_id=' . $category->virtuemart_category_id , FALSE);

          // Show Category ?>
    <div class="category floatleft<?php echo $category_cellwidth . $show_vertical_separator ?>">
      <div class="spacer">
        <h2>
          <a href="<?php echo $caturl ?>" title="<?php echo vmText::_($category->category_name) ?>">
          <?php echo vmText::_($category->category_name) ?>
          <br />
          <?php // if ($category->ids) {
            echo $category->images[0]->displayMediaThumb("",false);
          //} ?>
          </a>
        </h2>
      </div>
		<?php 
			// Show Products
			//print_r("<pre>");
			foreach($products as $product)
			{	
				print_r("<pre>");
				echo $product->product_name."<br/> ".$product->virtuemart_media_id[0];
				
				echo $product->images[0]->displayMediaThumb('class="browseProductImage"', false);
						
				//print_r($product);
				print_r("</pre>");
			}
			//print_r($products);
		//	print_r("</pre>");
		?>
    </div>
	    <?php
	    $iCategory ++;

	    // Do we need to close the current row now?
        if ($iCol == $categories_per_row) { ?>
    <div class="clear"></div>
	</div>
		    <?php
		    $iCol = 1;
	    } else {
		    $iCol ++;
	    }
    }
	// Do we need a final closing row tag?
	if ($iCol != 1) { ?>
		<div class="clear"></div>
	</div>
	<?php
	}
	?></div><?php
 } ?>
