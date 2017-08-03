<?php
/**
 * @package Extension Download
 * @version 1.0.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2013 YouTech Company. All Rights Reserved.
 * @author YouTech Company http://www.smartaddons.com
 */


/**
 * return subfolder and file in directory
 */
	$exts_group = array(0=>'modules',
						1=>'plugins_content',
						2=>'plugins_system',
						3=>'templates',
						4=>'quickstart'
						); 
	
	/**
     * Get list folder
     *
     * @param     string              $dir       path directory
     *
     * @return    array               $dir      
    */
	function getFolder($dir , $type = 'folder'){
		$result = array();
		$items = scandir($dir);
		
		if(!empty($items)){
			foreach($items as $key => $item){
				if(!in_array($item,array(".",".."))){
					
					if($type == 'folder') {
						if(is_dir($dir.DIRECTORY_SEPARATOR.$item)){
							$result[] = $item;
							
						}
					}else{
						if(is_file($dir.DIRECTORY_SEPARATOR.$item)){
							$result[] = $item;
						}
					}
				}
			}
		}
		return $result;
	}

	/**
     * Remove charter('mod','_')
     *
     * @param     string              $str      
     *
     * @return    string              $_str      
    */
	function _ucWords($str){
		$_str = $str;
		if($str != ''){
			if(strpos($str, '_')) {
				$_str = str_replace('_', ' ', $str);
				$_str = str_replace('mod ', '', $_str);
			}
			$_str = ucwords($_str);
		}
		return $_str;
	}

	/**
     * zip gop lai tat ca folder
     *
     * @param     string              $folder_path       path directory
	 * @param     string              $local_path        path directory
	 * @z     	  string              $local_path        path directory
     *
     * @return    array               $dir      
     */
	function addAll($folder_path,$local_path,$z){
		if (is_dir($folder_path)){
			$dh=opendir($folder_path);
			
			while (($file = readdir($dh)) !== false) {
				if( ($file !== ".") && ($file !== "..") && $file !=="sjtool" && $file !=="placehold_img" && $file !=="configuration.php" && $file !=="extensions.php" && strpos($file, '.svn') === false){
					if (is_file($folder_path.$file)){
						$z->addFile($folder_path.$file,$local_path.$file);
					}else{
						addAll($folder_path.$file."/",$local_path.$file."/",$z);
					}
				}
			}
			
		}else{
			echo "The directory $folder_path not exists.";
			exit;
		}
	}
	
	/**
     * Check if string contains a value in array
     */
	function contains($str, array $arr){
		foreach($arr as $a) {
			$place = strpos( $a ,$str);
			if (!empty($place)) {
				return $a;
			} 
		}
	}
	
	/**
     * zip group file
     *
     * @param     string              $type      	 single/group
	 * @param     string              $ext_name      name extenstion
	 * @param     string              $name_gr       name group
     *
     * @return    array               $dir      
     */
	function zipFileOuput($type,$ext_name,$name_gr = '', $read_me = ''){
		
		$folder = 'exts_dowload_tmp';
		$file_path_group = null;
		if($type == 'single'){
			$file_path = getFolder($folder , '');
			$file_path_name  = contains('zip',$file_path);
			$file_path_group =  $folder.'/'.$file_path_name;
			
		}else{
			if($name_gr != ''){
				$file_path_group = $folder.'/'.$name_gr.'.zip';
			}else{
				$file_path_group = $folder.'/please_rename_UNZIPFIRST.zip';
			}
			if($read_me != '') {
				$fileLocation = $folder. "/readme.txt";
				$file = fopen($fileLocation,"w");
				$content = $read_me;
				fwrite($file,$content);
				fclose($file);
			}
			
			$zip = new ZipArchive();
			
			if ($zip->open($file_path_group, ZIPARCHIVE::CREATE) === true) {
				if(!file_exists($file_path_group)){
					addAll($folder.'/',"",$zip);
				}
				$zip->close();
			}
			
		}
		
		if(file_exists($file_path_group)){
			ob_end_clean();
			
			header("Content-type: application/zip;\n");
			header("Content-Transfer-Encoding: Binary");
			header("Content-length: ".filesize($file_path_group).";\n");
			header("Content-disposition: attachment; filename=\"".basename($file_path_group)."\"");
			readfile($file_path_group);
		}else {
			exit("Could not find Zip file to download");
		}
		
		
		_delelteFolder($folder);
		return true;
		
	}
	
	/**
     * rename file
     *
     * @param     string              $oldfile      path directory
	 * @param     string              $newfile      path directory
     *     
    */
	function rename_win($oldfile,$newfile) {
	   if (!rename($oldfile,$newfile)) {
		  if (copy ($oldfile,$newfile)) {
			 unlink($oldfile);
			 return TRUE;
		  }
		  return FALSE;
	   }
	   return TRUE;
	}
	
	/**
     * delelte Folder
     *
     * @param     string              $folder      path directory
     *     
    */
	function _delelteFolder($folder){
		
		if(is_dir($folder)){
			$folder_handler = dir($folder);
			while ($file = $folder_handler->read()) {
				
				if ($file != "." && $file != "..") {
					if (filetype($folder."/".$file) == "dir") {
						_delelteFolder($folder."/".$file);
					} else { 
						unlink($folder."/".$file);
					}					
				}

			}
			$folder_handler->close();
			rmdir($folder);
			
		}
	}
	
	/**
     * Copy Folder
     *
     * @param     string              $source      path directory
     *     
    */
	function copydir($source,$destination){
		if(!is_dir($destination)){
			$oldumask = umask(0); 
			mkdir($destination, 01777); // so you get the sticky bit set 
			umask($oldumask);
		}	
		$dir_handle = @opendir($source);
		if ( $dir_handle != false ){
			while ($file = readdir($dir_handle)) 
			{
				if($file!="." && $file!=".." && !is_dir("$source/$file")) //if it is 				
					copy("$source/$file","$destination/$file");
				if($file!="." && $file!=".." && is_dir("$source/$file")) //if it is folder				
					copydir("$source/$file","$destination/$file");
			}
			closedir($dir_handle);
		}
	}	
	
	/**
     * check các exten,temp
     *
     * @param     string              $gr      path directory
	 * @param     string              $ext_name      path directory
     *     
    */
	function _proGeneral($gr = 'mod', $ext_name){
		$folder = 'exts_dowload_tmp';
		$jversion = null;
		if(!file_exists($folder)){
			mkdir ($folder, 0777);
		}

		if(!defined('_JEXEC')){
			define('_JEXEC',1) ;
		}
		
		if (!defined('JPATH_PLATFORM'))
		{
			define('JPATH_PLATFORM',__DIR__);
		}

		if(file_exists('libraries/cms/version/version.php')) {
			if(!class_exists('JVersion')) {
				require_once dirname(__FILE__).'/libraries/cms/version/version.php';
			}
			$version = new JVersion();
			$jversion = $version->RELEASE;
			
		}		
		$file_name = $ext_name;
		
		switch($gr){
			case 'mod':
				$xml = simplexml_load_file('modules/'.$file_name.'/'.$file_name.'.xml');
				$_file_name = $prefix.$file_name.'_v'.$xml->version;;
				$file_path= $folder.'/'.$_file_name.'.zip';
				
				$zip = new ZipArchive();
				if ($zip->open($file_path, ZIPARCHIVE::CREATE) === true) {
					if(!file_exists($file_path)){
						addAll("modules/".$file_name."/","",$zip);
					}
					$zip->close();
				}
				break;
			case 'pls':
				$path = 'plugins/system/';	
				$prefix = 'plg_system_';
				$folder_plc = $path.$file_name;
				$xml = simplexml_load_file($folder_plc.'/'.$file_name.'.xml');
				
				$_file_name = $prefix.$file_name.'_v'.$xml->version;;
				$file_path= $folder.'/'.$_file_name.'.zip';
				
				$zip = new ZipArchive();
				if ($zip->open($file_path, ZIPARCHIVE::CREATE) === true) {
					if(!file_exists($file_path)){
						addAll($path.$file_name."/","",$zip);
					}
					$zip->close();
				}
				break;
				
			case 'plc':
				$path = 'plugins/content/';	
				
				$folder_plc = $path.$file_name;
				$xml = simplexml_load_file($folder_plc.'/'.$file_name.'.xml');
				$_file_name = $prefix.$file_name.'_v'.$xml->version;
				$file_path= $folder.'/'.$_file_name.'.zip';
				
				$zip = new ZipArchive();
				if ($zip->open($file_path, ZIPARCHIVE::CREATE) === true) {
					if(!file_exists($file_path)){
						addAll($path.$file_name."/","",$zip);
					}
					$zip->close();
				}
				break;
			case 'tmp':
				$path = 'templates/';	
				$prefix = '.tpl_';
				
				$folder_plc = $path.$file_name;
				if($file_name == 'system'){echo 'Can not download this file';exit;}
				
				$xml 			= simplexml_load_file($folder_plc.'/templateDetails.xml');
				$_file_name = _getVersion($file_name,$jversion, $xml,$gr);
				$lag = $xml->languages->language['tag'];
				$folder_lag = $path.$file_name.'/language';
				
				if($lag != null && !file_exists($folder_lag)){
					$srcfile 	= 'language/'.$lag.'/'.$lag.$prefix.$file_name.'.ini';
					$srcfile1	= 'language/'.$lag.'/'.$lag.$prefix.$file_name.'.sys.ini';
					
					$src_index = $folder_plc.'/index.html';
					$dstfile = $folder_lag.'/'.$lag.'/'.$lag.$prefix.$file_name.'.ini';
					$dstfile1 = $folder_lag.'/'.$lag.'/'.$lag.$prefix.$file_name.'.sys.ini';
					
					$dh = opendir($folder_plc);
					while (($file = readdir($dh)) !== false) {
						if( ($file !== ".") && ($file !== "..")){
							if(!file_exists($folder_lag)){
								mkdir ($folder_lag, 0777);
								mkdir ($folder_lag.'/'.$lag, 0777);
								copy($src_index, $folder_lag.'/index.html');
								copy($src_index, $folder_lag.'/'.$lag.'/index.html');
								copy($srcfile, $dstfile);
								copy($srcfile1, $dstfile1);
							}
						}

					}
				}
				
				$file_path= $folder.'/'.$_file_name.'.zip';
				$zip = new ZipArchive();
				if ($zip->open($file_path, ZIPARCHIVE::CREATE) === true) {
					if(!file_exists($file_path)){
						addAll($path.$file_name."/","",$zip);
					}
					$zip->close();
				}
				
				break;
				
			case 'combo':
				
				$path = 'templates/';	
				$prefix = '.tpl_';
				
				$folder_plc = $path.$file_name;
				if($file_name == 'system'){
					echo 'Can not download this file';
					exit;
				}
				
				$xml 			= simplexml_load_file($folder_plc.'/templateDetails.xml');
				$xml_yt 		= simplexml_load_file('plugins/system/yt/yt.xml');
				$xml_shortcode	= simplexml_load_file('plugins/system/ytshortcodes/ytshortcodes.xml');
				
				$_file_name = _getVersion($file_name,$jversion, $xml,$gr);
				$lag = $xml->languages->language['tag'];
				$folder_lag = $path.$file_name.'/language';
				if($lag != null && !file_exists($folder_lag)){
					
					$srcfile 	= 'language/'.$lag.'/'.$lag.$prefix.$file_name.'.ini';
					$srcfile1	= 'language/'.$lag.'/'.$lag.$prefix.$file_name.'.sys.ini';
					
					$src_index = $folder_plc.'/index.html';
					$dstfile = $folder_lag.'/'.$lag.'/'.$lag.$prefix.$file_name.'.ini';
					$dstfile1 = $folder_lag.'/'.$lag.'/'.$lag.$prefix.$file_name.'.sys.ini';
					$dh = opendir($folder_plc);
					while (($file = readdir($dh)) !== false) {
						if( ($file !== ".") && ($file !== "..")){
							if(!file_exists($folder_lag)){
								mkdir ($folder_lag, 0777);
								mkdir ($folder_lag.'/'.$lag, 0777);
								copy($src_index, $folder_lag.'/index.html');
								copy($src_index, $folder_lag.'/'.$lag.'/index.html');
								copy($srcfile, $dstfile);
								copy($srcfile1, $dstfile1);
							}
						}

					}
				}
				
				//override installer.xml 
				$doc = new DOMDocument();
				$src_install 		= 'sjtool/';
				$doc->load( $src_install.'/pkg_yt.xml' );
				
				$xp = new DomXPath($doc);
				$nodes = $xp->query("//description");
				
				
				$node = $nodes->item(0);
				$fragment = $doc->createDocumentFragment();
				$fragment->appendXML('<![CDATA[    	
					<div class="alert" style="margin: 10px 0px 30px; padding:20px; ">
						<a style="display:block;padding-bottom:30px; text-align:center;" href="http://www.smartaddons.com"><img src="http://www.smartaddons.com/images/smartaddons/logoSmartaddons.png" ></a>
						<div class="alert alert-success ">	<i class="fa fa-check"></i> '._ucWords($file_name).' Templates '.$xml->version.' installation was successful</div>
						<div class="alert alert-success ">	<i class="fa fa-check"></i> YT Framework plugin v'.$xml_yt->version.' installation was successful</div>
						<div class="alert alert-success ">	<i class="fa fa-check"></i> YT Shortcode plugin v'.$xml_shortcode->version.' installation was successful</div>
					</div>	
				]]>	
				');
				
				$node-> appendChild($fragment);
				$res = $xp->query("//*[@type = 'template']");
				$res ->item(0)->setAttribute("id", $file_name);
				$res ->item(0)->nodeValue = $file_name;
				
				$node_name = $xp->query("//name");
				$node_name ->item(0)->nodeValue ='Package - '._ucWords($file_name);
				$node_pkgname = $xp->query("//packagename");
				$node_pkgname ->item(0)->nodeValue = $file_name;
				$node_script = $xp->query("//scriptfile");
				$node_script ->item(0)->nodeValue = 'pkg_'.$file_name.'.script';
				
				
				$doc->save($src_install.'pkg_yt.xml');
				
				//Creative folder of exts_dowload_tmp/package 
				$package 				= $folder.'/source';				
				$path_theme_src 		= $package.'/'.$file_name;
				$path_yt_src 			= $package.'/yt';
				$path_ytshortcodes_src 	= $package.'/ytshortcodes';
				$path_plugins 			= 'plugins/system/';	
				
				if(!file_exists($package))				 mkdir($package, 0777);
				if(!file_exists($path_theme_src))		 mkdir($path_theme_src, 0777, true);
				if(!file_exists($path_yt_src))			 mkdir($path_yt_src, 0777, true);
				if(!file_exists($path_ytshortcodes_src)) mkdir($path_ytshortcodes_src, 0777, true);
				
				//Copy folder themes & exension
				copydir($src_install, $folder);	
				$oldnamexml 	= $folder. '/pkg_yt.xml';
				$oldnamescript 	= $folder. '/pkg_yt.script.php';
				$newnamexml 	= $folder.'/'.'pkg_'.$file_name.'.xml';
				$newnamescript 	= $folder.'/'.'pkg_'.$file_name.'.script.php';
				if (file_exists($oldnamexml)) {
					rename_win($oldnamexml, $newnamexml );
					rename_win($oldnamescript, $newnamescript );
				}
				
				copydir($path.$file_name, $path_theme_src);	
				copydir($path_plugins.'yt', $path_yt_src);	
				copydir($path_plugins.'ytshortcodes', $path_ytshortcodes_src);	
				
				
				$file_path= $folder.'/'.$_file_name.'.zip';
				$zip = new ZipArchive();
				if ($zip->open($file_path, ZIPARCHIVE::CREATE) === true) {
					if(!file_exists($file_path)){
						addAll($folder."/","",$zip);
					}
					$zip->close();
				}
				
				//Remove description pkg_yt.xml 
				$nodes ->item(0)->nodeValue = '';
				$doc->save($src_install.'pkg_yt.xml');
				
				break;	
			case 'quick':	
			
				$templ	= dirname(dirname(realpath(__FILE__)))."\\".$file_name;
				$templ	= str_replace('\\', '/', $templ);
				 
				$path = $templ.'/templates/';	
				$folder_plc = $path.$file_name;
				$xml = simplexml_load_file($folder_plc.'/templateDetails.xml');
				
				//Delete folder
				$delete_path_cache   =  $templ.'/cache';
				$delete_path_tmp   = $templ. '/tmp';
				$delete_path_resized = $templ. '/yt-assets';
				_delelteFolder($delete_path_cache);	
				_delelteFolder($delete_path_resized);
				_delelteFolder($delete_path_tmp);	
				
				$oldname = $templ. '/~installation';
				$newname = $templ.'/installation';
				if (file_exists($oldname)) {
					$renameResult = rename_win($oldname, $newname );
				}
				
				$_file_name = _getVersion($file_name,$jversion, $xml,$gr);
				
				$file_path= $folder.'/'.$_file_name.'.zip';
			
				$zip = new ZipArchive();
				if ($zip->open($file_path, ZIPARCHIVE::CREATE) === true) {
					if(!file_exists($file_path)){
						addAll(dirname(dirname(realpath(__FILE__))).'\\'.$file_name.'\\'," ",$zip);
					}
					
					$zip->close();
				}

				break;
			default:
		}
	}
	
	/**
     * Get version module file .xml
     *
     * @param     string              $file_name      
	 * @param     string              $jversion      
	 * @param     string              $xml     
     *     
    */
	function _getVersion($file_name,$jversion = null,$xml,$gr = null){
		if($gr == 'tmp') $file_name = $file_name.'_template';
		else if ($gr == 'combo')$file_name = $file_name.'_template';
		else if ($gr == 'quick')$file_name = $file_name.'_quickstart';
		else if ($gr == 'pls')$file_name = 'plg_system_'.$file_name;
		else if ($gr == 'plc')$file_name = 'plg_content_'.$file_name;
		
		if($jversion != null) {
			$_file_name = $file_name.'_j'.$jversion.'_v'.$xml->version;
		}else{
			$_file_name = $file_name.'_v'.$xml->version;
		}
		return $_file_name;
	}
	
	
	 //load books from xml to array
     function load($fname){
        $doc= new DOMDocument();
        if($doc->load($fname))  $res= parse($doc);
        else     throw new Exception('error load XML');

        return $res;
     }


    function parse($doc){
        $xpath = new DOMXpath($doc);
        $items = $xpath->query("book");
        $result = array();
        foreach($items as $item)
        {
           $result[]=array('fields'=>parse_fields($item));
        }
        return $result;
    }


    function parse_fields($node) {
        $res=array();
        foreach($node->childNodes as $child)
        {
           if($child->nodeType==XML_ELEMENT_NODE)
           {
              $res[$child->nodeName]=$child->nodeValue;
           }
        }
        return $res;
     }


     //save array to xml
	 function save($fname, $rows){
        $doc = new DOMDocument('1.0','utf-8');
        $doc->formatOutput = true;

        $books = $doc->appendChild($doc->createElement('books'));

        foreach($rows as $row)
        {
           $book=$books->appendChild($doc->createElement('book'));
           foreach($row['fields'] as $field_name=>$field_value)
           {
              $f=$book->appendChild($doc->createElement($field_name));
              $f->appendChild($doc->createTextNode($field_value));
           }
        }

        file_put_contents($fname, $doc->saveXML());
     }

	 
	 
	if(isset($_POST['submit']) && $_POST['submit'] == 'Download' && !empty($_POST['zip_group']) ) {
		$zip_group = $_POST['zip_group'];
		
		if($zip_group !=''){
			$group = '';
			$ext_name = '';
			foreach($zip_group as $zip){
				$tmp = explode('.',$zip);
				$group = $tmp[0];
				$ext_name = $tmp[1];
				$type = 'single';
				_proGeneral($group,$ext_name);
			}
			if(count($zip_group) > 1) {
				$type = 'group';
			}else{
				$type = 'group';
			}
			$name_gr =  isset($_POST['name-group'])?$_POST['name-group']:'';
			$read_me = isset($_POST['read_me'])?$_POST['read_me']:'';
			 zipFileOuput($type,$ext_name,$name_gr, $read_me);
		}
	}
	
	if(isset($_GET['zipfile'])){
		$file_name = $_GET['zipfile'];
		$tmp = explode('.',$file_name);
		$group = $tmp[0];
		$ext_name = $tmp[1];
		$type = 'single';
		
		// Enables all errors reporting
		ini_set('display_startup_errors',1);
		ini_set('display_errors',1);
		error_reporting(-1);
		
		_proGeneral($group,$ext_name);
		zipFileOuput($type,$ext_name);
	}

?>
<html>
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
	<title>Quick Tool Package</title>
	<meta name="description" content="">
	<meta name="viewport" content="width=device-width">
	<!-- Latest compiled and minified CSS -->
	<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.4/css/bootstrap.min.css"/>
	<style type="text/css">
		.cf:before,
		.cf:after {
			content: " "; /* 1 */
			display: table; /* 2 */
		}

		.cf:after {
			clear: both;
		}
		.ext-download{ margin:20px auto; padding:0; overflow:hidden; width:700px; }
		
		.ext-download ul.extd-tabs{
			list-style: none; 
			margin:0; 
			padding:0;
			border-bottom:1px solid #DDD;
			border-left:1px solid #DDD;
		}
		.ext-download ul.extd-tabs li{
			float:left; 
			margin:0; 
			margin-bottom:-1px;
		}
		.ext-download ul.extd-tabs li > a{
			text-decoration: none;
			border: 1px solid #DDD;
			line-height: 20px;
			padding: 8px 15px;
			display:block; 
			border-left:0;
			color:#555555;
			background-color: #F5F5F5;
			background-image: linear-gradient(to bottom, #FFFFFF, #E6E6E6);
			background-repeat: repeat-x;
			border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) #B3B3B3;
			border-image: none;
			box-shadow: 0 1px 0 rgba(255, 255, 255, 0.2) inset, 0 1px 2px rgba(0, 0, 0, 0.05);
			color: #333333;
			cursor: pointer;
			display: inline-block;
			font-size: 18px;
			margin-bottom: 0;
			text-align: center;
			text-shadow: 0 1px 1px rgba(255, 255, 255, 0.75);
			vertical-align: middle;
			border-bottom-color:#DDD;
		}
		
		.ext-download ul.extd-tabs li.active >a{
			border-bottom-color: #FFF;
			box-shadow:none;
			background:none;
		}
		
		.extd-tabs-content{
			border:1px solid #DDD;
			border-top:0;
			margin-bottom:30px;
			padding:20px 0;
			
		}
		
		
		.extd-tabs-content ul{ 
			margin:0; 
			padding:0 20px; 
			list-style:none;
			display:none;
		}
		
		.extd-tabs-content ul li{ margin:2px 0; 
			border-bottom: 1px solid #FFFFFF;
			border-top: 1px solid transparent;
				color: #666699;
			padding: 8px;
		}
		.extd-tabs-content ul li.color{
			background: #eee;
		}
		.extd-tabs-content ul li { font-size:16px;}
		
		.extd-tabs-content ul li a{ float:right; padding: 3px 10px;}
		
		.ext-download .extd-sbumit{ text-align:center; margin:10px;}
		
		
		.extd-tabs-content ul.extd-content-active{ display:block; }
		
		.extd-name-group,.extd-readmre{ margin:10px 0;}
		
		.align-center{text-align:center;margin:50px 0 30px; font-weight:bold;}
	</style>
	<?php $tag_id = 'ext_download'.rand().time(); ?>
	<script src="http://ajax.googleapis.com/ajax/libs/jquery/1/jquery.min.js"  type="text/javascript"></script>
	<script type="text/javascript">
		$.noConflict();
		jQuery(document).ready(function($){
			;(function(element){
				var $element = $(element);
				var $extd_tab = $('.extd-tab',$element);
				var $tab_content = $('.extd-tab-pane',$element);
				$extd_tab.each(function(val,el){
					var $tab = $(this);
					$tab.on('click.download', function(){
						var $this = $(this);
						if($this.hasClass('active')) return false;
						$extd_tab.removeClass('active');
						$this.addClass('active');	
						$tab_content.removeClass('extd-content-active');
						var $tab_content_active = $this.attr('data-tab');
						$($tab_content_active).addClass('extd-content-active');
						return false;
					});
					
				});
			})('#<?php echo $tag_id; ?>');
		});
	</script>
</head>	
<body>
	<div class="ext-download" id="<?php echo $tag_id; ?>" >
		<h1 class="align-center">Tool Creative Package</h1>
		<ul class="extd-tabs cf">
			<?php foreach($exts_group  as $key=> $ext){ ?>	
			<li class="extd-tab <?php echo ($key == 0)?' active':'';?>" data-tab="<?php echo '.extd-'.$ext; ?>">
				<a data-toggle="tab" href="#<?php echo $ext ?>"><?php echo _ucWords($ext); ?></a>
			</li>
		  <?php  } ?>
		</ul>
		<form  method="post" action="" class="form-horizontal">
			<div class="extd-tabs-content">
				<div class="extd-tabs-content-inner">
				<?php
				foreach($exts_group  as $_key => $ext){
				$_ext = '';
				$gext = '';	
				if($ext == 'modules'){
					$gext = 'mod';
					$_ext = 'modules';
				}else if($ext == 'plugins_content'){
					$gext = 'plc';
					$_ext = 'plugins/content';
				}else if($ext == 'plugins_system'){
					$gext = 'pls';
					$_ext = 'plugins/system';
				}else if($ext == 'templates'){
					$gext = 'combo';
					$_ext = 'templates';
				}else if($ext == 'quickstart'){
					$gext = 'quick';
					$_ext = dirname(dirname(realpath(__FILE__)));
					$templ	= dirname(realpath(__FILE__));
					$templ = explode('\\', $templ);
					$templ_name = array_pop($templ);

					
				}
				
				$items = getFolder($_ext);
				$cls = 'extd-'.$ext;
				$cls .=  ($_key == 0)?' extd-content-active':'';
				if(!empty($items)) { ?>
				<ul class="extd-tab-pane  <?php echo $cls; ?> ">
					<?php  $i = 0; 
					foreach($items as $item) { 
						$i++;
						if($ext != 'quickstart'){
					?>
						<li class="item-content <?php echo ($i%2)?' color':''; ?>">
							<input type="checkbox" value="<?php echo $gext.'.'.$item; ?>" name="zip_group[]">
							<span  class="title"><?php echo _ucWords($item); ?></span>
							<a class="btn btn-info" href="<?php echo '?zipfile='.$gext.'.'.$item; ?>" title="<?php echo _ucWords($item); ?>" >Download</a>
						</li>
					<?php }else{ 
						   if($item == $templ_name){
					?>
							<li class="item-content <?php echo ($i%2)?' color':''; ?>">
								<input type="checkbox" value="<?php echo $gext.'.'.$item; ?>" name="zip_group[]">
								<span  class="title"><?php echo _ucWords($item); ?></span>
								<a class="btn btn-info" href="<?php echo '?zipfile='.$gext.'.'.$item; ?>" title="<?php echo _ucWords($item); ?>" >Download</a>
							</li>
					<?php 
							}
						}
					} 
					?>
				</ul>
				<?php } 
				} ?>
				</div>
			</div>
			
			<div class="form-group">
				<label for="inputEmail3" class="col-sm-4 control-label">Enter Name for Group: </label>
				<div class="col-sm-8">
					<input type="text" name="name-group" value="" size="50" />
				</div>
			</div>
			
			<div class="form-group">
				<label for="inputEmail3" class="col-sm-4 control-label">Enter Content for Readme.txt:  </label>
				<div class="col-sm-8">
					<textarea rows="5" cols="55" name="read_me"></textarea>
				</div>
			 </div>
			
			<div class="extd-sbumit">
				<input type="submit" name="submit" value="Download" class="btn btn-primary" />
				<input type="reset"  value="Reset" class="btn btn-default"  onclick="location.reload()" />
			</div>
		</form>
	</div>
</body>
</html>