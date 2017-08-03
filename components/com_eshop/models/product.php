<?php
/**
 * @version		1.4.1
 * @package		Joomla
 * @subpackage	EShop
 * @author  	Giang Dinh Truong
 * @copyright	Copyright (C) 2012 Ossolution Team
 * @license		GNU/GPL, see LICENSE.php
 */
// no direct access
defined('_JEXEC') or die();

class EShopModelProduct extends EShopModel
{
	/**
	 * Entity ID
	 *
	 * @var int
	 */
	protected $id = null;

	/**
	 * Entity data
	 *
	 * @var array
	 */
	protected $data = null;
	
	/**
	 * Current active language
	 *
	 * @var string
	 */
	protected $language = null;
	
	/**
	 * 
	 * Constructor
	 * @since 1.5
	 */
	public function __construct($config = array())
	{
		parent::__construct();
		$this->id = JRequest::getInt('id');
		$this->data = null;
		$this->language = JFactory::getLanguage()->getTag();
	}
	
	/**
	 * 
	 * Function to get product data
	 * @see EShopModel::getData()
	 */
	function &getData()
	{
		if (empty($this->data))
		{
			$this->_loadData();
		}
		return $this->data;
	}
	
	/**
	 * 
	 * Function to load product data
	 * @see EShopModel::_loadData()
	 */
	function _loadData() {
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('a.*, b.product_name, b.product_alias, b.product_desc, b.product_short_desc, b.product_page_title, b.product_page_heading, b.meta_key, b.meta_desc, b.tab1_title, b.tab1_content, b.tab2_title, b.tab2_content, b.tab3_title, b.tab3_content, b.tab4_title, b.tab4_content, b.tab5_title, b.tab5_content')
			->from('#__eshop_products AS a')
			->innerJoin('#__eshop_productdetails AS b ON (a.id = b.product_id)')
			->where('a.id = ' . intval($this->id))
			->where('a.published = 1')
			->where('b.language = "' . $this->language . '"');
		//Check viewable of customer groups
		$user = JFactory::getUser();
		if ($user->get('id'))
		{
			$customer = new EshopCustomer();
			$customerGroupId = $customer->getCustomerGroupId();
		}
		else
		{
			$customerGroupId = EshopHelper::getConfigValue('customergroup_id');
		}
		if (!$customerGroupId)
			$customerGroupId = 0;
		$query->where('((a.product_customergroups = "") OR (a.product_customergroups IS NULL) OR (a.product_customergroups = "' . $customerGroupId . '") OR (a.product_customergroups LIKE "' . $customerGroupId . ',%") OR (a.product_customergroups LIKE "%,' . $customerGroupId . ',%") OR (a.product_customergroups LIKE "%,' . $customerGroupId . '"))');
		//Check out of stock
		if (EshopHelper::getConfigValue('hide_out_of_stock_products'))
		{
			$query->where('a.product_quantity > 0');
		}
		$db->setQuery($query);
		$this->data = $db->loadObject();
	}
	
	/**
	 * 
	 * Function to write review
	 * @param array $data
	 * @return json array
	 */
	function writeReview($data)
	{
		$user = JFactory::getUser();
		$json = array();
		if (strlen($data['author']) < 3 || strlen($data['author']) > 25)
		{
			$json['error'] = JText::_('ESHOP_ERROR_YOUR_NAME');
			return $json;
		}
		if (strlen($data['review']) < 25 || strlen($data['review']) > 1000)
		{
			$json['error'] = JText::_('ESHOP_ERROR_YOUR_REVIEW');
			return $json;
		}
		if (!$data['rating'])
		{
			$json['error'] = JText::_('ESHOP_ERROR_RATING');
			return $json;
		}
		if (EshopHelper::getConfigValue('enable_reviews_captcha'))
		{
			$captchaPlugin = JFactory::getApplication()->getParams()->get('captcha', JFactory::getConfig()->get('captcha'));
			if ($captchaPlugin == 'recaptcha')
			{
				$input = JFactory::getApplication()->input;
				$res = JCaptcha::getInstance($captchaPlugin)->checkAnswer($input->post->get('recaptcha_response_field', '', 'string'));
				if (!$res)
				{
					$json['error'] = JText::_('ESHOP_INVALID_CAPTCHA');
					return $json;
				}	
			}
		}
		if (!$json)
		{
			$row = JTable::getInstance('Eshop', 'Review');
			$row->bind($data);
			$row->id = '';
			$row->product_id = $data['product_id'];
			$row->customer_id = $user->get('id') ? $user->get('id') : 0;
			$row->published = 0;
			$row->created_date = JFactory::getDate()->toSql();
			$row->created_by = $user->get('id') ? $user->get('id') : 0;
			$row->modified_date = JFactory::getDate()->toSql();
			$row->modified_by = $user->get('id') ? $user->get('id') : 0;
			$row->checked_out = 0;
			$row->checked_out_time = '0000-00-00 00:00:00';
			if ($row->store())
			{
				$json['success'] = JText::_('ESHOP_REVIEW_SUBMITTED_SUCESSFULLY');
			}
			else 
			{
				$json['error'] = JText::_('ESHOP_REVIEW_SUBMITTED_FAILURED');
			}
			return $json;
		}
	}
	
	/**
	 * 
	 * Function to process ask question
	 * @param array $data
	 */
	function processAskQuestion($data)
	{
		$jconfig = new JConfig();
		$fromName = $jconfig->fromname;
		$fromEmail =  $jconfig->mailfrom;
		$product = EshopHelper::getProduct($data['product_id']);
		$subject 	= JText::_('ESHOP_ASK_QUESTION_SUBJECT');
		$body 		= EshopHelper::getAskQuestionEmailBody($data, $product);
		$adminEmail = EshopHelper::getConfigValue('email') ? trim(EshopHelper::getConfigValue('email')) : $fromEmail;
		$mailer = JFactory::getMailer();
		$mailer->ClearAllRecipients();
		$mailer->sendMail($fromEmail, $fromName, $adminEmail, $subject, $body, 1);
	}
}