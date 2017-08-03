<?php
/**
 * @version		1.4.1
 * @package		Joomla
 * @subpackage	EShop - Auspost Shipping
 * @author  	Giang Dinh Truong
 * @copyright	Copyright (C) 2012 Ossolution Team
 * @license		GNU/GPL, see LICENSE.php
 */
// no direct access
defined( '_JEXEC' ) or die();

class eshop_auspostpro extends eshop_shipping
{
	
	/**
	 * 
	 * Constructor function
	 */
	function eshop_auspostpro()
	{
		parent::setName('eshop_auspostpro');
		parent::eshop_shipping();
	}

	/**
	 * 
	 * Function tet get quote for auspost shipping
	 * @param array $addressData
	 * @param object $params
	 */
	function getQuote($addressData, $params)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		if (!$params->get('geozone_id'))
		{
			$status = true;
		}
		else
		{
			$query->select('COUNT(*)')
				->from('#__eshop_geozonezones')
				->where('geozone_id = ' . intval($params->get('geozone_id')))
				->where('country_id = ' . intval($addressData['country_id']))
				->where('(zone_id = 0 OR zone_id = ' . intval($addressData['zone_id']) . ')');
			$db->setQuery($query);
			if ($db->loadResult())
			{
				$status = true;
			}
			else
			{
				$status = false;
			}
		}
		
		if ($status && ($params->get('auspost_standard') || $params->get('auspost_registered') || $params->get('auspost_insured') || $params->get('auspost_express') || $params->get('auspost_sea') || $params->get('auspost_air') || $params->get('auspost_satchreg') || $params->get('auspost_satcheby') || $params->get('auspost_satchexp') || $params->get('auspost_satchpla')))
		{
			$cart = new EshopCart();
			$tax = new EshopTax(EshopHelper::getConfig());
			$currency = new EshopCurrency();
			$eshopWeight = new EshopWeight();
			$eshopLength = new EshopLength();
			$cartWeight = $eshopWeight->convert($cart->getWeight(), EshopHelper::getConfigValue('weight_id'), $params->get('weight_id'));
			//Find id of gram weight
			$query->clear();
			$query->select('weight_id')
				->from('#__eshop_weightdetails')
				->where('weight_unit = "g"');
			$db->setQuery($query);
			$unitG = $db->loadResult();
			$weight = intval($eshopWeight->convert($cart->getWeight(), EshopHelper::getConfigValue('weight_id'), $unitG));
			$countryInfo = EshopHelper::getCountry($addressData['country_id']);
			//Initialise variables
			$methodData = array();
			$quoteData = array();
			$error = FALSE;
			//These errors will clobber each other, so only one error will be displayed at a time
			if (intval($weight) == 0)
				$error = 'The cart weight is 0g, unable to calculate shipping costs';
			if ((intval($weight) > 20000) && !$params->get('auspost_multiple'))
			{
				$error = 'Your cart is too heavy to ship with Australia Post (20kg+)';
			}
			else
			{
				//Still need to check if there is a single item > 20kg
				foreach ($cart->getCartData() as $weightcheck)
				{
					if (($eshopWeight->convert($weightcheck['total_weight'], $weightcheck['product_weight_id'], $unitG) / $weightcheck['quantity']) > 20000)
					{
						$error = 'There is a single item in your cart that is too heavy to ship with Australia Post (20kg)';
					}
				}
			}
		
			if($countryInfo->iso_code_2 == 'AU')
			{
				if (!preg_match('/^[0-9]{4}$/', $addressData['postcode']))
				{
					$error = 'Your post code is not valid in Australia';
				}
				$validmethods = array("standard","registered","insured","express");
			}
			else
			{
				$validmethods = array("sea","air");
			}
			//Break the items up in to multiple parcels ready to be sent off to the quote function
			if((@count($validmethods) > 0) && $error == FALSE)
			{
				//Find id of mm length
				$query->clear();
				$query->select('length_id')
					->from('#__eshop_lengthdetails')
					->where('length_unit = "mm"');
				$db->setQuery($query);
				$unitMm = $db->loadResult();
		
				//Set the total cubed amount to 0
				$cartcubed = 0;
		
				//Setup the parcel array to take the contents from the cart
				$parcels = array();
				$parcels[0]['weight'] = 0;
				$parcels[0]['items'] = 0;
				$parcels[0]['single_w'] = 0;
				$parcels[0]['single_h'] = 0;
				$parcels[0]['single_l'] = 0;
				$parcels[0]['cubed'] = 0;
				$parcels[0]['price'] = 0;
		
				foreach ($cart->getCartData() as $cartitem)
				{
					//Split everything into parcels for multiple parcel shipping (all items now use this method)
					$productWeight = $eshopWeight->convert($cartitem['total_weight'], $cartitem['product_weight_id'], $unitG) / $cartitem['quantity'];
					//Always try and fill at the first parcel, then move to the next
					$parcelNum = 0;
					for ($qnty = 1; $qnty <= $cartitem['quantity']; $qnty++)
					{
						$item_placed = false;
						while(!$item_placed)
						{
							if(($parcels[$parcelNum]['weight'] + $productWeight) > 20000)
							{
								if($parcels[$parcelNum]['weight'] == 0)
								{
									//The parcel is empty but can't fit 20kg in it.. this should never happen, but this is here just in case! (possible rounding error?)
									//This item will never fit in a parcel, tell the loop to exit gracefully
									$item_placed = true;
								}
								else
								{
									//This parcel can't fit the item, move to the next parcel
									$parcelNum++;
									//There is an offset by 1, so it's really if parcel_num is more than parcels
									if($parcelNum == count($parcels)) {
										$parcels[$parcelNum]['weight'] = 0;
										$parcels[$parcelNum]['items'] = 0;
										$parcels[$parcelNum]['single_w'] = 0;
										$parcels[$parcelNum]['single_h'] = 0;
										$parcels[$parcelNum]['single_l'] = 0;
										$parcels[$parcelNum]['cubed'] = 0;
										$parcels[$parcelNum]['price'] = 0;
									}
								}
							}
							else
							{
								//Check the length class, if it isn't mm we need to convert it
								//If the length value is 0 we use 100mm (10cm) as a fallback
								if($cartitem['product_width'] != 0)
								{
									$itemWidth = $eshopLength->convert($cartitem['product_width'], $cartitem['product_length_id'], $unitMm);
								}
								else
								{
									$itemWidth = 100;
								}
		
								if($cartitem['product_height'] != 0)
								{
									$itemHeight = $eshopLength->convert($cartitem['product_height'], $cartitem['product_length_id'], $unitMm);
								}
								else
								{
									$itemHeight = 100;
								}
		
								if($cartitem['product_length'] != 0)
								{
									$itemLength = $eshopLength->convert($cartitem['product_length'], $cartitem['product_length_id'], $unitMm);
								}
								else
								{
									$itemLength = 100;
								}
		
								$parcels[$parcelNum]['weight'] += $productWeight;
								$parcels[$parcelNum]['items'] ++;
		
								//Price is used only for insurance (per parcel)
								$parcels[$parcelNum]['price'] += $cartitem['price'];
		
								//Cubed is used if there is more than one item in this parcel
								$parcels[$parcelNum]['cubed'] += ($itemWidth * $itemHeight * $itemLength);
		
								//Single values are clobbered everytime an item is put into this parcel, single values are only used if this parcel contains a single item
								$parcels[$parcelNum]['single_w'] = $itemWidth;
								$parcels[$parcelNum]['single_h'] = $itemHeight;
								$parcels[$parcelNum]['single_l'] = $itemLength;
		
								//We have placed the item in a parcel
								$item_placed = true;
							}
						}
					}
				}
			}
		
			foreach ($validmethods as $postmethod)
			{
				if($params->get('auspost_' . $postmethod) && $error == FALSE)
				{
					$combinedPostcharge = 0;
					for ($plp = 0; $plp < count($parcels); $plp++)
					{
						if($parcels[$plp]['items'] == 1)
						{
							$postcharge = $this->getAuspostQuote($addressData['postcode'], $postmethod, $parcels[$plp]['weight'], $countryInfo->iso_code_2, $parcels[$plp]['single_w'], $parcels[$plp]['single_h'], $parcels[$plp]['single_l'] , $parcels[$plp]['price'], $params);
						}
						else
						{
							$postcharge = $this->getAuspostQuote($addressData['postcode'], $postmethod, $parcels[$plp]['weight'], $countryInfo->iso_code_2, round(pow($parcels[$plp]['cubed'],1/3)), round(pow($parcels[$plp]['cubed'],1/3)), round(pow($parcels[$plp]['cubed'],1/3)), $parcels[$plp]['price'], $params);
						}
						if($postcharge[0] < 0)
						{
							$error = $postcharge[1];
						}
						else
						{
							$combinedPostcharge += $postcharge[0];
						}
					}
					
					if($error == FALSE)
					{
						$quoteData['auspost_' . $postmethod] = array(
							'name'			=> 'eshop_auspostpro.auspost_' . $postmethod,
							'title'			=> JText::_('ESHOP_AUSPOSTPRO_' . strtoupper($postmethod)). $postcharge[1],
							'cost'			=> $combinedPostcharge,
							'taxclass_id'	=> $params->get('auspost_tax_class_id'),
							'text'			=> '$' . sprintf('%.2f', ($tax->calculate($combinedPostcharge, $params->get('auspost_tax_class_id'), EshopHelper::getConfigValue('tax'))))
						);
					}
				}
			}
		
			//Code for prepaid satchels
			//Satchels do not feedback any errors, they are just displayed if the weight fits in the criteria and the method is enabled
			if($countryInfo->iso_code_2 == 'AU') {
				foreach (array("satchreg", "satcheby", "satchexp", "satchpla") as $postmethod) {
					if($params->get('auspost_' . $postmethod) && $error == FALSE) {
						$satcharge = $this->getAuspostSatchel($postmethod, $weight, $params);
						if($satcharge > 0) {
							$quoteData['auspost_' . $postmethod] = array(
									'name'			=> 'eshop_auspostpro.auspost_' . $postmethod,
									'title'			=> JText::_('ESHOP_AUSPOSTPRO_' . strtoupper($postmethod)),
									'cost'			=> $satcharge,
									'taxclass_id'	=> $params->get('auspost_tax_class_id'),
									'text'			=> '$' . sprintf('%.2f', ($tax->calculate($satcharge, $params->get('auspost_taxclass_id'), EshopHelper::getConfigValue('tax'))))
							);
						}
					}
				}
			}
		
			//If there are no postage quotes, we don't want to return an empty set (but we do want to make sure errors are displayed)
			if(count($quoteData) != 0)
			{
				$query->clear();
				$query->select('*')
					->from('#__eshop_shippings')
					->where('name = "eshop_auspostpro"');
				$db->setQuery($query);
				$row = $db->loadObject();
				$methodData = array (
					'name'         => 'eshop_auspostpro',
					'title'		=> JText::_('ESHOP_AUSPOSTPRO_TITLE'),
					'quote'      => $quoteData,
					'ordering'	=> $row->ordering,
					'error'      => false);
			}
			return $methodData;
		}//End Auspost module is enabled
		return $methodData;
	}
	
	private function getAuspostQuote($dstPostcode, $service, $weight, $country, $width, $height, $length, $parcelValue, $params)
	{
		//Registered and Registered (Insured) are both the 'standard' shipping method with additional fees added
		if($service == "registered" || $service == "insured")
		{
			$reqService = "standard";
		}
		else
		{
			$reqService = $service;
		}
		//Australia Post appear to have some undocumented minimum values for different dimensions, check that items passed aren't below the minimums
		if($width < 30)
			$width = 30;
		if($height < 50)
			$height = 50;
		if($length < 50)
			$length = 50;
		
		$requestUrl = 'http://drc.edeliver.com.au/ratecalc.asp?pickup_postcode=' . $params->get('auspost_postcode') . '&width=' . $width . '&height=' . $height . '&length=' . $length . '&country=' . $country . '&service_type=' . $reqService . '&quantity=1&weight=' . $weight;
		if(strtolower($country) == "au")
			$requestUrl .= '&destination_postcode=' . $dstPostcode;
	
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL,$requestUrl);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		$getQuote = curl_exec($ch);
		curl_close($ch);
	
		if (strstr($getQuote, 'err_msg=OK') == FALSE)
		{
			//This is going to be returned as an error, set the value to less than 0
			$auspostQuote[0] = -1;
	
			//Check if the string is even remotely what we are looking for (if so, we know Auspost doesn't like the combo)
			if(strstr($getQuote, 'err_msg=') == FALSE)
			{
				$auspostQuote[1] = 'Error interfacing with Australia Post (connection)';
			}
			else
			{
				if(strstr($getQuote, 'Weight Outside Valid Range') != FALSE)
				{
					//Special case where destination country won't accept parcels over a certain weight, give the customer a better explanation
					$auspostQuote[1] = 'Cart is too heavy to ship to this destination';
				}
				else
				{
					//If it's not a special case, feed back the Australia Post error directly
					$auspostQuote[1] = substr(strstr($getQuote,'err_msg='),8);
				}
			}
		}
		else
		{
			$getQuoteCharge = preg_match('/^charge=([0-9]{1,3}\.?[0-9]{0,2})/', $getQuote, $quoteCharge);
			if (!isset($quoteCharge[1]))
			{
				$auspostQuote[0] = -1;
				$auspostQuote[1] = 'Error interfacing with Australia Post (charge)';
			}
			else
			{
				$postCharge = sprintf('%.2f', $quoteCharge[1]);
				//Calculate additional values for registered / insured
				if($service == "registered" || $service == "insured")
				{
					$postCharge = sprintf('%2.f', $postCharge + 3.05);
				}
				//Calculate additional insurance cost if the item is over $100AUD (first $100AUD is covered by standard registered post)
				if(($service == "insured") && ($parcelValue > 100))
				{
					$postCharge = sprintf('%.2f', $postCharge + floatval(ceil(($parcelValue - 100) / 100) * 1.45));
				}
				if (floatval($params->get('auspost_handling')) > 0)
				{
					$postCharge = sprintf('%.2f', $postCharge + floatval($params->get('auspost_handling')));
				}
	
				if ($params->get('auspost_stripgst'))
				{
					$auspostQuote[0] = sprintf('%.2f', ($postCharge / 11) * 10);
				}
				else
				{
					$auspostQuote[0] = sprintf('%.2f', $postCharge);
				}
	
				$getPostEstimate = preg_match('/days=([0-9]{1,2})/', $getQuote, $postEstimate);
				$auspostQuote[1] = '';
				if ($params->get('auspost_estimate') && isset($postEstimate[1]))
				{
					//Added check for 0 as Australia Post modified their gateway to return 0 for international estimates as they no longer provide them
					if (is_numeric($postEstimate[1]) && $postEstimate[1] !=0)
					{
						if($postEstimate[1] == 1)
						{
							$auspostQuote[1] = ' (est. ' . $postEstimate[1] . ' day delivery)';
						}
						else
						{
							$auspostQuote[1] = ' (est. ' . $postEstimate[1] . ' days delivery)';
						}
					}
				}
			}
		}
		return $auspostQuote;
	}
	
	private function getAuspostSatchel($service, $weight, $params)
	{
		//Define the different satchel sizes / prices (0 represents unavailable) - Updated April 2012
		$satchel = array("satchreg" => array(0 => 7.20, 1 => 11.40, 2 => 14.50),
			"satcheby" => array(0 => 6.20, 1=> 10.55, 2=> 0),
			"satchexp" => array(0 => 9.55, 1 => 13.05, 2 => 21.65),
			"satchpla" => array(0 => 13.90, 1 => 18.35, 2 => 0));
		//Default to return 0
		$satchQuote = 0;
		if($weight <= 500)
			$satchQuote = $satchel[$service][0];
		if(($weight > 500) && ($weight <= 3000))
			$satchQuote = $satchel[$service][1];
		if(($weight > 3000) && ($weight <=5000))
			$satchQuote = $satchel[$service][2];
		//Added > 0 check to ensure handling wasn't added if no satchel was suitable
		if ((floatval($params->get('auspost_handling')) > 0) && $satchQuote > 0 )
			$satchQuote = sprintf('%.2f', $satchQuote + floatval($params->get('auspost_handling')));
		if ($params->get('auspost_stripgst'))
			$satchQuote = (($satchQuote / 11) * 10);
		return $satchQuote;
	}
}