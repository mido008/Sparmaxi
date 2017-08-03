;(function($){
	$.fn.extend({
		jsmart_listing: function(options){
			var defaults = {
				duration: 0
			};
			var options =  $.extend(defaults, options);
			
			return $(this).each(function(){
				var $this = $(this);
				var $content = $('.lt-items', $this);
				var $content_page = $('.lt-content-page', $content);
				var $pages = $('li.lt-page', $this);
				var $number_of_page = $pages.length - 2;
				
				var locatDesc = function(){
						$('.lt-item' , $content_page.filter('.curr')).each(function(){
							var $this = $(this);
							if( (($this.offset().left + $this.width()) > ($(window).width()/2))){
								if(($(window).width()-($this.offset().left + $this.width())) < $this.offset().left ){
									$('.lt-arrow , .lt-desc',$this).removeClass('lt-right').addClass('lt-left');
								}
							}
					  });
				}
				
				locatDesc();
				$pages.each(function(){
					$content.data('current',0);
					$(this).click(function(){
						if($(this).hasClass('active')){
							return;
						}
						var $index = false;
						
						if($(this).hasClass('lt-next')){
							$index = $content.data('current');
							$index = $index + 1;
							if($index + 1 > $number_of_page){
								$index = 0;
							}
						}
						else if($(this).hasClass('lt-previous')){
							$index = $content.data('current');
							$index = $index - 1;
							if($index < 0 ){
								$index = $number_of_page - 1;
							}
						}else {
							$index = $pages.index($(this));
							$index = $index - 1;
						}
						
						var $page_active = $pages.filter('.lt-page-'+$index);
						var $page_curr = $content_page.eq($index);
						$pages.removeClass('active');
						$page_active.addClass('active');
						$content.data('current',$index);
						$content.css({
							opacity: 0
						}).animate({
							opacity: 1
						},{
							duration: options.duration,
							queue: false
						});
						$content_page.removeClass('curr');
						$page_curr.addClass('curr');
						locatDesc();
					});
				});
			});
		}
	});
})($jsmart||jQuery);