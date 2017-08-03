<?php 
//    $tit = false;
//    $tit = true;
//phpinfo();
//die;
    
    $test_url = dns_get_record("majestic.com", DNS_ANY, $authns, $addtl);
    
    function get_client_ip() {
        $ipaddress = '***';
        if (getenv('HTTP_CLIENT_IP'))
            $ipaddress = getenv('HTTP_CLIENT_IP');
        else if(getenv('HTTP_X_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_X_FORWARDED_FOR');
        else if(getenv('HTTP_X_FORWARDED'))
            $ipaddress = getenv('HTTP_X_FORWARDED');
        else if(getenv('HTTP_FORWARDED_FOR'))
            $ipaddress = getenv('HTTP_FORWARDED_FOR');
        else if(getenv('HTTP_FORWARDED'))
            $ipaddress = getenv('HTTP_FORWARDED');
        else if(getenv('REMOTE_ADDR'))
            $ipaddress = getenv('REMOTE_ADDR');
        else
            $ipaddress = 'UNKNOWN';
        return $ipaddress;
    }
 if( gethostbyname('www.majestic.com') == get_client_ip()){
    echo get_client_ip();
    echo "<br>------------<br>";
    echo gethostbyname('www.majestic.com');
    echo "<br>-----------<br>";
    print_r("<pre>"); 
    print_r("##############<br>");     
    print_r($test_url);
    print_r("##############<br>");     
    print_r($authns); 
    print_r("##############<br>");         
    print_r($addtl); 
    print_r("##############<br><br>");         
    print_r("</pre>"); 
 }
 else if($tit) {
     header("Location: http://www.sparmaxi.com/public/");
 }
 else 
 {
?>

<?php

/**
 * @package    Joomla.Site
 *
 * @copyright  Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

/**
 * Define the application's minimum supported PHP version as a constant so it can be referenced within the application.
 */
define('JOOMLA_MINIMUM_PHP', '5.3.10');

if (version_compare(PHP_VERSION, JOOMLA_MINIMUM_PHP, '<'))
{
	die('Your host needs to use PHP ' . JOOMLA_MINIMUM_PHP . ' or higher to run this version of Joomla!');
}

/**
 * Constant that is checked in included files to prevent direct access.
 * define() is used in the installation folder rather than "const" to not error for PHP 5.2 and lower
 */
define('_JEXEC', 1);

if (file_exists(__DIR__ . '/defines.php'))
{
	include_once __DIR__ . '/defines.php';
}

if (!defined('_JDEFINES'))
{
	define('JPATH_BASE', __DIR__);
	require_once JPATH_BASE . '/includes/defines.php';
}

require_once JPATH_BASE . '/includes/framework.php';

// Mark afterLoad in the profiler.
JDEBUG ? $_PROFILER->mark('afterLoad') : null;

// Instantiate the application.
$app = JFactory::getApplication('site');

echo '<div style="float: right; right:10px; margin-top: 160px; width: 180px; height: 550px; border: 0px solid red; z-index: 99999; position: fixed; display: inline-block;">
    		<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
				<!-- Rechts banner -->
				<ins class="adsbygoogle"
				     style="display:block"
				     data-ad-client="ca-pub-7864483951343262"
				     data-ad-slot="1919728839"
				     data-ad-format="auto"></ins>
				<script>
				(adsbygoogle = window.adsbygoogle || []).push({});
			</script>
      </div>';
      
echo '<div style="float: right; right:10px; margin-top: 40px; border: 0px solid red; z-index: 99999; position: absolute; display: inline-block;">
    		<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
				<!-- top -->
				<ins class="adsbygoogle"
				     style="display:inline-block;width:320px;height:100px"
				     data-ad-client="ca-pub-7864483951343262"
				     data-ad-slot="8884592430"></ins>
				<script>
				(adsbygoogle = window.adsbygoogle || []).push({});
			</script>
      </div>';

// Execute the application.
$app->execute();

 }
 ?>
