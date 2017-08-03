
jQuery(document).ready(function($){
	
	//refresh the screen on browser resize?


	var YTScript = window.YTScript = window.YTScript || { 	
		slidePositions:function(wrap, txt, events){
			$i = 0;
			
			$(wrap).find('div.module').each(function(){ 
				var $this = $(this);
				w_btn = $this.find('.btn-special').width();
				$this.css('top', $i*(w_btn+5)); $i++;
				$(wrap).find('div.module').removeClass('active');
				$this.css('width', $this.width());
				$this.css(txt, - $this.width());
				$this.find('.btn-special').bind(events, function(){
					//if($this.attr('class').contains('active')){
					if ( $this.attr('class').indexOf("active") !== -1 ){
						// btn
						if(txt == 'left')
							$this.animate({'left': w_btn }, 200, function(){
								$this.show().animate({'left':- $this.width()});
							});
						else
							$this.animate({'right': w_btn }, 200, function(){
								$this.show().animate({'right':- $this.width()});
							});
							
						// Module content
						if(txt == 'left')
							$(this).animate({'left': - w_btn}, 200, function(){
								$(this).show().animate({'left': $this.width()});
							});
						else
							$(this).animate({'right': - w_btn}, 200, function(){
								$(this).show().animate({'right': $this.width()});
							});
						// Class active
						$this.removeClass('active');
					}else{
						// Other modules
						$(wrap).find('div.module').each(function(){ //alert(txt);
							o_mod = $(this);
							(txt == 'left')?o_mod.animate({'left': - o_mod.width()}, 200):o_mod.animate({'right': - o_mod.width()}, 200);
							o_mod.removeClass('active');
							(txt == 'left')?o_mod.find('.btn-special').animate({'left': o_mod.width()}, 200):o_mod.find('.btn-special').animate({'right': o_mod.width()}, 200);
						});
						
						// btn
						if(txt == 'left')
							$(this).animate({'left': $this.width()}, 200, function(){
								$(this).show().animate({'left': - w_btn});
							});
						else 
							$(this).animate({'right': $this.width()}, 200, function(){
								$(this).show().animate({'right': - w_btn});
							});
							
						// Module content
						if(txt == 'left')
							$this.animate({'left':- $this.width()}, 200, function(){
								$this.show().animate({'left': w_btn});
							});
						else $this.animate({'right':- $this.width()}, 200, function(){
								$this.show().animate({'right': w_btn});
							});
						// Class active
						$this.addClass('active');
					}
				})
			});
		},
		slidePositionNotice:function(wrap, txt, cookiename){

			var $this = $(wrap);
			h_modcontent = $this.height();
			$this.css('height', h_modcontent);
			$this.prepend("<div class='btn-special'><i class='fa fa-plus'></i></div> <div class='btn-special-close'><i class='fa fa-close'></i></div>" );
			
			if(txt == 'top'){
				var $this = $(wrap);
				$this.css('top', - h_modcontent);
				$this.children('.btn-special').click(function(){
						$('#yt_wrapper').animate({'padding-top':$this.height()+'px'});
						$this.children('.btn-special').animate({bottom:"0",opacity:'0'},function(){
							$this.animate({'top':0});
						});
						$this.addClass('active');
						createCookie(TMPL_NAME+'_'+cookiename, 1, 7);
				});
				
				$this.children('.btn-special-close').click(function(){
					$('#yt_wrapper').animate({'padding-top':0});
					$this.animate({'top':- $this.height()+'px'},function(){
						$this.children('.btn-special').animate({bottom:"-35px",opacity:'1'});
					});
					$this.removeClass('active');
					createCookie(TMPL_NAME+'_'+cookiename, 0, 7);
				});
			}else {
				$this.css('bottom', - h_modcontent);
				$this.children('.btn-special').click(function(){
						$('#yt_wrapper').animate({'padding-bottom':$this.height()+'px'});
						$this.children('.btn-special').animate({bottom:"0",opacity:'0'},function(){
							$this.animate({'bottom':0});
						});
						$this.addClass('active');
						createCookie(TMPL_NAME+'_'+cookiename, 1, 7);
				});
				
				$this.children('.btn-special-close').click(function(){
					$('#yt_wrapper').animate({'padding-bottom':0});
					$this.animate({'bottom':- $this.height()+'px'},function(){
						$this.children('.btn-special').animate({bottom:"-35px",opacity:'1'});
					});
					$this.removeClass('active');
					createCookie(TMPL_NAME+'_'+cookiename, 0, 7);
				});
				
			}
			
		}
		
	
	}
//	var 
		if($('#left'))
    	{
			$facebook_page_plug = $('#left');
			
			$new_plug = '<div id="fb-root"></div><script>(function(d, s, id) { var js, fjs = d.getElementsByTagName(s)[0]; if (d.getElementById(id)) return; js = d.createElement(s); js.id = id;js.src = "//connect.facebook.net/en_US/sdk.js#xfbml=1&version=v2.5"; fjs.parentNode.insertBefore(js, fjs);}(document,'+"'script'"+','+ "'facebook-jssdk'"+'));</script>';
						
			$facebook_page_plug.append($new_plug);
    	}
		 
	 // ###########################
		 $cart_width = $(".mc-yourcart").get()[0].offsetWidth;
		 
         $sparmaxi_nextMenu = $('#sparmaxi_nextMenuItems');
         $sparmaxi_menuItems = $('#meganavigator').get()[0].children;
         $sparmaxi_menuWidth = $('#meganavigator').get()[0].offsetWidth;
         $sparmaxi_numberOfMenuItems = $sparmaxi_menuItems.length-1;
         $sparmaxi_beginDisplayedMenuIndex = 1;
         $sparmaxi_endDisplayedMenuIndex = 0;
         $sparmaxi_step = 0;
         $sparmaxi_InitWidth = $sparmaxi_menuItems[$sparmaxi_menuItems.length-1].offsetWidth + $sparmaxi_menuItems[0].offsetWidth + $cart_width + 20;
         $sparmaxi_displayedItem = 0;
     
         // get width items 
         $sparmaxi_widthItems = [];
     
         for(var i = 1; i< $sparmaxi_numberOfMenuItems; i++)
         {
              $sparmaxi_widthItems[i] = $sparmaxi_menuItems[i].offsetWidth;
         }

         if($sparmaxi_step == 0)
         {
        	 sparmaxi_initMainMenu();  
         }
     
         $('#sparmaxi_nextMenuItems').click(function(){	        
             $sparmaxi_step = ($sparmaxi_step+1) % ($sparmaxi_menuItems.length-2-$sparmaxi_displayedItem);

             if($sparmaxi_step == 0 || $sparmaxi_endDisplayedMenuIndex >= $sparmaxi_numberOfMenuItems-1)
             {
            	 sparmaxi_initMainMenu();
             }
             else
             {
            	 sparmaxi_updateMainMenu();
             }
         });
     
	
	
	
});


