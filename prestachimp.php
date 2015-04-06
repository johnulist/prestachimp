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

	parent::__construct();

	$this->displayName = $this->l('PrestaChimp Module');
	$this->description = $this->l('PrestaChimp configuration module');

	$this->confirmUninstall = $this->l('Are you sure you want to uninstall?');

	if (!Configuration::get('PRESTACHIMP'))      
	  $this->warning = $this->l('No name provided');
	}

	public function install()
	{
	  return parent::install() &&
	  	Configuration::updateValue('PRESTACHIMP', 'PRESTACHIMP MODULE') &&
	  	$this->registerHook('footer') &&
	  	$this->registerHook('myPrestaChimp');
	}	

	public function uninstall()
	{
	  return parent::uninstall() && 
	  	Configuration::deleteByName('PRESTACHIMP') &&
	  	Configuration::deleteByName('PCHIMP_KEY') &&
	  	Configuration::deleteByName('PCHIMP_LIST_ID');
	}

	public function getContent()
	{
	    $output = null;
	 
	    if (Tools::isSubmit('submit'.$this->name))
	    {
	        $key = strval(Tools::getValue('PCHIMP_KEY'));
	        $listId = strval(Tools::getValue('PCHIMP_LIST_ID'));
	        if (!$key  || empty($key) || !Validate::isGenericName($key))
	            $output .= $this->displayError( $this->l('Invalid Configuration value') );
	        else
	        {
	            Configuration::updateValue('PCHIMP_KEY', $key);
	            Configuration::updateValue('PCHIMP_LIST_ID', $listId);
	            $output .= $this->displayConfirmation($this->l('Settings updated'));
	        }
	    }
	    return $output.$this->displayForm();
	}	

	public function displayForm()
	{
	    // Get default Language
	    $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
	     
	    // Init Fields form array
	    $fields_form[0]['form'] = array(
	        'legend' => array(
	            'title' => $this->l('PrestaChimp Settings'),
	        ),
	        'input' => array(
	            array(
	                'type' => 'text',
	                'label' => $this->l('API KEY'),
	                'name' => 'PCHIMP_KEY',
	                'size' => 20,
	                'required' => true
	            ),
	            array(
	                'type' => 'text',
	                'label' => $this->l('YOUR LIST ID'),
	                'name' => 'PCHIMP_LIST_ID',
	                'size' => 20,
	                'required' => true
	            )	            
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
	    $helper->fields_value['PCHIMP_KEY'] 	= Configuration::get('PCHIMP_KEY');
	    $helper->fields_value['PCHIMP_LIST_ID'] = Configuration::get('PCHIMP_LIST_ID');
	     
	    return $helper->generateForm($fields_form);
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
}