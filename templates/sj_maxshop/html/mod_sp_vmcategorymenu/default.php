<?php

    /**
    * @package SP VirtueMart Category Menu
    * @author JoomShaper http://www.joomshaper.com
    * @copyright Copyright (c) 2010 - 2013 JoomShaper
    * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPLv2 or Later
    */ 

?>
<div class="sp-vmmenu <?php echo $moduleclass_sfx; ?>" id="sp-vmmenu-<?php echo $module_id ?>">
    <ul>
        <?php echo $tree ?>
    </ul>
</div>


<script type="text/javascript">
//<![CDATA[
jQuery(document).ready(function($){
	;(function(element){
		var el = $(element), vf_menu = $('.vf-menu',el), _li = $('.sp-vmmenu .parent', el);
		
		function _vfResponsive() {
			if($(window).width() <= 1024) {
				_li.addClass('vf-close');
			
				vf_button = $('.fa-angle-right',el);
				vf_button.unbind('click touchstart').on('click touchstart', function(e){
					e.preventDefault();
					$(this).parents().next().stop().slideDown();
					$(this).parents(':eq(1)').siblings("li").children("ul").slideUp();
					$(this).addClass(function() {
						if($(this).hasClass("active")) return "";
						return "active";
					});
					$(this).parents(':eq(1)').siblings("li").find(".active").removeClass("active");
				});
				
			}
			
		}
		_vfResponsive();
		$(window).on('resize', function(){
			_vfResponsive();
		});
	})('#vertical-menu');
});
//]]>
</script>	

