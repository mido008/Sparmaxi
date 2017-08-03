<?php
/**
 * @package SJ Categories for VirtueMart
 * @version 2.2.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2014 YouTech Company. All Rights Reserved.
 * @author YouTech Company http://www.smartaddons.com
 *
 */
defined('_JEXEC') or die;

$big_image_config = array(
    'type' => $params->get('imgcfgcat_type'),
    'width' => $params->get('imgcfgcat_width'),
    'height' => $params->get('imgcfgcat_height'),
    'quality' => 90,
    'function' => ($params->get('imgcfgcat_function') == 'none') ? null : 'resize',
    'function_mode' => ($params->get('imgcfgcat_function') == 'none') ? null : substr($params->get('imgcfgcat_function'), 7),
    'transparency' => $params->get('imgcfgcat_transparency', 1) ? true : false,
    'background' => $params->get('imgcfgcat_background'));
?>
<div class="cat-wrap theme4">
    <?php $i = 0;
    foreach ($list as $key => $items) {
        $i++;
        $cat_child = $items->child_cat; ?>
        <div class="sj-categories-inner">
            <div class="sj-categories-heading">
                <div class="icon_left"></div>
                <?php if ($params->get('cat_title_display') == 1) { ?>
                    <div class="cat-title">
                        <?php $caturl = JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id=' . $items->virtuemart_category_id); ?>
                        <a href="<?php echo $caturl; ?>"
                           title="<?php echo $items->category_name; ?>" <?php echo VmCategoriesHelper::parseTarget($options->target); ?> >
                            <?php echo VmCategoriesHelper::truncate($items->category_name, (int)$params->get('cat_title_maxcharacs', 25)); ?>
                        </a>
                    </div>
                <?php } ?>
                <div class="icon_right"></div>
            </div>
            <div class="sj-categories-content cf">
                <?php
                if (!empty($cat_child)) {
                    $k = 0;
                    foreach ($cat_child as $key1 => $item) {
                        $k++;
                        $count = count($cat_child); ?>
                        <div class="sj-categories-content-inner">
                            <div class="child-cat <?php echo ($k == $count) ? 'cat-lastitem' : ''; ?>">
                                <div class="child-cat-info">
                                    <?php $img = VmCategoriesHelper::getVmCImage($item, $params, "imgcfgcat");
                                    if ($img && (strlen($img['src']) != '')) {
                                        ?>
                                        <div class="image-cat" title="<?php echo $item->category_name; ?>"
                                             style="max-width:<?php echo $params->get('imgcfg_width'); ?>px;">
                                            <?php $caturl = JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id=' . $item->virtuemart_category_id); ?>
                                            <a href="<?php echo $caturl; ?>"
                                               title="<?php echo $item->category_name; ?>" <?php echo VmCategoriesHelper::parseTarget($options->target); ?> >
                                                <img
                                                    src="<?php echo VmCategoriesHelper::imageSrc($img, $big_image_config); ?>"
                                                    title="<?php echo $item->category_name; ?>"
                                                    alt="<?php echo $item->category_name; ?>"/>
                                            </a>
                                        </div>
                                    <?php } ?>
                                    <?php if ((int)$params->get('cat_sub_title_display', 1)) { ?>
                                        <div class="child-cat-desc">
                                            <div class="child-cat-title">
                                                <?php $caturl = JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id=' . $item->virtuemart_category_id); ?>
                                                <a href="<?php echo $caturl ?>"
                                                   title="<?php echo $item->category_name; ?>" <?php echo VmCategoriesHelper::parseTarget($options->target); ?> >
                                                    <?php echo VmCategoriesHelper::truncate($item->category_name, (int)$params->get('cat_sub_title_maxcharacs', 25)); ?>
                                                </a>
                                            </div>
                                            <?php if ($params->get('cat_all_product', 1)) { ?>
                                                <div class="num_items" style="float:left;color: #737373;">
                                                    <?php echo '(' . ($item->number_product) . ')'; ?>
                                                </div>
                                            <?php } ?>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    <?php
                    }
                } else {
                    ?>
                    <div class="sj-categories-content-inner">
                        <div class="child-cat subcat-empty">
                            <div class="child-cat-info">
                                <?php echo JText::_('No sub-categories to show!'); ?>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        </div>
    <?php } ?>
</div>
