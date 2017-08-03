<?php
/**
 * @version		1.0.0
 * @package		Joomla
 * @subpackage	EShop
 * @author  	Giang Dinh Truong
 * @copyright	Copyright (C) 2011 Ossolution Team
 * @license		GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die;

class modEshopTagsHelper 
{
    /**
     *
     * @param object $params
     * @return tags object list
     */
    function getListTag(&$params)
    {
    	$db = JFactory::getDbo();
        $query = $db->getQuery(true);

		// Get data #__eshop_tags
        $query->select('tag_name, hits')
        	->from('#__eshop_tags')
        	->where('published = 1')
        	->order('tag_name');
        $db->setQuery($query);
		$lists = $db->loadObjectList();
		
		// Change these font sizes if you will
		$max_size	= $params->get( 'max_font_size', 250); 	// max font size in %
    	$min_size	= $params->get( 'min_font_size', 100); 	// min font size in %

		// Get data hits from #__eshop_tags
        $query->clear()
        	->select('hits')
        	->from('#__eshop_tags')
        	->where('published=1')
        	->order('hits');
        $db->setQuery($query);
		$hits = $db->loadColumn();

		// Get the largest and smallest array values of hits
		$max_qty = count($hits)? max(array_values($hits)):0;
		$min_qty = count($hits)? min(array_values($hits)):0;

		// Find the range of values
		$spread = $max_qty - $min_qty;
		if ($spread == 0)
			$spread = 1; // we don't want to divide by zero
		
		// Determine the font-size increment
		// this is the increase per tag quantity (times used)
		$step = ($max_size - $min_size) / ($spread);

		foreach ($lists as $tag)
		{
			$tag->size = $min_size + (($tag->hits - $min_qty) * $step);
			$tag->link = JRoute::_("index.php?option=com_eshop&task=search&keyword=$tag->tag_name");
		}
		return $lists;
	}
}