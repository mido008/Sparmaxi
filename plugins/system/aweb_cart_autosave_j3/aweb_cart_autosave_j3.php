<?php
/**
 * @package Plugin aWeb_Cart_AutoSave for Joomla! 3.x
 * @version 1.02
 * @author aWebSupport Team
 * @copyright (C) 2013-2015 aWebSupport.com
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/

defined('_JEXEC') or die;

class plgSystemaWeb_Cart_AutoSave_j3 extends JPlugin
{
	
	public function onAfterRoute()
	{
		$this->saveCart();	
	}

	protected function get_date()
	{
		$config = JFactory::getConfig();
		$dtz = new DateTimeZone('GMT');
		$date = new DateTime(NULL, $dtz);
		return $date->format('Y-m-d H:i:s');		
	}
	
	protected function getCartProducts($mycart) 
	{
		// virtuemart 3.0 support			
		if (substr($mycart, 0,1)=='O')
		{
			$cart = unserialize($mycart);	
			$products = $cart->products;
		}
		else if (substr($mycart, 0,1)=='{') 
		{
			$cart = json_decode($mycart);		
			$products = $cart->cartProductsData;	
		}
		return $products;
	}

	function saveCart()
	{
		$user = JFactory::getUser();
		$userid = $user->get('id');		
		if ($userid!=0)
		{				
			$db = JFactory::getDBO();				
			$session = JFactory::getSession();			
			if ($session->get('vmcart', null, 'vm')!=null)
			{
				$rawdata =  $session->get('vmcart', null, 'vm');
				$products = $this->getCartProducts($rawdata);
				$cartsize = count($products);						
				if ($cartsize>0)
				{		
					$db = JFactory::getDBO();
					if ($db->name == "mysql") $data = mysql_real_escape_string( $rawdata);
					else $data = mysqli_real_escape_string($db->getConnection(), $rawdata); 
					$now = $this->get_date();		
					$compr = 0;
					
					$q="INSERT INTO ".$db->getPrefix()."awebsavedcart (userid,data,date,compr) VALUES ('".$userid."','".$data."','".$now."','".$compr."')";
					$q.=" ON DUPLICATE KEY UPDATE data='".$data."',date='".$now."',compr='".$compr."'";    
					$db->setQuery($q);
					$db->query();	
				}
				else {
					$q="DELETE FROM ".$db->getPrefix()."awebsavedcart where userid='".$userid."'";
					$db->setQuery($q);
					$db->query();																		
				}
			}
			
		}
	}
	
}