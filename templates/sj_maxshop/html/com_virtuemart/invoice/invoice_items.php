<?php
/**
*
* Order items view
*
* @package	VirtueMart
* @subpackage Orders
* @author Max Milbers, Valerie Isaksen
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: details_items.php 5432 2012-02-14 02:20:35Z Milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');
// border-collapse: collapse;
 ?>
 
<style type="text/css">
.head_tab_product{
    border-bottom:1px solid #000; 
    
    background-color: #eceded;
}

.body_tab_product{
    border-bottom:1px solid #000;
    
}

.body_tab_product_detail{
}
</style>
<div style="width: 194mm;">
	<table cellspacing="0" cellpadding="0" width="100%" border="0" align="left">
		<tr align="left">
			<td style="width: 10mm;"><div style="width: 10mm; height: 2mm; background-color: #eceded; border-bottom: 0.5px solid #fff; border-bottom: 1px solid #000; font-size: 5.275pt"><br/></div></td>
	
			<td align="left" style="width: 190mm;">
					<table  width="190mm" style="font-size: 7.5pt ;font-family: Helvetica; display: block;" align="left" cellspacing="0" cellpadding="0">
                    	<tr align="left">
                    		<td align="left" height="6mm" width="5%" class="head_tab_product"><p>Pos</p></td>
                    		<td align="left" height="6mm" width="7%" class="head_tab_product"><p>Menge</p></td>
                    		<td align="left" height="6mm" width="12%" class="head_tab_product"><p>ProduktNr.</p></td>
                    		<td align="left" height="6mm" width="46%" class="head_tab_product"><p>Produktname</p></td>
                    		<td align="center" height="6mm" width="10%" class="head_tab_product"><p>Netto</p></td>
                    		<td align="center" height="6mm" width="10%" class="head_tab_product"><p>Ust</p></td>
                    		<td align="center" height="6mm" width="10%" class="head_tab_product"><p>Brutto</p></td>
                    	</tr>
                    	
                    	<?php
                            $menuItemID = shopFunctionsF::getMenuItemId($this->orderDetails['details']['BT']->order_language);
                            if(!class_exists('VirtueMartModelCustomfields'))require(VMPATH_ADMIN.DS.'models'.DS.'customfields.php');
                            VirtueMartModelCustomfields::$useAbsUrls = ($this->isMail or $this->isPdf);
                            $pos = 0;
                            $gesamt_netto = 0;
                            $gesamt_brutto = 0;
                            $gesamt_mwst = 0;
                           
                            foreach($this->orderDetails['items'] as $item) {
                                $pos++; 
                                $qtt = $item->product_quantity ;
                                $product_link = JURI::root().'index.php?option=com_virtuemart&view=productdetails&virtuemart_category_id=' . $item->virtuemart_category_id .'&virtuemart_product_id=' . $item->virtuemart_product_id . '&Itemid=' . $menuItemID;
                                $tax_product = $this->orderDetails['details']['BT']->order_tax;
                                $product_price_netto = $item->product_item_price;
                                $product_price_brutto = $tax_product+$product_price_netto;
                                $currency = $this->currency;
                                
                                $gesamt_netto += $product_price_netto;
                                $gesamt_brutto += $product_price_brutto;
                                $gesamt_mwst += $tax_product;
                                
                    	?>
                    	<tr >
                    		<td align="left" class="body_tab_product"> 
                			 	<div class="body_tab_product_detail"><p><br/><?php echo $pos; ?><br/></p></div>
                    		</td>
                    		<td align="left" class="body_tab_product">
                    			<div class="body_tab_product_detail"><p><br/><?php echo $qtt; ?><br/></p></div>
                    		</td>
                    		<td align="left" class="body_tab_product">
                    			<div class="body_tab_product_detail"><p><br/><?php echo $item->order_item_sku; ?><br/></p></div>
                    		</td>
                    		<td align="left" class="body_tab_product">
                    			<div class="body_tab_product_detail"><p><br/><?php
                                            echo $item->order_item_name;
                                            ?></p><p><?php 
                        				    $product_attribute = VirtueMartModelCustomfields::CustomsFieldOrderDisplay($item,'FE');
                            				if($product_attribute !="") 
                            				{
                        				        $product_attribute = strip_tags($product_attribute, '<br>');
                        				        $attributes = explode("<br/>", $product_attribute);
                            				    foreach($attributes as $att)
                            				    {
                            				        if($att != "" && $att != "<br />") 
                            				        {
                            				            echo "<br/>".$att;
//                             				            echo htmlspecialchars($att);
                            				        }
                            				    }
                            				        
//                             				    echo htmlspecialchars($product_attribute);
//                             				    echo "<br/>".$product_attribute;
                            				    
                            				}
                            			?></p></div>
                    		</td>
                    		<td align="center" class="body_tab_product">
                    			<div class="body_tab_product_detail"><p><br/><?php if ($this->doctype == 'invoice') echo $currency->priceDisplay($product_price_netto, $currency); ?><br/></p></div>
                    		</td>
                    		<td align="center" class="body_tab_product">
                    			<div class="body_tab_product_detail"><p><br/><?php if ($this->doctype == 'invoice') 
                                                if ( VmConfig::get('show_tax'))
                                                {
                                                    echo $currency->priceDisplay($tax_product, $currency); 
                                                } 
                                        ?><br/></p></div>
                    	    </td>
                    		<td align="center" class="body_tab_product">
                    			<div class="body_tab_product_detail"><p><br/><?php echo $currency->priceDisplay($product_price_brutto, $currency); ?><br/></p></div>
                    		</td>
                    	</tr><?php } ?>
                    	
                    	<tr align="left">
                    		<td colspan="4" align="left" class="body_tab_product"></td>
                    		<td colspan="3" align="left" class="body_tab_product">
                    			<div><br/><table cellspacing="2" cellpadding="4" width="100%">
                        				<tr align="left">
                        					<td align="left" style="width: 60%; border:1px solid #000;"><p>Gesamtbetrag (Netto)</p></td>
                        					<td align="left" style="width: 40%; border:1px solid #000;"><p><?php echo $currency->priceDisplay($gesamt_netto, $currency); ?></p></td>
                        				</tr>
                        				<tr align="left">
                        					<td align="left" style="width: 60%; border:1px solid #000;"><p>zzgl. 19% MwSt.</p></td>
                        					<td align="left" style="width: 40%; border:1px solid #000;"><p><?php echo $currency->priceDisplay($gesamt_mwst,$currency); ?></p></td>
                        				</tr>
                        				<tr align="left">
                        					<td align="left" style="width: 60%; border:1px solid #000;"><p>Versand Kosten</p></td>
                        					<td align="left" style="width: 40%; border:1px solid #000;"><p><?php echo "..."; ?></p></td>
                    					</tr>
                					</table>
            					</div>
            				</td>
                    	</tr>
                    	<tr align="left">
                    		<td colspan="4" align="left" class="body_tab_product">
                    			<div><br/><table cellspacing="2" cellpadding="4" width="50%">
                    				<tr align="left">
                    					<td align="left" style="border:1px solid #000; background-color:#000; color: #fff;"><p>Lieferungsadresse</p></td>
                    				</tr>
                    				
                    				<tr align="left">
                    					<td align="left" style="border:1px solid #000;"><p><?php 
                                                                        						    $user_first_name = "";
                                                                        						    $user_last_name = "";
                                                                        						    $user_street = "";
                                                                        						    $user_zip = "";
                                                                        						    $user_city = "";
                                                                        						    $user_country = "";
                                                                            						foreach ($this->userfields['fields'] as $field) {
                                                                            						    if (!empty($field['value'])) {
                                                                            						        switch ($field['title'])
                                                                            						        {
                                                                            						            case "First Name":
                                                                            						                $user_first_name = $field['value'];
                                                                            						                break;
                                                                            						            case "Last Name":
                                                                            						                $user_last_name = $field['value'];
                                                                            						                break;
                                                                            						            case "Address 1":
                                                                            						                $user_street = $field['value'];
                                                                            						                break;
                                                                            						            case "Zip / Postal Code":
                                                                            						                $user_zip = $field['value'];
                                                                            						                break;
                                                                            						            case "City":
                                                                            						                $user_city = $field['value'];
                                                                            						                break;
                                                                            						            case "Country":
                                                                            						                $user_country = $field['value'];
                                                                            						                break;
                                                                            						        }
                                                                            						    }
                                                                        						    }
                                                                        						    echo $user_first_name." ".$user_last_name."<br/>";
                                                                        						    echo $user_street."<br/>";
                                                                        						    echo $user_zip." ".$user_city." - ".$user_country;
                                                                                                ?></p>
                    					</td>
                    				</tr>
                    			</table></div>
                    		</td>
                    		
                    		<td colspan="3" align="left" class="body_tab_product">
                    			<div><br/><table cellspacing="3" cellpadding="4">
                    				<tr align="left">
                    					<td align="left" style="width: 60%; border:1px solid #000; background-color:#000; color: #fff;"><p>Gesamtbetrag (Brutto)</p></td>
                    					<td align="left" style="width: 40%; border:1px solid #000; background-color:#000; color: #fff;"><p><?php echo $currency->priceDisplay($gesamt_brutto,$currency); ?></p></td>
                    				</tr>
                    				
                    				<tr align="left">
                    					<td colspan="2" align="left"><p><?php echo "Gewählte Zahlungsart: ".$this->orderDetails['paymentName']; ?></p></td>
                    				</tr>
                    				<tr colspan="2" align="left">
                    					<td align="left" style="font-weight: bold;"><p>Ihre ....</p></td>
                    				</tr>
                    			</table></div>
                    		</td>
                    	</tr>
                    	
                    </table>
			</td>
			<td style="width: 10mm;"><div style="width: 10mm; height: 2mm; background-color: #eceded; border-bottom: 0.5px solid #fff; border-bottom: 1px solid #000; font-size: 5.275pt"><br/></div></td>
		</tr>
	</table>
</div>
<?php
// print_r($this->orderDetails);
?>