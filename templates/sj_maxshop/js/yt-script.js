// JavaScript Document
function createCookie(name,value,days) {
  if (days) {
    var date = new Date();
    date.setTime(date.getTime()+(days*24*60*60*1000));
    var expires = "; expires="+date.toGMTString();
  }
  else expires = "";
  document.cookie = name+"="+value+expires+"; path=/";
}

function getCookie(c_name, defaultvalue){	//alert(document.cookie);
	var i,x,y,arrcookies=document.cookie.split(";");
	for (i=0;i<arrcookies.length;i++){
	  x=arrcookies[i].substr(0,arrcookies[i].indexOf("="));
	  y=arrcookies[i].substr(arrcookies[i].indexOf("=")+1);
	  x=x.replace(/^\s+|\s+$/g,"");
	  if (x==c_name){
		  return unescape(y);
	  }
	}
	return defaultvalue;
}

//


function sparmaxi_initMainMenu()
{
	$sparmaxi_beginDisplayedMenuIndex = 1;
    $sparmaxi_EndDisplayedMenuIndex = 0;
    $sparmaxi_displayedItem = 0;
    $sparmaxi_numberOfDisabledItems = 0;
    var $sparmaxi_curretWidth = $sparmaxi_InitWidth; 
         
    for(var i = 1; i< $sparmaxi_numberOfMenuItems; i++)
    {
//    	console.log($sparmaxi_menuItems[i]);
    	if(($sparmaxi_curretWidth+$sparmaxi_widthItems[i]) <= $sparmaxi_menuWidth && $sparmaxi_numberOfDisabledItems == 0)
    	{
    		$sparmaxi_menuItems[i].style.display = "block";
            $sparmaxi_curretWidth = $sparmaxi_curretWidth+$sparmaxi_widthItems[i];
            $sparmaxi_displayedItem++;
            $sparmaxi_endDisplayedMenuIndex = i;
        }
        else
        {   
        	$sparmaxi_numberOfDisabledItems++;
        	$sparmaxi_menuItems[i].style.display = "none";
//            $sparmaxi_menuItems[i].hide();
        }
    }
          
    $deff = ((($sparmaxi_menuWidth-$sparmaxi_curretWidth)/($sparmaxi_numberOfMenuItems-1))/$sparmaxi_displayedItem)+"px";
    for(var i = 1; i< $sparmaxi_numberOfMenuItems; i++)
    {   
        $sparmaxi_menuItems[i].style.paddingLeft = $deff;
    }
//          $next.get()[0].style.paddingLeft = $deff;
}
     
function sparmaxi_updateMainMenu()
{
    var $sparmaxi_curretWidth = $sparmaxi_InitWidth;

    for(var i = 1; i< $sparmaxi_numberOfMenuItems; i++)
    {
    	if(($sparmaxi_curretWidth+$sparmaxi_widthItems[i]) < $sparmaxi_menuWidth && i > $sparmaxi_step)
        {
    		$sparmaxi_menuItems[i].style.display = "block";
//    		$sparmaxi_menuItems[i].show();
    		$sparmaxi_curretWidth = $sparmaxi_curretWidth+$sparmaxi_widthItems[i];
    		$sparmaxi_displayedItem++;
    		$sparmaxi_endDisplayedMenuIndex = i;	                   
    	}
    	else
    	{
    		$sparmaxi_numberOfDisabledItems++;
    		$sparmaxi_menuItems[i].style.display = "none";
//    		$sparmaxi_menuItems[i].hide();
     	}
     }
     
     $deff = ((($sparmaxi_menuWidth-$sparmaxi_curretWidth)/($sparmaxi_numberOfMenuItems-1))/$sparmaxi_displayedItem)+"px";
     
     for(var i = 1; i< $sparmaxi_numberOfMenuItems; i++)
     {   
    	 $sparmaxi_menuItems[i].style.paddingLeft = $deff;
     }
//          $next.get()[0].style.paddingLeft = $deff; 
     
}


