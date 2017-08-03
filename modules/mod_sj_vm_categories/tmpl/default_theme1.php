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
?>
<div class="cat-wrap theme1">
    <?php $i = 0;
    foreach ($list as $key => $items) {
        $i++;
        $cat_child = $items->child_cat;?>
        <div class="content-box">
            <?php if ((int)$params->get('cat_title_display', 1)) { ?>
                <div class="cat-title">
                    <?php $caturl = JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id=' . $items->virtuemart_category_id); ?>
                    <a title="<?php echo $items->category_name; ?>"
                       href="<?php echo $caturl; ?>" <?php echo VmCategoriesHelper::parseTarget($options->target); ?> >
                        <?php echo VmCategoriesHelper::truncate($items->category_name, (int)$params->get('cat_title_maxcharacs', 25)); ?>
                    </a>
                </div>
            <?php } ?>
            <?php if ((int)$params->get('cat_sub_title_display', 1)) { ?>
                <div class="child-cat">
                    <?php
                    if (!empty($items->child_cat)) {
                        foreach ($items->child_cat as $key1 => $item) {
                            ?>
                            <div class="child-cat-title">
                                <?php $caturl = JRoute::_('index.php?option=com_virtuemart&view=category&virtuemart_category_id=' . $item->virtuemart_category_id); ?>
                                <a href="<?php echo $caturl; ?>"
                                   title="<?php echo $item->category_name; ?>" <?php echo VmCategoriesHelper::parseTarget($options->target); ?>>
                                    <?php echo VmCategoriesHelper::truncate($item->category_name, (int)$params->get('cat_sub_title_maxcharacs', 25)); ?>
                                </a>
                                <?php if ((int)$params->get('cat_all_product', 1)) { ?>
                                    <span class="num_items">
                                    <?php echo '(' . ($item->number_product) . ')'; ?>
                                </span>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    <?php } else { ?>
                        <p>
                            <?php echo JText::_('No sub-categories to show!'); ?>
                        </p>
                    <?php } ?>
                </div>
            <?php } ?>
        </div> <!-- END sub_content -->
        <?php
        $clear = 'clr1';
        if ($i % 2 == 0) $clear .= ' clr2';
        if ($i % 3 == 0) $clear .= ' clr3';
        if ($i % 4 == 0) $clear .= ' clr4';
        if ($i % 5 == 0) $clear .= ' clr5';
        if ($i % 6 == 0) $clear .= ' clr6';
        ?>
        <div class="<?php echo $clear; ?>"></div>
    <?php } ?>
</div>
       
