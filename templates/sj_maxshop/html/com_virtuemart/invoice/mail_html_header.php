<?php
/**
 *
 * Define here the Header for order mail success !
 *
 * @package    VirtueMart
 * @subpackage Cart
 * @author Kohl Patrick
 * @author Valérie Isaksen
 * @link http://www.virtuemart.net
 * @copyright Copyright (c) 2004 - 2010 VirtueMart Team. All rights reserved.
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
 * VirtueMart is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 *
 */
// Check to ensure this file is included in Joomla!
defined ('_JEXEC') or die('Restricted access');
?>

<div style="width: 170mm; height: 4mm; background-color: #000;"></div>

<div style="width: 170mm; display:block; background-color: #eceded; border-bottom: 1px solid #000;">
<table  cellspacing="0" cellpadding="0" border="0" align="left" style="width: 170mm; font-size: 6.68pt; font-family: Helvetica">
	<tr>
		<td style="width: 10mm;"></td>
		
		<td style="width: 52mm;">
			<table width="100%" cellspacing="4" cellpadding="4">
				<tr>
					<td style="height: 19.8mm;"><img src="images/Sparmaxi-Rechnung-Rechnung.png" alt="" /></td>
				</tr>
				<tr>
					<td style="border:1px solid #000; height: 6.5mm;"><strong style="font-size: 10pt;"><?php echo $this->orderDetails['details']['BT']->first_name." ".$this->orderDetails['details']['BT']->last_name; ?></strong></td>
				</tr>
				<tr>
					<td style="border:1px solid #000; height: 9.5mm">
						<strong><?php 
                                echo "bill_to_street"."<br/>";
                                echo "bill_to_zip"." "."bill_to_city"." - "."bill_to_country";
						    ?></strong>
					</td>
				</tr>
			</table>
		</td>
		
		<td style="width: 86mm;"></td>
		
		<td style="width: 52mm; font-size: 7.5pt; font-family: Helvetica;">
			<table width="100%" cellspacing="4" cellpadding="2">
				<tr>
					<td style="height: 19.8mm;"><img width="147.4px" height="56.13px" src="images/Sparmaxi-Rechnung-Logo.png" alt="" /></td>
				</tr>
				<tr>
					<td style="height: 5mm; background-color: #000; color: #fff;"><strong> Kunden ID: <?php ?></strong></td>
				</tr>
				<tr>
					<td style="height: 5mm; background-color: #000; color: #fff;"><strong> Rechnungsnr. <?php echo $this->invoiceNumber;?></strong></td>
				</tr>
				<tr>
					<td style="height: 5mm; background-color: #000; color: #fff;"><strong> Rechnungsdatum: <?php echo vmJsApi::date($this->invoiceDate, 'LC4', true); ?></strong></td>
				</tr>
			</table>
		</td>
		
		<td style="width: 10mm;"></td>
	</tr>
</table>
</div>







