<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_login
 *
 * @copyright   Copyright (C) 2005 - 2014 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
    
	
?>
<div class="login-profile">
    <form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" id="login-form" class="form-vertical">
    <?php if ($params->get('greeting')) : ?>

        <div class="login-greeting">
        <?php if ($params->get('name') == 0) : {
            echo JText::sprintf('MOD_LOGIN_HINAME', htmlspecialchars($user->get('name')));
        } else : {
            echo JText::sprintf('MOD_LOGIN_HINAME', htmlspecialchars($user->get('username')));
        } endif; ?>
        </div>
    <?php endif; ?>

    <div class="dropdown list-profiles">
        <button class="btn btn-default dropdown-toggle" type="button" id="LoginList" data-toggle="dropdown"><span class="fa fa-user"></span><?php echo JText::_('MOD_LOGIN_MY_ACCOUNT'); ?><span class="fa fa-angle-down"></span>
        </button>
            <ul class="dropdown-menu" role="menu" aria-labelledby="LoginList">
                <li>
					<a href="<?php echo JRoute::_('index.php?option=com_users&view=profile&Itemid=201'); ?>">
						<?php echo JText::_('MOD_LOGIN_MY_PROFILE'); ?></a>
				</li>
				<li>
					<a href="<?php echo JRoute::_('index.php?option=com_virtuemart&view=orders&layout=list&Itemid=1528'); ?>">
						<?php echo JText::_('MOD_LOGIN_LIST_ORDERS'); ?></a>
				</li>
                <li>
					<a href="<?php echo JRoute::_('index.php?option=com_virtuemart&view=user&layout=edit'); ?>">
						<?php echo JText::_('MOD_LOGIN_ACCOUNT_DETAIL'); ?></a>
				</li>
                <li><a title="Address Book" href="#">Address Book</a></li>
                <li><a title="My Tags" href="#">My Tags</a></li>
                <li><a title="Billing Agreements" href="#">Billing Agreements</a></li>
                <li><a title="Recurring Profiles" href="#">Recurring Profiles</a></li>
				<li class="logout-button">
		
				<button type="submit" name="Submit" class="btn"><?php echo JText::_('MOD_LOGIN_LOGOUT'); ?></button>
					<input type="hidden" name="option" value="com_users" />
					<input type="hidden" name="task" value="user.logout" />
					<input type="hidden" name="return" value="<?php echo $return; ?>" />
					<?php echo JHtml::_('form.token'); ?>
				</li>

			</ul>
    </div>
    </form>
</div>