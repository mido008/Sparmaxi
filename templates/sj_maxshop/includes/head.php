<?php
/*
 * ------------------------------------------------------------------------
 * Copyright (C) 2009 - 2013 The YouTech JSC. All Rights Reserved.
 * @license - GNU/GPL, http://www.gnu.org/licenses/gpl.html
 * Author: The YouTech JSC
 * Websites: http://www.smartaddons.com - http://www.cmsportal.net
 * ------------------------------------------------------------------------
*/

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );
?>

<?php
if($yt->getParam('enableGoogleAnalytics')=='1' && $yt->getParam('googleAnalyticsTrackingID')!='' ){ ?>
	<!--For param enableGoogleAnalytics-->
	<script type="text/javascript">
        var _gaq = _gaq || [];
        _gaq.push(["_setAccount", "<?php echo $yt->getParam('googleAnalyticsTrackingID')?>"]);
        _gaq.push(["_trackPageview"]);
        (function() {
        var ga = document.createElement("script"); ga.type = "text/javascript"; ga.async = true;
        ga.src = ("https:" == document.location.protocol ? "https://ssl" : "http://www") + ".google-analytics.com/ga.js";
        var s = document.getElementsByTagName("script")[0]; s.parentNode.insertBefore(ga, s);
        })();
        
    </script>
	
<?php
} 
?>
<!-- Facebook Pixel Code -->
<script>
!function(f,b,e,v,n,t,s){if(f.fbq)return;n=f.fbq=function(){n.callMethod?
n.callMethod.apply(n,arguments):n.queue.push(arguments)};if(!f._fbq)f._fbq=n;
n.push=n;n.loaded=!0;n.version='2.0';n.queue=[];t=b.createElement(e);t.async=!0;
t.src=v;s=b.getElementsByTagName(e)[0];s.parentNode.insertBefore(t,s)}(window,
document,'script','//connect.facebook.net/en_US/fbevents.js');

fbq('init', '545495998948737');
fbq('track', "PageView");
fbq('track', 'Purchase', {value: '0.00', currency: 'EUR'});
</script>
<noscript><img height="1" width="1" style="display:none"
src="https://www.facebook.com/tr?id=545495998948737&ev=PageView&noscript=1"
/></noscript>
<!-- End Facebook Pixel Code -->

<script>
  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');

  ga('create', 'UA-73210646-1', 'auto');
  ga('send', 'pageview');

</script>

