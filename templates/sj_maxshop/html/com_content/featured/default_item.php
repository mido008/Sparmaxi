<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_content
 *
 * @copyright   Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// includes placehold
$yt_temp = JFactory::getApplication()->getTemplate();
include (JPATH_BASE . '/templates/'.$yt_temp.'/includes/placehold.php');

// Create a shortcut for params.
$params = $this->item->params;
$images = json_decode($this->item->images);
$canEdit = $this->item->params->get('access-edit');
$info    = $this->item->params->get('info_block_position', 0);


global $leadingFlag;
$doc = JFactory::getDocument();
$app = JFactory::getApplication();

?>

<?php if ($this->item->state == 0) : ?>
<div class="system-unpublished">
<?php endif; ?> 


	
<?php  if (isset($images->image_intro) and !empty($images->image_intro)) : ?>
	<?php $imgfloat = (empty($images->float_intro)) ? $params->get('float_intro') : $images->float_intro; ?>
    <?php
	// Begin:  The way to resize your image.
	$templateParams = JFactory::getApplication()->getTemplate(true)->params;
	YTTemplateUtils::getImageResizerHelper(array(
		'background' => $templateParams->get('thumbnail_background', '#000'), 
		'thumbnail_mode' => $templateParams->get('thumbnail_mode', 'fit')
		)
	);
	
	$imgW = (isset($leadingFlag) && $leadingFlag)?$templateParams->get('leading_width', '300'):$templateParams->get('intro_width', '200');
	$imgH = (isset($leadingFlag) && $leadingFlag)?$templateParams->get('leading_height', '300'):$templateParams->get('intro_height', '200');
	$imgsrc = YTTemplateUtils::resize($images->image_intro, $imgW, $imgH);
	
	
	//Create placeholder items images
	$src = $images->image_intro;
	if (file_exists(JPATH_BASE . '/' . $src)) {								
		$thumb_img = '<img src="'.$imgsrc.'" alt="'.$images->image_intro_alt.'" />';
	} else if ($is_placehold) {					
		$thumb_img = yt_placehold($placehold_size['listing']);
	}	
	?>
	<figure class="pull-<?php echo htmlspecialchars($imgfloat); ?> item-image" >
		<a  class="listingimg" title="<?php echo htmlspecialchars($images->image_fulltext_alt); ?>" href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid)); ?>" >
			<?php echo $thumb_img; ?>
		</a>
		
		
    </figure>
<?php endif; ?>
	
	
<div class="article-text">
	<?php if ($params->get('show_title')) : ?>
		<header class="article-header">
			<h2>
			<?php if ($params->get('link_titles') && $params->get('access-view')) : ?>
				<a href="<?php echo JRoute::_(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid)); ?>">
				<?php echo $this->escape($this->item->title); ?></a>
			<?php else : ?>
				<?php echo $this->escape($this->item->title); ?>
			<?php endif; ?>
			</h2>
		</header>
	<?php endif; ?>
	
	<?php if (!$params->get('show_intro')) : ?>
		<?php echo $this->item->event->afterDisplayTitle; ?>
	<?php endif; ?>
	
	<?php if ($params->get('show_print_icon') || $params->get('show_email_icon') || $canEdit || 
		  $params->get('show_author') || $params->get('show_category') || $params->get('show_create_date') || 
		  $params->get('show_modify_date') || $params->get('show_publish_date') || $params->get('show_parent_category') || 
		  $params->get('show_hits') ) { ?>
		<?php // Todo Not that elegant would be nice to group the params ?>
			<?php $useDefList = ($params->get('show_modify_date') || $params->get('show_publish_date') || $params->get('show_create_date')
				|| $params->get('show_hits') || $params->get('show_category') || $params->get('show_parent_category') || $params->get('show_author') ); ?>

			<?php if ($useDefList && ($info == 0 || $info == 2)) : ?>
                <aside class="article-aside">
                    <?php echo JLayoutHelper::render('joomla.content.info_block.block', array('item' => $this->item, 'params' => $params, 'position' => 'above')); ?>
                </aside>
			<?php endif; ?>
		<?php } ?>
		
	<?php echo $this->item->event->beforeDisplayContent; ?>
	
	<?php if ($params->get('show_intro')) : ?>
		<div class="article-intro">
			<?php echo $this->item->introtext; ?>
		</div>
	<?php endif; ?>

    <?php if ($useDefList && ($info == 1 || $info == 2)) : ?>
        <aside class="article-aside">
            <?php echo JLayoutHelper::render('joomla.content.info_block.block', array('item' => $this->item, 'params' => $params, 'position' => 'below')); ?>
        </aside>
    <?php  endif; ?>
<?php if ($params->get('show_readmore') && $this->item->readmore) :
	if ($params->get('access-view')) :
		$link = JRoute::_(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid));
	else :
		$menu = JFactory::getApplication()->getMenu();
		$active = $menu->getActive();
		$itemId = $active->id;
		$link1 = JRoute::_('index.php?option=com_users&view=login&&Itemid=' . $itemId);
		$returnURL = JRoute::_(ContentHelperRoute::getArticleRoute($this->item->slug, $this->item->catid));
		$link = new JURI($link1);
		$link->setVar('return', base64_encode($returnURL));
	endif;
?>
			
			<a class="button" href="<?php echo $link; ?>">
			
				<?php if (!$params->get('access-view')) :
					echo JText::_('COM_CONTENT_REGISTER_TO_READ_MORE');
				elseif ($readmore = $this->item->alternative_readmore) :
					echo $readmore;
					if ($params->get('show_readmore_title', 0) != 0) :
					    echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
					endif;
				elseif ($params->get('show_readmore_title', 0) == 0) :
					echo JText::sprintf('COM_CONTENT_READ_MORE_TITLE');	
				else :
					echo JText::_('COM_CONTENT_READ_MORE');
					echo JHtml::_('string.truncate', ($this->item->title), $params->get('readmore_limit'));
				endif; ?></a>
				
			
			<?php if ($this->params->get('show_tags', 1)) : ?>
			<div class="item-tags clearfix">
				<?php $this->item->tagLayout = new JLayoutFile('joomla.content.tags'); ?>
				<?php echo $this->item->tagLayout->render($this->item->tags->itemTags); ?>
			</div>
			<?php endif; ?>
			
<?php endif; ?>

<?php if ($this->item->state == 0) : ?>
</div>
<?php endif; ?>
</div>
<?php

?>
<?php echo $this->item->event->afterDisplayContent; ?>
