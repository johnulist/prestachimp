<?php
/*
* 2007-2014 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.5.0
 */

include _PS_ROOT_DIR_ .'/modules/prestachimp/libraries/Mailchimp.php';

class PrestachimpProcessModuleFrontController extends ModuleFrontController
{
	public $ssl = true;
	public $display_column_left = false;

	/**
	 * @see FrontController::initContent()
	 */
	public function initContent()
	{
		parent::initContent();		

		$MailChimp = new Mailchimp(Configuration::get('PCHIMP_KEY'));		

		$result = self::register( Tools::getValue('EMAIL') );		

		if ( !Tools::getValue('ajax') )
		{
			if ($result['result'])
				Tools::redirect('/?action=mc&status=1&msg='.urlencode($result['msg']).'#newsletter-wrap');
			else
				Tools::redirect('/?action=mc&status=0&msg='.urlencode($result['msg']).'#newsletter-wrap');	
		}
		else
			die(Tools::jsonEncode($result));
	}

	public static function register( $email )
	{
		$MailChimp = new Mailchimp(Configuration::get('PCHIMP_KEY'));

		try {

			//CHECK FOR USER/S IN YOUR LIST
			$user = $MailChimp->call('lists/subscribe', array(
			    'id' => Configuration::get('PCHIMP_LIST_ID'),
			    'email' => array(
			        'email' => $email
			      )
			    )
			);			

			$msg = 'Thank you for subscribing. Please check your email to verify your subscription.';
			return array('result'=>true,'msg'=>$msg);

		} catch (Mailchimp_Error $e) {
            if ($e->getMessage()) {
                $error = $e->getMessage();
            } else {
                $error = 'An unknown error occurred';
            }

            if (strlen(trim($email)) == 0)
            	$error = 'Oops! Please check the email the you entered and try again.';

            return array('result'=>false,'msg'=>$error);
		}
	}
}