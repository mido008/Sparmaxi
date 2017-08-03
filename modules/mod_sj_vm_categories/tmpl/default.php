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

JHtml::stylesheet('modules/' . $module->module . '/assets/css/sj-categories.css');
ImageHelper::setDefault($params);
$uniqued = 'sj_categories_' . rand() . time();
$options = $params->toObject();
$theme = $params->get('theme', 'theme1');

if ($theme == 'theme4') {
    if (!defined('SMART_JQUERY') && $params->get('include_jquery', 0) == "1") {
        JHtml::script('modules/' . $module->module . '/assets/js/jquery-1.8.2.min.js');
        JHtml::script('modules/' . $module->module . '/assets/js/jquery-noconflict.js');
        define('SMART_JQUERY', 1);
    }

    JHtml::script('modules/' . $module->module . '/assets/js/jquery.imagesloaded.js');
    JHtml::script('modules/' . $module->module . '/assets/js/jquery.sj_accordion.js');
    ?>

    <script type="text/javascript">
        //<![CDATA[
        jQuery(document).ready(function ($) {
            ;
            (function (element) {
                var $element = $(element);
                $(window).load(function () {
                    $element.imagesLoaded(function () {
                    });
                });
                $element.imagesLoaded(function () {

                    $element.sj_accordion({
                        items: '.sj-categories-inner',
                        heading: '.sj-categories-heading',
                        content: '.sj-categories-content',
                        active_class: 'selected',
                        event: '<?php echo $params->get('accmouseenter','click'); ?>',
                        delay: 300,
                        duration: 500,
                        active: '1'
                    });

                    var height_content = function () {
                        $('.sj-categories-inner', $element).each(function () {
                            var $inner = $('.sj-categories-content', $(this).filter('.selected'));
                            $inner.css('height', 'auto');
                            if ($inner.length) {
                                var inner_height = $inner.height();
                                $inner.css('height', inner_height);
                            }
                        });
                    }

                    if ($.browser.msie && parseInt($.browser.version, 10) <= 8) {

                    } else {
                        $(window).resize(function () {
                            height_content();
                        });
                        $(window).load(function () {
                            height_content();
                        });
                    }
                });
            })('#<?php echo $uniqued;?>')

        });
        //]]>

    </script>
<?php } ?>

<?php if (!empty($options->pretext)) { ?>
    <div class="intro_text">
        <?php echo $options->pretext; ?>
    </div>
<?php } ?>

<!--[if lt IE 9]>
<div id="<?php echo $uniqued; ?>"
     class="sj-categories <?php echo $theme; ?> <?php echo $options->deviceclass_sfx; ?> msie lt-ie9"><![endif]-->
<!--[if IE 9]>
<div id="<?php echo $uniqued; ?>"
     class="sj-categories <?php echo $theme; ?> <?php echo $options->deviceclass_sfx; ?> msie"><![endif]-->
<!--[if gt IE 9]><!-->
<div id="<?php echo $uniqued; ?>" class="sj-categories <?php echo $theme; ?> <?php echo $options->deviceclass_sfx; ?>">
    <!--<![endif]-->
    <?php include JModuleHelper::getLayoutPath($module->module, $layout . '_' . $theme); ?>
</div>

<?php if (!empty($options->posttext)) { ?>
    <div class="footer_text">
        <?php echo $options->posttext; ?>
    </div>
<?php } ?>
 