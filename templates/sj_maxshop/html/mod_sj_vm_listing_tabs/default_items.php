<?php
/**
 * @package Sj Vm Listing Tabs
 * @version 1.0.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2014 YouTech Company. All Rights Reserved.
 * @author YouTech Company http://www.smartaddons.com
 *
 */

defined('_JEXEC') or die;

$currency = CurrencyDisplay::getInstance();
$small_image_config = array(
    'type' => $params->get('imgcfg_type'),
    'width' => $params->get('imgcfg_width'),
    'height' => $params->get('imgcfg_height'),
    'quality' => 90,
    'function' => ($params->get('imgcfg_function') == 'none') ? null : 'resize',
    'function_mode' => ($params->get('imgcfg_function') == 'none') ? null : substr($params->get('imgcfg_function'), 7),
    'transparency' => $params->get('imgcfg_transparency', 1) ? true : false,
    'background' => $params->get('imgcfg_background'));
if (!empty($child_items)) {
    $app = JFactory::getApplication();
    $k = $app->input->getInt('ajax_reslisting_start', 0);
    foreach ($child_items as $item) {
        $k++; 
        
        $max= 0;
        
        foreach($item->categories as $categorie_id)
        {
            if($max <= $categorie_id) $max=$categorie_id;
        }
        
        $pos = strpos($item->link,"virtuemart_category_id=");
        $ch = substr($item->link,0,$pos)."virtuemart_category_id=$max";
        $item->link = $ch;
        
        ?>
        <div class="ltabs-item new-ltabs-item">
            <div class="item-inner">
                <?php
                $item_img = VMListingTabsHelper::getVmImage($item, $params, 'imgcfg');
                if ($item_img) {
                    ?>
                    <div class="item-image">
                        <a href="<?php echo $item->link ?>"
                           title="<?php echo $item->title; ?>" <?php echo VMListingTabsHelper::parseTarget($params->get('link_target')); ?> >
                            <?php echo VMListingTabsHelper::imageTag($item_img, $small_image_config); ?>
                            <span class="image-border"></span>
                        </a>
                    </div>
                <?php } ?>

                <?php if ($params->get('item_title_display', 1) == 1) { ?>
                    <div class="item-title">
                        <a href="<?php echo $item->link; ?>"
                           title="<?php echo $item->title ?>" <?php echo VMListingTabsHelper::parseTarget($params->get('link_target')); ?> >
                            <?php echo VMListingTabsHelper::truncate($item->title, (int)$params->get('item_title_max_characters', 25)); ?>
                        </a>
                    </div>
                <?php } ?>

                <?php if ((int)$params->get('item_prices_display', 1) && ( !empty($item->prices['salesPrice']) || !empty($item->prices['salesPriceWithDiscount'])) ) { ?>
                    <div class="item-prices">
                        <?php
                        if (!empty($item->prices['salesPrice'])) {
                            echo $currency->createPriceDiv('salesPrice', JText::_("Price: "), $item->prices, false, false, 1.0, true);
                        }
                        if (!empty($item->prices['salesPriceWithDiscount'])) {
                            echo $currency->createPriceDiv('salesPriceWithDiscount', JText::_("Price: "), $item->prices, false, false, 1.0, true);
                        } ?>
                    </div>
                <?php } ?>

                <?php if ((int)$params->get('item_description_display', 1) && VMListingTabsHelper::_trimEncode($item->_description) != '') { ?>
                    <div class="item-desc">
                        <?php echo $item->_description;?>
                    </div>
                <?php } ?>

                <div class="other-infor">

                    <?php if ($params->get('item_created_display', 1) == 1) { ?>
                        <div class="created-date ">
                            <?php
                            echo JHTML::_('date', $item->created_on, JText::_('DATE_FORMAT_LC3'));
                            ?>
                        </div>
                    <?php } ?>

                    <?php if ($params->get('item_readmore_display') == 1) { ?>
                        <div class="item-readmore">
                            <a href="<?php echo $item->link; ?>"
                               title="<?php echo $item->title ?>" <?php echo VMListingTabsHelper::parseTarget($params->get('item_link_target')); ?>>
                                <?php echo $params->get('item_readmore_text','Read More'); ?>
                            </a>
                        </div>
                    <?php } ?>

                </div>

                <?php if ($params->get('item_addtocart_display', 1)) {
                    $_item['product'] = $item; ?>
                    <div class="item-addtocart">
                        <?php echo shopFunctionsF::renderVmSubLayout('addtocart', $_item); ?>
                    </div>
                <?php } ?>
            </div>
        </div>
        <?php $clear = 'clr1';
        if ($k % 2 == 0) $clear .= ' clr2';
        if ($k % 3 == 0) $clear .= ' clr3';
        if ($k % 4 == 0) $clear .= ' clr4';
        if ($k % 5 == 0) $clear .= ' clr5';
        if ($k % 6 == 0) $clear .= ' clr6';
        ?>
        <div class="<?php echo $clear; ?>"></div>
    <?php
    } ?>
<?php
}?>

