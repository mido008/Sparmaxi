<?php
/**
 * @package		Joomla.Site
 * @subpackage	mod_login
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;
JHtml::_('behavior.keepalive');
?>

<div class="yt-loginform">
	<div class="yt-login">
		
		<div id="myLogin" class="modal fade" tabindex="-1" role="dialog"  aria-hidden="true">
			<div class="modal-dialog">

				<div class="modal-content row">

                    <h3 class="title"><span class="title-inner"><?php echo JText::_('MOD_LOGIN_TITLE'); ?></span></h3>
					<form action="<?php echo JRoute::_('index.php', true, $params->get('usesecure')); ?>" method="post" id="login-form" class="col-sm-6" >
						<?php if ($params->get('pretext')): ?>
							<div class="pretext">
							<p><?php echo $params->get('pretext'); ?></p>
							</div>
						<?php endif; ?>
						<div class="userdata">
							<div id="form-login-username" class="form-group">
								<i class="fa fa-user"></i>
								<input id="modlgn-username" type="text" name="username" class="inputbox"  size="40" placeholder="<?php echo JText::_('MOD_LOGIN_VALUE_USERNAME') ?>" />
							</div>
							<div id="form-login-password" class="form-group">
                                <i class="fa fa-key"></i>
								<input id="modlgn-passwd" type="password" name="password" class="inputbox" size="40" placeholder="<?php echo JText::_('JGLOBAL_PASSWORD') ?>"  />
							</div>
							
							<div id="form-login-remember" class="form-group ">
								<input id="modlgn-remember" type="checkbox" name="remember" value="1"/>
								<label for="modlgn-remember" class="control-label"><?php echo JText::_('MOD_LOGIN_REMEMBER_ME') ?></label> 
							</div>
							
							
							<div id="form-login-submit" class="control-group">
								<div class="controls">
									<button type="submit" tabindex="3" name="Submit" class="button"><i class="fa fa-lock"></i><?php echo JText::_('JLOGIN') ?></button>
								</div>
							</div>
							
							<input type="hidden" name="option" value="com_users" />
							<input type="hidden" name="task" value="user.login" />
							<input type="hidden" name="return" value="<?php echo $return; ?>" />
							<?php echo JHtml::_('form.token'); ?>
						</div>
						<ul class="listinline listlogin">
							<li>
								<a href="<?php echo JRoute::_('index.php?option=com_users&view=reset'); ?>">
								<?php echo JText::_('MOD_LOGIN_FORGOT_YOUR_PASSWORD'); ?></a>
							</li>
							
						</ul>
						<?php if ($params->get('posttext')): ?>
							<div class="posttext">
								<p><?php echo $params->get('posttext'); ?></p>
							</div>
						<?php endif; ?>
						
					</form>
                    <div class="login-right col-sm-6">
						<h3><?php echo JText::_('MOD_LOGIN_NEW_HERE'); ?></h3>
						<p><?php echo JText::_('MOD_LOGIN_REGISTRATION_IS_FREE_AND_EASY'); ?></p>
						<ul>
							<li>
								<?php echo JText::_('MOD_LOGIN_FASTER_CHECKOUT'); ?>
							</li>
							<li>
								<?php echo JText::_('MOD_LOGIN_SAVE_MULTIPLE_SHIPPING_ADDRESSES'); ?>
							</li>
							<li>
								<?php echo JText::_('MOD_LOGIN_VIEW_AND_TRACK_ORDERS_AND_MORE'); ?>
							</li>
							</ul>
						<a href="<?php echo JRoute::_("index.php?option=com_users&view=registration");?>" onclick="showBox('yt_register_box','jform_name',this, window.event || event);return false;" class="btReverse">Create an account</a>
                    </div>
				</div>
			</div>
		</div>
		<a class="login-switch" data-toggle="modal" href="#myLogin" title="<?php JText::_('ME_LOGIN');?>">
		  <!--img data-placeholder="no" src="templates/<?php echo $app->getTemplate();?>/html/mod_login/images/user.png" alt="" /-->
		  <span class="fa fa-lock"></span><?php echo JText::_('ME_LOGIN');?>
		</a>

	</div>
	
</div>

