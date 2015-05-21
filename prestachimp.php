<?php
/*
*
* Author: Jeff Simons Decena @2013
*
*/

if (!defined('_PS_VERSION_'))
	exit;

class Prestachimp extends Module
{

	public function __construct()
	{
	$this->name = 'prestachimp';
	$this->tab = 'advertising_marketing';
	$this->version = '0.1';
	$this->author = 'Jeff Simons Decena';
	$this->need_instance = 0;
	$this->ps_versions_compliancy = array('min' => '1.5', 'max' => '1.6');
	$this->pchimp_table = "pchimp_subscribers";

	parent::__construct();

	$this->displayName = $this->l('PrestaChimp Module');
	$this->description = $this->l('PrestaChimp configuration module');

	$this->vcode 		= Tools::passwdGen(12, 'NO_NUMERIC');
	$this->vcode_type 	= null;

	$this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

	if (!Configuration::get('PRESTACHIMP'))      
	  $this->warning = $this->l('No name provided');
	}

	public function install()
	{
	  return parent::install() &&
	  	Configuration::updateValue('PRESTACHIMP', 'PRESTACHIMP MODULE') &&
	  	Configuration::updateValue('PCHIMP_KEY', 'b467da49d708b4642486a2dfc8b7606c-us4') &&	  
	  	Configuration::updateValue('PCHIMP_LIST_ID', 'bc5097e5f4') &&
	  	Configuration::updateValue('PCHIMP_DOUBLE_OPTIN', 1) &&
	  	Configuration::updateValue('PCHIMP_SEND_WELCOME_EMAIL', 0) &&
	  	Configuration::updateValue('PCHIMP_SEND_VOUCHER', 0) &&
	  	Configuration::updateValue('PCHIMP_VOUCHERTITLE', null) &&
	  	Configuration::updateValue('PCHIMP_DISCOUNTTYPE', 1) &&
	  	Configuration::updateValue('PCHIMP_DISCOUNTVALUE', 0) &&
	  	Configuration::updateValue('PCHIMP_MIN_PURCHASE', 0) &&
	  	Configuration::updateValue('PCHIMP_EMAIL_CONTENT', null) &&
	  	$this->registerHook('footer') &&
	  	$this->registerHook('myPrestaChimp') &&
	  	$this->registerHook('actionCustomerAccountAdd') &&
		//ALTER THE CUSTOMER TABLE
	    Db::getInstance()->Execute('
			CREATE TABLE `'._DB_PREFIX_. $this->pchimp_table.'` (
			  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
			  `email` varchar(128) DEFAULT NULL,
			  `code` varchar(255) DEFAULT NULL,
			  `type` varchar(255) DEFAULT NULL,
			  PRIMARY KEY `id` (`id`)
			) ENGINE=InnoDB DEFAULT CHARSET=utf8;
	    ');
	}	

	public function uninstall()
	{
	  return parent::uninstall() && 
	  	Configuration::deleteByName('PRESTACHIMP') &&
	  	Configuration::deleteByName('PCHIMP_KEY') &&
	  	Configuration::deleteByName('PCHIMP_LIST_ID') &&
	  	Configuration::deleteByName('PCHIMP_DOUBLE_OPTIN') &&
	  	Configuration::deleteByName('PCHIMP_SEND_WELCOME_EMAIL') &&
	  	Configuration::deleteByName('PCHIMP_SEND_VOUCHER') &&
	  	Configuration::deleteByName('PCHIMP_VOUCHERTITLE') &&
	  	Configuration::deleteByName('PCHIMP_DISCOUNTTYPE') &&
	  	Configuration::deleteByName('PCHIMP_DISCOUNTVALUE') &&
	  	Configuration::deleteByName('PCHIMP_EMAIL_CONTENT') &&
	  	$this->unregisterHook('footer') &&
	  	$this->unregisterHook('myPrestaChimp') &&
	  	$this->unregisterHook('actionCustomerAccountAdd') &&
	  	Db::getInstance()->Execute('DROP TABLE '. _DB_PREFIX_ .$this->pchimp_table.'');
	}

	public function getContent()
	{
	    $output = null;
	 	$js 	= null;
	 	$css 	= null;

	 	$output .= '
	 		<img src="'._MODULE_DIR_.$this->name.'/images/Freddie_OG.png" alt="" width="28" height="28" style="float:left; margin-right: 5px" />
	 		<h1 style="line-height: 0px; padding:0 0 10px">PrestaChimp Settings</h1>
	 	';

	    if (Tools::isSubmit('submit'.$this->name))
	    {
            Configuration::updateValue('PCHIMP_KEY', strval(Tools::getValue('PCHIMP_KEY')));
            Configuration::updateValue('PCHIMP_LIST_ID', strval(Tools::getValue('PCHIMP_LIST_ID')));
            Configuration::updateValue('PCHIMP_DOUBLE_OPTIN', strval(Tools::getValue('PCHIMP_DOUBLE_OPTIN')));
            Configuration::updateValue('PCHIMP_SEND_WELCOME_EMAIL', strval(Tools::getValue('PCHIMP_SEND_WELCOME_EMAIL')));
            Configuration::updateValue('PCHIMP_SEND_VOUCHER', strval(Tools::getValue('PCHIMP_SEND_VOUCHER')));
            Configuration::updateValue('PCHIMP_MIN_PURCHASE', strval(Tools::getValue('PCHIMP_MIN_PURCHASE')));
            Configuration::updateValue('PCHIMP_EMAIL_CONTENT', strval(Tools::getValue('PCHIMP_EMAIL_CONTENT')));
            
			//IF WE NEED TO CREATE A VOUCHER CODE
            if (Tools::getValue('PCHIMP_SEND_VOUCHER')){
            	
            	Configuration::updateValue('PCHIMP_VOUCHERTITLE', strval(Tools::getValue('PCHIMP_VOUCHERTITLE')));
            	Configuration::updateValue('PCHIMP_DISCOUNTTYPE', strval(Tools::getValue('PCHIMP_DISCOUNTTYPE')));
            	Configuration::updateValue('PCHIMP_DISCOUNTVALUE', strval(Tools::getValue('PCHIMP_DISCOUNTVALUE')));
				Configuration::updateValue('PCHIMP_VOUCHERTITLE', strval(Tools::getValue('PCHIMP_VOUCHERTITLE')));
            }else{

	        	Configuration::updateValue('PCHIMP_VOUCHERTITLE', null);
	        	Configuration::updateValue('PCHIMP_MIN_PURCHASE', 0);
	        	Configuration::updateValue('PCHIMP_DISCOUNTTYPE', 1);
	        	Configuration::updateValue('PCHIMP_DISCOUNTVALUE', 0);
	        }

            $output .= $this->displayConfirmation($this->l('Settings updated'));
	    }

	    $js 	= '<script type="text/javascript" src="'._MODULE_DIR_. $this->name .'/prestachimp-admin.js"></script>';
	    $css 	= '<style type="text/css">.hidden { display: none; } .form-group textarea { width:300px; height:120px; }</style> ';
	    
	    return $output.$this->displayForm().$js.$css;
	}	

	public function displayForm()
	{
		$output = '
					<p class="alert alert-warning">Notes: </p>
					<ul>
						<li><em>The voucher code to be generated is a random string.</em></li>
						<li><em>Send confirmation email</em> is the email where the customer need to confirm that they really want to join the mailing list.</li>
						<li>Discount type is in Percentage or Fixed amount.</li>
						<li>Variables you can use in the email content: <br />
							<strong><em>{code}</em></strong> - is the voucher code. <br />
							<strong><em>{value_discount}</em></strong> - is the discount value. <br />
							<strong><em>{link}</em></strong> - is the store link.
						</li>
					</ul>
				';
	    // Get default Language
	    $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
	     
	    // Init Fields form array
	    $fields_form[0]['form'] = array(
	        'input' => array(
	            array(
	                'type' => 'text',
	                'label' => $this->l('API key'),
	                'name' => 'PCHIMP_KEY',
	                'size' => 20,
	                'required' => true
	            ),
	            array(
	                'type' => 'text',
	                'label' => $this->l('Your mailing list id'),
	                'name' => 'PCHIMP_LIST_ID',
	                'size' => 20,
	                'required' => true
	            ),
				array(
					'type' => 'switch',
					'label' => $this->l('Send confirmation email'),
					'name' => 'PCHIMP_DOUBLE_OPTIN',
					'is_bool' => true,
					'values' => array(
						array(
							'id' => 'optin_on',
							'value' => 1,
							'label' => $this->l('Yes')
						),
						array(
							'id' => 'optin_off',
							'value' => 0,
							'label' => $this->l('No')
						)
					),
				),
				array(
					'type' => 'switch',
					'label' => $this->l('Send welcome email'),
					'name' => 'PCHIMP_SEND_WELCOME_EMAIL',
					'is_bool' => true,
					'values' => array(
						array(
							'id' => 'sendEmail_on',
							'value' => 1,
							'label' => $this->l('Yes')
						),
						array(
							'id' => 'sendEmail_off',
							'value' => 0,
							'label' => $this->l('No')
						)
					),
				),
				array(
					'type' => 'switch',
					'label' => $this->l('Send voucher'),
					'name' => 'PCHIMP_SEND_VOUCHER',
					'is_bool' => true,
					'values' => array(
						array(
							'id' => 'sendVoucher_on',
							'value' => 1,
							'label' => $this->l('Yes')
						),
						array(
							'id' => 'sendVoucher_off',
							'value' => 0,
							'label' => $this->l('No')
						)
					),
				),
	            array(
	                'type' => 'text',
	                'class' => 'hidden',
	                'label' => '<span class="hidden">' .$this->l('Title') . '</span>',
	                'name' => 'PCHIMP_VOUCHERTITLE',
	                'size' => 20
	            ),
				array(
					'type' => 'switch',
					'label' => $this->l('Discount Type in %?'),
					'name' => 'PCHIMP_DISCOUNTTYPE',
					'is_bool' => true,
					'values' => array(
						array(
							'id' => 'disc_on',
							'value' => 1,
							'label' => $this->l('Yes')
						),
						array(
							'id' => 'disc_off',
							'value' => 0,
							'label' => $this->l('No')
						)
					),
				),
				array(
					'type' => 'text',
					'class' => 'hidden',
					'label' => '<span class="hidden">' .$this->l('Discount value') . '</span>',
					'name' => 'PCHIMP_DISCOUNTVALUE'
				),			
	            array(
	                'type' => 'text',
	                'class' => 'hidden',
	                'label' => '<span class="hidden">' .$this->l('Minimum purchase (set 0 if none)') . '</span>',
	                'name' => 'PCHIMP_MIN_PURCHASE',
	                'size' => 20
	            ),
	            array(
	                'type' => 'textarea',
	                'label' => $this->l('Email Content'),
	                'name' => 'PCHIMP_EMAIL_CONTENT'
	            ),	            
	        ),
	        'submit' => array(
	            'title' => $this->l('Save'),
	            'class' => 'button'
	        )
	    );
	     
	    $helper = new HelperForm();
	     
	    // Module, token and currentIndex
	    $helper->module = $this;
	    $helper->name_controller = $this->name;
	    $helper->token = Tools::getAdminTokenLite('AdminModules');
	    $helper->currentIndex = AdminController::$currentIndex.'&configure='.$this->name;
	     
	    // Language
	    $helper->default_form_language = $default_lang;
	    $helper->allow_employee_form_lang = $default_lang;
	     
	    // Title and toolbar
	    $helper->title = $this->displayName;
	    $helper->show_toolbar = true;        // false -> remove toolbar
	    $helper->toolbar_scroll = true;      // yes - > Toolbar is always visible on the top of the screen.
	    $helper->submit_action = 'submit'.$this->name;
	    $helper->toolbar_btn = array(
	        'save' =>
	        array(
	            'desc' => $this->l('Save'),
	            'href' => AdminController::$currentIndex.'&configure='.$this->name.'&save'.$this->name.
	            '&token='.Tools::getAdminTokenLite('AdminModules'),
	        ),
	        'back' => array(
	            'href' => AdminController::$currentIndex.'&token='.Tools::getAdminTokenLite('AdminModules'),
	            'desc' => $this->l('Back to list')
	        )
	    );
	     
	    // Load current value
	    $helper->fields_value['PCHIMP_KEY'] 					= Configuration::get('PCHIMP_KEY');
	    $helper->fields_value['PCHIMP_LIST_ID'] 				= Configuration::get('PCHIMP_LIST_ID');
	    $helper->fields_value['PCHIMP_DOUBLE_OPTIN'] 			= Configuration::get('PCHIMP_DOUBLE_OPTIN');
	    $helper->fields_value['PCHIMP_SEND_WELCOME_EMAIL'] 		= Configuration::get('PCHIMP_SEND_WELCOME_EMAIL');
	    $helper->fields_value['PCHIMP_SEND_VOUCHER'] 			= Configuration::get('PCHIMP_SEND_VOUCHER');
	    $helper->fields_value['PCHIMP_VOUCHERTITLE'] 			= Configuration::get('PCHIMP_VOUCHERTITLE');
	    $helper->fields_value['PCHIMP_DISCOUNTTYPE'] 			= Configuration::get('PCHIMP_DISCOUNTTYPE');
	    $helper->fields_value['PCHIMP_DISCOUNTVALUE'] 			= Configuration::get('PCHIMP_DISCOUNTVALUE');
	    $helper->fields_value['PCHIMP_MIN_PURCHASE'] 			= Configuration::get('PCHIMP_MIN_PURCHASE');
	    $helper->fields_value['PCHIMP_EMAIL_CONTENT'] 			= Configuration::get('PCHIMP_EMAIL_CONTENT');
	     
	    return $output.$helper->generateForm($fields_form);
	}

	public function hookMyPrestaChimp()
	{
		$this->context->smarty->assign(array(
			'mailchimp'		=> $this->context->link->getModuleLink('prestachimp', 'process')
		));

		return $this->display(__FILE__, 'my-mailchimp.tpl');
	}

	public function hookFooter()
	{
		$this->context->controller->addJS($this->_path.'prestachimp.js');
		$this->context->controller->addCSS($this->_path.'prestachimp.css', 'screen');

		$this->context->smarty->assign(array(
			'mchimp'		=> $this->context->link->getModuleLink('prestachimp', 'iframe')
		));

		return $this->display(__FILE__, 'my-mailchimp-footer.tpl');
	}

	public function hookActionCustomerAccountAdd($params)
	{
		//WHEN THE CUSTOMER CREATE AN ACCOUNT, CHECK IN THE TEMPORARY TABLE IF THIS CUSTOMER HAS SUBSCRIBE TO OUR MAILING LIST 
		//WHEN HE IS STILL NOT YET A CUSTOMER
		$q = '
				SELECT mc.email, mc.code, mc.type
				FROM '._DB_PREFIX_.$this->pchimp_table.' AS mc
				WHERE mc.email = "'.$_POST['email'].'"';
		
		$result = DB::getInstance()->executeS($q);

		if ($result && Configuration::get('PCHIMP_SEND_VOUCHER')) {

			$email 	= @$result[0]['email'];

			foreach ($params as $param) {
				if ($param->email == $email) {
					$id_customer = $param->id;
				}
			}

			$customer = new Customer($id_customer);

			$this->generateVoucherCode($customer->id, $result[0]['code'], $result[0]['type']);
		}
	}

	public function subscribe( $email )
	{
		/*
		*
		* TEST CASES
		*
		* 1. CUSTOMER NOT YET LISTED IN THE MAILING LIST. (WELCOME VOUCHER IS NOT ACTIVATED)
			- EXPECTED RESULTS:
			1. SUBSCRIBE THE CUSTOMER IN THE MAILING LIST

		* 2. CUSTOMER ALREADY IN THE MAILING LIST. (WELCOME VOUCHER IS NOT ACTIVATED)
			- EXPECTED RESULTS:
			1. DO NOT SUBSCRIBE THE CUSTOMER TO THE LIST

		* 3. CUSTOMER IS: (WELCOME VOUCHER IS ACTIVATED)
				1. NOT YET LISTED IN THE MAILING LIST
				2. NOT AN EXISTING CUSTOMER
					- EXPECTED RESULTS:
					1. 	SUBSCRIBE THE CUSTOMER IN THE MAILING LIST
					2. 	ADD THE EMAIL IN THE TEMPORARY TABLE
					3. 	WHEN THE CUSTOMER SUBSCRIBED, THE SYSTEM WILL DETECT THE EMAIL OF THE CUSTOMER AND WILL
						CREATE THE WELCOME VOUCHER BASED ON THE SETTING OF THE MODULE.
					4. EMAIL THE CUSTOMER ABOUT THEIR SUBSCRIPTION

		* 4. CUSTOMER IS: (WELCOME VOUCHER IS ACTIVATED)
				1. ALREADY IN THE MAILING LIST
				2. ALREADY A CUSTOMER
					- EXPECTED RESULTS:
					1. DO NOT SUBSCRIBE THE CUSTOMER IN THE LIST.
					2. DO NOT CREATE A VOUCHER. ONLY NEW SUBSCRIBER CAN GET THE CODE.

		* 5. CUSTOMER IS: (WELCOME VOUCHER IS ACTIVATED)
				1. NOT YET IN THE MAILING LIST
				2. ALREADY A CUSTOMER
					- EXPECTED RESULTS:
					1. SUBSCRIBE THE USER
					2. CREATE THE WELCOME VOUCHER BASED ON THE SETTING OF THE MODULE.
					3. EMAIL THE CUSTOMER ABOUT THEIR SUBSCRIPTION
		*/

		$MailChimp = new Mailchimp(Configuration::get('PCHIMP_KEY'));

		try {

			$data = array(
						    'id' 	=> Configuration::get('PCHIMP_LIST_ID'),
						    'email' => array(
						        'email' => $email
						      ),
						   	'double_optin' => Configuration::get('PCHIMP_DOUBLE_OPTIN'),
						   	'send_welcome' => Configuration::get('PCHIMP_SEND_WELCOME_EMAIL')
				    	);

			$MailChimp->call('lists/subscribe', $data);

			//CHECK IF THE SEND VOUCHER IS ENABLED OTHERWISE DO A NORMAL SUBSCRIPTION
			if (Configuration::get('PCHIMP_SEND_VOUCHER')) {
				
				//CHECK IF THIS EMAIL IS ALREADY AN EXISTING CUSTOMER
				$user 		= new Customer;

				if ($user->customerExists($email)) {
					
					$customer = $user->getByEmail($email);

					$userName = $customer->firstname .' '. $customer->lastname;

					//CREATE NEW DISCOUNT CODE
					$this->generateVoucherCode($customer->id, $this->vcode, Configuration::get('PCHIMP_DISCOUNTTYPE'));

				}else{

					//CREATE A TEMPORARY CODE FOR THIS USER
					$data = array(
						'email' => $email,
						'code'	=> $this->vcode,
						'type'  => Configuration::get('PCHIMP_DISCOUNTTYPE') // 1 = PERCENTAGE ELSE FIXED
					);

					$userName = "Valued Customer";

					if(!Db::getInstance()->insert('pchimp_subscribers', $data))
						die(Tools::jsonEncode(array('result' => false, 'msg' => 'We have problem inserting data.')));
				}

				//SEND THE WELCOME EMAIL
				$template_vars = array(
					'{email_content}'	=> nl2br(Configuration::get('PCHIMP_EMAIL_CONTENT')),
					'{code}'			=> $this->vcode,
					'{value_discount}' 	=> Configuration::get('PCHIMP_DISCOUNTVALUE'),
					'{link}'			=> '<a href="'.Tools::getShopDomain(true).'">link</a>',
				);

				$this->sendWelcomeEmail($template_vars, $email, $userName);
			}

			$result = array('result' => true, 'msg' => 'Thank you for subscribing to our newsletter!');

			die(Tools::jsonEncode($result));
			
		} catch (Mailchimp_Error $e) {
			
			$result = array('result'=> false, 'msg'=> $e->getMessage());

			die(Tools::jsonEncode($result));
		}
	}

	public function generateVoucherCode($id_customer, $code, $type)
	{
		//CREATE NEW DISCOUNT CODE
		$languages = Language::getLanguages();
		foreach ($languages as $key => $language) {
			$lang[$language['id_lang']] = Configuration::get('PCHIMP_VOUCHERTITLE');
		}

		$coupon 					= new CartRule;
		$coupon->id_customer 		= $id_customer;
		$coupon->code 				= $this->vcode;
		$coupon->name 				= $lang;
		$coupon->description 		= Configuration::get('PCHIMP_VOUCHERTITLE');

		//TYPE 1 IS PERCENTAGE ELSE FIXED
		if ($type == 1) {
			$coupon->reduction_percent 	= Configuration::get('PCHIMP_DISCOUNTVALUE');
			$amount = $coupon->reduction_percent;
		}else{
			$coupon->reduction_amount 	= Configuration::get('PCHIMP_DISCOUNTVALUE');
			$amount = $coupon->reduction_amount;
		}

		$coupon->partial_use 		= 0;
		$coupon->quantity_per_user 	= 1;
		$coupon->quantity 			= 1;
		$coupon->highlight 			= 1;
		$coupon->minimum_amount 	= Configuration::get('PCHIMP_MIN_PURCHASE');
		$coupon->date_from 			= date('Y-m-d');
		$coupon->date_to 			= date('Y-m-d', strtotime('+1month'));
		$coupon->add();

		$newCode 					= array(
											'code' => $coupon->code,
											'value_discount' => $amount,
										);

		return $newCode;
	}

	public function sendWelcomeEmail($template_vars = array(), $to, $to_name)
	{
		if(!Mail::Send(
			$this->context->language->id, 
			'prestachimp', 
			'Thanks for subscribing!', 
			$template_vars, 
			$to,
			$to_name, 
			Configuration::get('PS_SHOP_EMAIL'), 
			Configuration::get('PS_SHOP_NAME'), 
			null, null, _PS_MAIL_DIR_, false, null, Configuration::get('SENDAHBCC')))
		{
			die(Tools::jsonEncode(array('errors' => 'Sorry, there is a problem sending the email.')));	
		}
	}	
}