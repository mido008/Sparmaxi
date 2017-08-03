<?php

/*
 * ------------------------------------------------------------------------
 * Copyright (C) 2009 - 2015 The YouTech JSC. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: The YouTech JSC
 * Websites: http://www.smartaddons.com - http://www.cmsportal.net
 * ------------------------------------------------------------------------
*/

defined('_JEXEC') or die();


class PkgInstallerScript
{
	
    /**
     * Called before any type of action
     */
    public function preFlight($route, JAdapterInstance $adapter)
    {
        return true;
    }


    /**
     * Called after any type of action
     *
     */
    public function postFlight($route, JAdapterInstance $adapter)
    {
        return true;
    }
	
	
}
